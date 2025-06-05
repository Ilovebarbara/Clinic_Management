from flask import Blueprint, render_template, request, redirect, url_for, flash, jsonify
from flask_login import login_required, current_user
from app import db
from app.models import User, Patient, Doctor, Appointment, MedicalRecord, QueueTicket
from datetime import datetime, timedelta
from sqlalchemy import func

admin = Blueprint('admin', __name__)

@admin.route('/dashboard')
@login_required
def dashboard():
    if current_user.role not in ['super_admin', 'admin']:
        flash('Access denied.', 'error')
        return redirect(url_for('main.dashboard'))
    
    # Analytics data
    total_users = User.query.count()
    total_patients = Patient.query.count()
    total_doctors = Doctor.query.count()
    today_appointments = Appointment.query.filter(
        func.date(Appointment.appointment_date) == datetime.now().date()
    ).count()
    
    # Recent activities
    recent_patients = Patient.query.order_by(Patient.created_at.desc()).limit(5).all()
    recent_appointments = Appointment.query.order_by(Appointment.created_at.desc()).limit(5).all()
    
    return render_template('admin/dashboard.html',
                         total_users=total_users,
                         total_patients=total_patients,
                         total_doctors=total_doctors,
                         today_appointments=today_appointments,
                         recent_patients=recent_patients,
                         recent_appointments=recent_appointments)

@admin.route('/manage_staff')
@login_required
def manage_staff():
    if current_user.role != 'super_admin':
        flash('Access denied.', 'error')
        return redirect(url_for('main.dashboard'))
    
    staff_members = User.query.filter(User.role != 'super_admin').all()
    return render_template('admin/manage_staff.html', staff_members=staff_members)

@admin.route('/analytics')
@login_required
def analytics():
    if current_user.role not in ['super_admin', 'admin']:
        flash('Access denied.', 'error')
        return redirect(url_for('main.dashboard'))
    
    # Patient demographics
    patient_by_campus = db.session.query(
        Patient.campus, func.count(Patient.id)
    ).group_by(Patient.campus).all()
    
    patient_by_type = db.session.query(
        Patient.patient_type, func.count(Patient.id)
    ).group_by(Patient.patient_type).all()
    
    # Appointment statistics
    today = datetime.now().date()
    week_ago = today - timedelta(days=7)
    
    appointments_this_week = Appointment.query.filter(
        func.date(Appointment.appointment_date) >= week_ago
    ).count()
    
    return render_template('admin/analytics.html',
                         patient_by_campus=patient_by_campus,
                         patient_by_type=patient_by_type,
                         appointments_this_week=appointments_this_week)

@admin.route('/system_settings')
@login_required
def system_settings():
    if current_user.role != 'super_admin':
        flash('Access denied.', 'error')
        return redirect(url_for('main.dashboard'))
    
    return render_template('admin/system_settings.html')
