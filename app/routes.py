from flask import Blueprint, render_template, request, redirect, url_for, flash
from flask_login import login_required, current_user
from app import db
from app.models import Patient, Doctor, Appointment, QueueTicket, MedicalRecord, User
from datetime import datetime
from sqlalchemy import func

main = Blueprint('main', __name__)

@main.route('/')
def index():
    """Main landing page with different views based on user authentication"""
    if current_user.is_authenticated:
        # Authenticated users get dashboard view
        return redirect(url_for('admin.dashboard'))
    else:
        # Public landing page with service information
        return render_template('index.html')

@main.route('/dashboard')
@login_required
def dashboard():
    """Redirect to appropriate dashboard based on user role"""
    if current_user.role == 'super_admin' or current_user.role == 'staff':
        return redirect(url_for('admin.dashboard'))
    else:
        return redirect(url_for('patient_portal.dashboard'))

@main.route('/services')
def services():
    """Public services page"""
    return render_template('services.html')

@main.route('/about')
def about():
    """About page"""
    return render_template('about.html')

@main.route('/contact')
def contact():
    """Contact page"""
    return render_template('contact.html')

@main.route('/patients')
@login_required
def patients():
    """Staff view of all patients"""
    if current_user.role not in ['super_admin', 'staff', 'nurse', 'dentist', 'physician']:
        flash('Access denied. Staff access required.', 'error')
        return redirect(url_for('main.index'))
    
    patients = Patient.query.order_by(Patient.last_name).all()
    return render_template('patients/list.html', patients=patients)

@main.route('/patient/<int:id>')
@login_required
def patient_detail(id):
    """View individual patient details"""
    if current_user.role not in ['super_admin', 'staff', 'nurse', 'dentist', 'physician']:
        flash('Access denied. Staff access required.', 'error')
        return redirect(url_for('main.index'))
    
    patient = Patient.query.get_or_404(id)
    medical_records = MedicalRecord.query.filter_by(patient_id=id).order_by(MedicalRecord.visit_date.desc()).all()
    appointments = Appointment.query.filter_by(patient_id=id).order_by(Appointment.appointment_date.desc()).all()
    
    return render_template('patients/detail.html', 
                         patient=patient, 
                         medical_records=medical_records,
                         appointments=appointments)

@main.route('/doctors')
@login_required
def doctors():
    """Staff view of all doctors"""
    if current_user.role not in ['super_admin', 'staff']:
        flash('Access denied. Admin access required.', 'error')
        return redirect(url_for('main.index'))
    
    doctors = Doctor.query.order_by(Doctor.last_name).all()
    return render_template('doctors/list.html', doctors=doctors)

@main.route('/appointments')
@login_required
def appointments():
    """Staff view of appointments"""
    if current_user.role not in ['super_admin', 'staff', 'nurse']:
        flash('Access denied. Staff access required.', 'error')
        return redirect(url_for('main.index'))
    
    appointments = Appointment.query.order_by(Appointment.appointment_date.desc()).all()
    return render_template('appointments/list.html', appointments=appointments)

@main.route('/appointment/<int:id>')
@login_required
def appointment_detail(id):
    """View individual appointment details"""
    if current_user.role not in ['super_admin', 'staff', 'nurse', 'dentist', 'physician']:
        flash('Access denied. Staff access required.', 'error')
        return redirect(url_for('main.index'))
    
    appointment = Appointment.query.get_or_404(id)
    return render_template('appointments/detail.html', appointment=appointment)

@main.route('/appointment/<int:id>/edit')
@login_required
def edit_appointment(id):
    """Edit appointment"""
    if current_user.role not in ['super_admin', 'staff', 'nurse']:
        flash('Access denied. Staff access required.', 'error')
        return redirect(url_for('main.index'))
    
    appointment = Appointment.query.get_or_404(id)
    patients = Patient.query.order_by(Patient.last_name).all()
    doctors = Doctor.query.filter_by(is_active=True).all()
    
    return render_template('appointments/edit.html', 
                         appointment=appointment,
                         patients=patients,
                         doctors=doctors)

@main.route('/staff_management')
@login_required
def staff_management():
    """Admin staff management interface"""
    if current_user.role != 'super_admin':
        flash('Access denied. Super admin access required.', 'error')
        return redirect(url_for('main.index'))
    
    staff_members = User.query.filter(User.role != 'patient').order_by(User.last_name).all()
    return render_template('admin/staff_management.html', staff=staff_members)

@main.route('/queue_status')
def queue_status():
    """Public queue status display"""
    active_tickets = QueueTicket.query.filter_by(status='waiting').order_by(QueueTicket.priority.desc(), QueueTicket.created_at).all()
    return render_template('queue/status.html', tickets=active_tickets)


