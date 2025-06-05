from flask import Blueprint, render_template, request, redirect, url_for, flash, session
from app import db
from app.models import Patient, Appointment, MedicalRecord, QueueTicket, BLOOD_TYPES, CAMPUS_OPTIONS, COLLEGE_OFFICE_OPTIONS
from datetime import datetime
import re

patient_portal = Blueprint('patient_portal', __name__)

def patient_required(f):
    """Decorator to require patient login"""
    from functools import wraps
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if 'patient_id' not in session:
            flash('Please log in to access this page.', 'error')
            return redirect(url_for('auth.login'))
        return f(*args, **kwargs)
    return decorated_function

@patient_portal.route('/register', methods=['GET', 'POST'])
def register():
    """Patient self-registration"""
    if request.method == 'POST':
        # Basic Information
        patient_number = request.form.get('patient_number')
        email = request.form.get('email')
        password = request.form.get('password')
        confirm_password = request.form.get('confirm_password')
        
        # Personal Information
        first_name = request.form.get('first_name')
        middle_name = request.form.get('middle_name')
        last_name = request.form.get('last_name')
        age = request.form.get('age')
        sex = request.form.get('sex')
        date_of_birth = request.form.get('date_of_birth')
        
        # Contact Information
        phone = request.form.get('phone')
        
        # Address
        lot_number = request.form.get('lot_number')
        barangay_subdivision = request.form.get('barangay_subdivision')
        street = request.form.get('street')
        city_municipality = request.form.get('city_municipality')
        province = request.form.get('province')
        
        # Academic/Work Information
        campus = request.form.get('campus')
        college_office = request.form.get('college_office')
        course_designation = request.form.get('course_designation')
        patient_type = request.form.get('patient_type')
        
        # Emergency Contact
        emergency_contact_name = request.form.get('emergency_contact_name')
        emergency_contact_relation = request.form.get('emergency_contact_relation')
        emergency_contact_number = request.form.get('emergency_contact_number')
        
        # Medical Information
        blood_type = request.form.get('blood_type')
        allergies = request.form.get('allergies')
        
        # Validation
        errors = []
        
        if not all([patient_number, email, password, first_name, last_name, age, sex, date_of_birth, phone,
                   city_municipality, province, campus, college_office, course_designation, patient_type,
                   emergency_contact_name, emergency_contact_relation, emergency_contact_number]):
            errors.append('All required fields must be filled.')
        
        if len(password) < 6:
            errors.append('Password must be at least 6 characters long.')
        
        if password != confirm_password:
            errors.append('Passwords do not match.')
        
        if Patient.query.filter_by(patient_number=patient_number).first():
            errors.append('Patient number already exists.')
        
        if Patient.query.filter_by(email=email).first():
            errors.append('Email already exists.')
        
        if not re.match(r'^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$', email):
            errors.append('Invalid email format.')
        
        try:
            age = int(age)
            if age < 0 or age > 150:
                errors.append('Invalid age.')
        except ValueError:
            errors.append('Age must be a number.')
        
        try:
            date_of_birth = datetime.strptime(date_of_birth, '%Y-%m-%d').date()
        except ValueError:
            errors.append('Invalid date of birth format.')
        
        if errors:
            for error in errors:
                flash(error, 'error')
            return render_template('patient_portal/register.html',
                                 blood_types=BLOOD_TYPES,
                                 campus_options=CAMPUS_OPTIONS,
                                 college_office_options=COLLEGE_OFFICE_OPTIONS)
        
        # Create new patient
        patient = Patient(
            patient_number=patient_number,
            email=email,
            first_name=first_name,
            middle_name=middle_name,
            last_name=last_name,
            age=age,
            sex=sex,
            date_of_birth=date_of_birth,
            phone=phone,
            lot_number=lot_number,
            barangay_subdivision=barangay_subdivision,
            street=street,
            city_municipality=city_municipality,
            province=province,
            campus=campus,
            college_office=college_office,
            course_designation=course_designation,
            patient_type=patient_type,
            emergency_contact_name=emergency_contact_name,
            emergency_contact_relation=emergency_contact_relation,
            emergency_contact_number=emergency_contact_number,
            blood_type=blood_type,
            allergies=allergies
        )
        patient.set_password(password)
        
        db.session.add(patient)
        db.session.commit()
        
        flash('Registration successful! You can now log in.', 'success')
        return redirect(url_for('auth.login'))
    
    return render_template('patient_portal/register.html',
                         blood_types=BLOOD_TYPES,
                         campus_options=CAMPUS_OPTIONS,
                         college_office_options=COLLEGE_OFFICE_OPTIONS)

@patient_portal.route('/dashboard')
@patient_required
def dashboard():
    """Patient dashboard"""
    patient = Patient.query.get(session['patient_id'])
    
    # Get recent appointments
    recent_appointments = Appointment.query.filter_by(patient_id=patient.id).order_by(
        Appointment.appointment_date.desc()
    ).limit(5).all()
    
    # Get recent medical records
    recent_records = MedicalRecord.query.filter_by(patient_id=patient.id).order_by(
        MedicalRecord.date_time.desc()
    ).limit(5).all()
    
    # Get current queue tickets
    current_tickets = QueueTicket.query.filter_by(
        patient_id=patient.id,
        status='waiting'
    ).all()
    
    return render_template('patient_portal/dashboard.html',
                         patient=patient,
                         recent_appointments=recent_appointments,
                         recent_records=recent_records,
                         current_tickets=current_tickets)

@patient_portal.route('/appointments')
@patient_required
def appointments():
    """View patient appointments"""
    patient = Patient.query.get(session['patient_id'])
    appointments = Appointment.query.filter_by(patient_id=patient.id).order_by(
        Appointment.appointment_date.desc()
    ).all()
    
    return render_template('patient_portal/appointments.html',
                         patient=patient,
                         appointments=appointments)

@patient_portal.route('/medical_records')
@patient_required
def medical_records():
    """View patient medical records"""
    patient = Patient.query.get(session['patient_id'])
    records = MedicalRecord.query.filter_by(patient_id=patient.id).order_by(
        MedicalRecord.date_time.desc()
    ).all()
    
    return render_template('patient_portal/medical_records.html',
                         patient=patient,
                         records=records)

@patient_portal.route('/profile')
@patient_required
def profile():
    """View patient profile"""
    patient = Patient.query.get(session['patient_id'])
    return render_template('patient_portal/profile.html', patient=patient)

@patient_portal.route('/edit_profile', methods=['GET', 'POST'])
@patient_required
def edit_profile():
    """Edit patient profile"""
    patient = Patient.query.get(session['patient_id'])
    
    if request.method == 'POST':
        # Update allowed fields
        patient.phone = request.form.get('phone')
        patient.lot_number = request.form.get('lot_number')
        patient.barangay_subdivision = request.form.get('barangay_subdivision')
        patient.street = request.form.get('street')
        patient.city_municipality = request.form.get('city_municipality')
        patient.province = request.form.get('province')
        patient.emergency_contact_name = request.form.get('emergency_contact_name')
        patient.emergency_contact_relation = request.form.get('emergency_contact_relation')
        patient.emergency_contact_number = request.form.get('emergency_contact_number')
        patient.allergies = request.form.get('allergies')
        
        db.session.commit()
        flash('Profile updated successfully.', 'success')
        return redirect(url_for('patient_portal.profile'))
    
    return render_template('patient_portal/edit_profile.html',
                         patient=patient,
                         blood_types=BLOOD_TYPES,
                         campus_options=CAMPUS_OPTIONS,
                         college_office_options=COLLEGE_OFFICE_OPTIONS)
