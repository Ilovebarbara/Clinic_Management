from flask import Blueprint, jsonify, request, session
from flask_login import login_required, current_user
from app import db
from app.models import Patient, Appointment, QueueTicket, User, MedicalRecord
from datetime import datetime, date, timedelta
from sqlalchemy import func, and_

api = Blueprint('api', __name__)

@api.route('/dashboard-stats')
@login_required
def dashboard_stats():
    """Get real-time dashboard statistics"""
    try:
        # Total patients
        total_patients = Patient.query.count()
        
        # Today's appointments
        today = date.today()
        todays_appointments = Appointment.query.filter(
            func.date(Appointment.appointment_date) == today
        ).count()
        
        # Current queue length
        queue_length = QueueTicket.query.filter(
            QueueTicket.status.in_(['waiting', 'called'])
        ).count()
        
        # Active staff (users who logged in today)
        active_staff = User.query.filter(
            and_(
                User.role.in_(['doctor', 'nurse', 'staff']),
                func.date(User.last_login) == today
            )
        ).count()
        
        return jsonify({
            'success': True,
            'total_patients': total_patients,
            'todays_appointments': todays_appointments,
            'queue_length': queue_length,
            'active_staff': active_staff
        })
        
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

@api.route('/notifications')
@login_required
def get_notifications():
    """Get pending notifications for the user"""
    try:
        notifications = []
        
        # Check for overdue appointments (if user is staff)
        if current_user.role in ['doctor', 'nurse', 'admin', 'super_admin']:
            overdue_appointments = Appointment.query.filter(
                and_(
                    Appointment.appointment_date < datetime.now(),
                    Appointment.status == 'scheduled'
                )
            ).count()
            
            if overdue_appointments > 0:
                notifications.append({
                    'type': 'warning',
                    'message': f'{overdue_appointments} overdue appointments need attention'
                })
        
        # Check for emergency queue tickets
        emergency_tickets = QueueTicket.query.filter(
            and_(
                QueueTicket.priority == 'emergency',
                QueueTicket.status == 'waiting'
            )
        ).count()
        
        if emergency_tickets > 0:
            notifications.append({
                'type': 'danger',
                'message': f'{emergency_tickets} emergency patients waiting'
            })
        
        # Check for long waiting times
        long_wait_tickets = QueueTicket.query.filter(
            and_(
                QueueTicket.status == 'waiting',
                QueueTicket.created_at < datetime.now() - timedelta(hours=2)
            )
        ).count()
        
        if long_wait_tickets > 0:
            notifications.append({
                'type': 'warning',
                'message': f'{long_wait_tickets} patients waiting over 2 hours'
            })
        
        return jsonify({
            'success': True,
            'notifications': notifications
        })
        
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

@api.route('/queue/current-status')
def queue_current_status():
    """Get current queue status - public endpoint"""
    try:
        # Current ticket being served
        current_ticket = QueueTicket.query.filter_by(status='called').first()
        current_data = None
        if current_ticket:
            current_data = {
                'number': current_ticket.ticket_number,
                'service': current_ticket.service_type,
                'priority': current_ticket.priority,
                'counter': 1  # Default counter
            }
        
        # Waiting tickets
        waiting_tickets = QueueTicket.query.filter_by(status='waiting').order_by(
            QueueTicket.priority_order(), QueueTicket.created_at
        ).all()
        
        waiting_data = []
        for ticket in waiting_tickets:
            wait_time = datetime.now() - ticket.created_at
            wait_minutes = int(wait_time.total_seconds() / 60)
            
            waiting_data.append({
                'number': ticket.ticket_number,
                'service': ticket.service_type,
                'priority': ticket.priority,
                'wait_time': f'{wait_minutes} min'
            })
        
        # Queue statistics
        stats = {
            'total_waiting': len(waiting_data),
            'average_wait': calculate_average_wait_time(),
            'served_today': QueueTicket.query.filter(
                and_(
                    func.date(QueueTicket.created_at) == date.today(),
                    QueueTicket.status == 'completed'
                )
            ).count()
        }
        
        return jsonify({
            'success': True,
            'current': current_data,
            'waiting': waiting_data,
            'stats': stats
        })
        
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

