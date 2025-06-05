from flask import Blueprint, render_template, request, redirect, url_for, flash, jsonify
from app import db
from app.models import QueueTicket, Patient, DIAGNOSIS_OPTIONS
from datetime import datetime
import uuid

queue = Blueprint('queue', __name__)

@queue.route('/kiosk')
def kiosk():
    """Main kiosk interface for patients to get queue tickets"""
    return render_template('queue/kiosk.html')

@queue.route('/get_ticket', methods=['POST'])
def get_ticket():
    """Generate queue ticket for patient"""
    data = request.get_json()
    ticket_type = data.get('ticket_type')  # appointment or walk_in
    transaction_type = data.get('transaction_type')
    patient_number = data.get('patient_number')
    walk_in_name = data.get('walk_in_name')
    walk_in_type = data.get('walk_in_type')
    
    # Generate queue number
    today = datetime.now().strftime('%Y%m%d')
    count = QueueTicket.query.filter(
        QueueTicket.created_at >= datetime.now().replace(hour=0, minute=0, second=0)
    ).count() + 1
    queue_number = f"{today}-{count:03d}"
    
    # Determine priority
    priority_level = 4  # Regular
    priority_type = 'regular'
    
    if ticket_type == 'walk_in':
        if walk_in_type == 'faculty':
            priority_level = 1
            priority_type = 'faculty'
        elif walk_in_type == 'personnel':
            priority_level = 2
            priority_type = 'personnel'
    else:
        # Check if registered patient has priority
        if patient_number:
            patient = Patient.query.filter_by(patient_number=patient_number).first()
            if patient:
                if patient.patient_type == 'faculty':
                    priority_level = 1
                    priority_type = 'faculty'
                elif patient.patient_type == 'non_academic':
                    priority_level = 2
                    priority_type = 'personnel'
    
    # Create queue ticket
    ticket = QueueTicket(
        patient_id=None if ticket_type == 'walk_in' else Patient.query.filter_by(patient_number=patient_number).first().id if patient_number else None,
        queue_number=queue_number,
        ticket_type=ticket_type,
        transaction_type=transaction_type,
        priority_level=priority_level,
        priority_type=priority_type,
        walk_in_name=walk_in_name,
        walk_in_type=walk_in_type
    )
    
    db.session.add(ticket)
    db.session.commit()
    
    return jsonify({
        'success': True,
        'queue_number': queue_number,
        'ticket_id': ticket.id,
        'priority_type': priority_type,
        'estimated_wait': calculate_estimated_wait(priority_level)
    })

@queue.route('/display')
def display():
    """Queue display for clinic screens"""
    # Get current queue by priority and transaction type
    current_queue = QueueTicket.query.filter_by(status='waiting').order_by(
        QueueTicket.priority_level.asc(),
        QueueTicket.created_at.asc()
    ).all()
    
    now_serving = QueueTicket.query.filter_by(status='serving').all()
    
    return render_template('queue/display.html', 
                         current_queue=current_queue, 
                         now_serving=now_serving)

@queue.route('/management')
def management():
    """Staff interface for queue management"""
    windows = [1, 2, 3, 4]  # Number of service windows
    
    # Get queue by window
    queue_by_window = {}
    for window in windows:
        queue_by_window[window] = QueueTicket.query.filter_by(
            window_number=window, 
            status='waiting'
        ).order_by(
            QueueTicket.priority_level.asc(),
            QueueTicket.created_at.asc()
        ).all()
    
    return render_template('queue/management.html', 
                         windows=windows, 
                         queue_by_window=queue_by_window)

@queue.route('/call_next/<int:window_number>')
def call_next(window_number):
    """Call next patient in queue for specific window"""
    # Get next ticket by priority
    next_ticket = QueueTicket.query.filter_by(status='waiting').order_by(
        QueueTicket.priority_level.asc(),
        QueueTicket.created_at.asc()
    ).first()
    
    if next_ticket:
        next_ticket.status = 'serving'
        next_ticket.window_number = window_number
        next_ticket.called_at = datetime.now()
        db.session.commit()
        
        flash(f'Called {next_ticket.queue_number} to window {window_number}', 'success')
    else:
        flash('No patients in queue', 'info')
    
    return redirect(url_for('queue.management'))

@queue.route('/complete_service/<int:ticket_id>')
def complete_service(ticket_id):
    """Mark service as completed"""
    ticket = QueueTicket.query.get_or_404(ticket_id)
    ticket.status = 'completed'
    ticket.completed_at = datetime.now()
    db.session.commit()
    
    flash(f'Service completed for {ticket.queue_number}', 'success')
    return redirect(url_for('queue.management'))

@queue.route('/print_ticket/<int:ticket_id>')
def print_ticket(ticket_id):
    """Generate printable ticket"""
    ticket = QueueTicket.query.get_or_404(ticket_id)
    return render_template('queue/print_ticket.html', ticket=ticket)

def calculate_estimated_wait(priority_level):
    """Calculate estimated wait time based on priority and current queue"""
    base_wait = 5  # 5 minutes base wait time
    queue_ahead = QueueTicket.query.filter(
        QueueTicket.status == 'waiting',
        QueueTicket.priority_level <= priority_level
    ).count()
    
    return base_wait * queue_ahead