@api.route('/queue/generate-ticket', methods=['POST'])
def generate_ticket():
    """Generate a new queue ticket"""
    try:
        data = request.get_json()
        
        # Generate ticket number
        today_tickets = QueueTicket.query.filter(
            func.date(QueueTicket.created_at) == date.today()
        ).count()
        
        ticket_number = f"{date.today().strftime('%Y%m%d')}-{today_tickets + 1:03d}"
        
        # Create new ticket
        ticket = QueueTicket(
            ticket_number=ticket_number,
            service_type=data['service'],
            priority=data['priority'],
            status='waiting',
            created_at=datetime.now()
        )
        
        db.session.add(ticket)
        db.session.commit()
        
        return jsonify({
            'success': True,
            'ticket': {
                'number': ticket_number,
                'service': data['service'],
                'priority': data['priority'],
                'timestamp': ticket.created_at.isoformat(),
                'auto_print': True
            }
        })
        
    except Exception as e:
        db.session.rollback()
        return jsonify({
            'success': False,
            'message': str(e)
        }), 500

@api.route('/queue/call-next', methods=['POST'])
@login_required
def call_next_patient():
    """Call the next patient in queue"""
    try:
        # Complete current patient if any
        current_ticket = QueueTicket.query.filter_by(status='called').first()
        if current_ticket:
            current_ticket.status = 'completed'
            current_ticket.completed_at = datetime.now()
        
        # Get next patient by priority
        next_ticket = QueueTicket.query.filter_by(status='waiting').order_by(
            QueueTicket.priority_order(), QueueTicket.created_at
        ).first()
        
        if next_ticket:
            next_ticket.status = 'called'
            next_ticket.called_at = datetime.now()
            db.session.commit()
            
            return jsonify({
                'success': True,
                'ticket': {
                    'number': next_ticket.ticket_number,
                    'service': next_ticket.service_type,
                    'priority': next_ticket.priority
                }
            })
        else:
            return jsonify({
                'success': False,
                'message': 'No patients in queue'
            })
            
    except Exception as e:
        db.session.rollback()
        return jsonify({
            'success': False,
            'message': str(e)
        }), 500

@api.route('/queue/complete-current', methods=['POST'])
@login_required
def complete_current_patient():
    """Mark current patient as completed"""
    try:
        current_ticket = QueueTicket.query.filter_by(status='called').first()
        
        if current_ticket:
            current_ticket.status = 'completed'
            current_ticket.completed_at = datetime.now()
            db.session.commit()
            
            return jsonify({
                'success': True,
                'message': 'Patient consultation completed'
            })
        else:
            return jsonify({
                'success': False,
                'message': 'No current patient'
            })
            
    except Exception as e:
        db.session.rollback()
        return jsonify({
            'success': False,
            'message': str(e)
        }), 500

@api.route('/queue/skip-current', methods=['POST'])
@login_required
def skip_current_patient():
    """Skip current patient and move to waiting"""
    try:
        current_ticket = QueueTicket.query.filter_by(status='called').first()
        
        if current_ticket:
            current_ticket.status = 'waiting'
            current_ticket.called_at = None
            db.session.commit()
            
            return jsonify({
                'success': True,
                'message': 'Patient skipped'
            })
        else:
            return jsonify({
                'success': False,
                'message': 'No current patient'
            })
            
    except Exception as e:
        db.session.rollback()
        return jsonify({
            'success': False,
            'message': str(e)
        }), 500

@api.route('/search')
def search_api():
    """API endpoint for search functionality"""
    try:
        query = request.args.get('q', '').strip()
        search_type = request.args.get('type', 'all')
        
        if not query:
            return jsonify({
                'success': False,
                'message': 'Search query is required'
            })
        
        results = {
            'patients': [],
            'doctors': [],
            'appointments': [],
            'medical_records': []
        }
        
        # Search patients
        if search_type in ['all', 'patients']:
            patients = Patient.query.filter(
                db.or_(
                    Patient.first_name.contains(query),
                    Patient.last_name.contains(query),
                    Patient.email.contains(query),
                    Patient.patient_number.contains(query)
                )
            ).limit(10).all()
            
            results['patients'] = [{
                'id': p.id,
                'name': f"{p.first_name} {p.last_name}",
                'email': p.email,
                'patient_number': p.patient_number
            } for p in patients]
        
        # Search doctors
        if search_type in ['all', 'doctors']:
            doctors = User.query.filter(
                and_(
                    User.role == 'doctor',
                    db.or_(
                        User.first_name.contains(query),
                        User.last_name.contains(query),
                        User.specialization.contains(query)
                    )
                )
            ).limit(10).all()
            
            results['doctors'] = [{
                'id': d.id,
                'name': f"Dr. {d.first_name} {d.last_name}",
                'specialization': d.specialization,
                'email': d.email
            } for d in doctors]
        
        return jsonify({
            'success': True,
            'results': results,
            'total': sum(len(v) for v in results.values())
        })
        
    except Exception as e:
        return jsonify({
            'success': False,
            'message': str(e)
        }), 500

@api.route('/queue/status')
def queue_status():
    """Get current queue status for real-time updates"""
    today = date.today()
    
    # Get all tickets for today grouped by status
    waiting_tickets = QueueTicket.query.filter(
        QueueTicket.created_at >= today,
        QueueTicket.status == 'waiting'
    ).order_by(QueueTicket.priority_level.asc(), QueueTicket.created_at.asc()).all()
    
    serving_tickets = QueueTicket.query.filter(
        QueueTicket.created_at >= today,
        QueueTicket.status == 'serving'
    ).all()
    
    completed_tickets = QueueTicket.query.filter(
        QueueTicket.created_at >= today,
        QueueTicket.status == 'completed'
    ).count()
    
    # Format response
    queue_data = {
        'waiting': [{
            'id': ticket.id,
            'queue_number': ticket.queue_number,
            'patient_name': ticket.get_patient_name(),
            'transaction_type': ticket.transaction_type,
            'priority_level': ticket.priority_level,
            'priority_type': ticket.priority_type,
            'created_at': ticket.created_at.strftime('%H:%M'),
            'window_number': ticket.window_number
        } for ticket in waiting_tickets],
        'serving': [{
            'id': ticket.id,
            'queue_number': ticket.queue_number,
            'patient_name': ticket.get_patient_name(),
            'transaction_type': ticket.transaction_type,
            'window_number': ticket.window_number,
            'called_at': ticket.called_at.strftime('%H:%M') if ticket.called_at else None
        } for ticket in serving_tickets],
        'completed_count': completed_tickets,
        'total_waiting': len(waiting_tickets),
        'last_updated': datetime.now().strftime('%H:%M:%S')
    }
    
    return jsonify(queue_data)

@api.route('/queue/call_next')
def call_next():
    """Call the next patient in queue"""
    window_number = request.args.get('window', type=int, default=1)
    
    # Get the next ticket to call
    next_ticket = QueueTicket.query.filter(
        QueueTicket.status == 'waiting'
    ).order_by(
        QueueTicket.priority_level.asc(), 
        QueueTicket.created_at.asc()
    ).first()
    
    if next_ticket:
        next_ticket.status = 'serving'
        next_ticket.window_number = window_number
        next_ticket.called_at = datetime.now()
        db.session.commit()
        
        return jsonify({
            'success': True,
            'ticket': {
                'id': next_ticket.id,
                'queue_number': next_ticket.queue_number,
                'patient_name': next_ticket.get_patient_name(),
                'window_number': window_number
            }
        })
    
    return jsonify({
        'success': False,
        'message': 'No patients waiting in queue'
    })

@api.route('/queue/complete/<int:ticket_id>')
def complete_ticket(ticket_id):
    """Mark a ticket as completed"""
    ticket = QueueTicket.query.get_or_404(ticket_id)
    ticket.status = 'completed'
    ticket.completed_at = datetime.now()
    db.session.commit()
    
    return jsonify({
        'success': True,
        'message': f'Ticket {ticket.queue_number} marked as completed'
    })

def calculate_average_wait_time():
    """Calculate average wait time for completed tickets today"""
    from datetime import timedelta
    
    completed_today = QueueTicket.query.filter(
        and_(
            func.date(QueueTicket.created_at) == date.today(),
            QueueTicket.status == 'completed',
            QueueTicket.completed_at.isnot(None)
        )
    ).all()
    
    if not completed_today:
        return "0 min"
    
    total_wait = sum(
        (ticket.completed_at - ticket.created_at).total_seconds() 
        for ticket in completed_today
    )
    
    average_seconds = total_wait / len(completed_today)
    average_minutes = int(average_seconds / 60)
    
    return f"{average_minutes} min"
