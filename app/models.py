from app import db
from datetime import datetime
from flask_login import UserMixin
from werkzeug.security import generate_password_hash, check_password_hash
import os

class User(UserMixin, db.Model):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(64), unique=True, nullable=False, index=True)
    email = db.Column(db.String(120), unique=True, nullable=False, index=True)
    password_hash = db.Column(db.String(255), nullable=False)
    first_name = db.Column(db.String(50), nullable=False)
    last_name = db.Column(db.String(50), nullable=False)
    role = db.Column(db.String(20), nullable=False, default='staff')  # super_admin, staff, nurse, dentist, physician
    phone = db.Column(db.String(20), nullable=True)
    employee_id = db.Column(db.String(20), unique=True, nullable=True)
    profile_picture = db.Column(db.String(255), nullable=True)
    is_active = db.Column(db.Boolean, default=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    created_by_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=True)
    
    # Relationships
    created_by = db.relationship('User', remote_side=[id], backref='created_users')
    medical_records = db.relationship('MedicalRecord', backref='attending_staff', lazy=True)
    appointments_attended = db.relationship('Appointment', backref='attending_staff', lazy=True)
    
    def set_password(self, password):
        self.password_hash = generate_password_hash(password)
    
    def check_password(self, password):
        return check_password_hash(self.password_hash, password)
    
    def get_full_name(self):
        return f"{self.first_name} {self.last_name}"
    
    def __repr__(self):
        return f'<User {self.username}>'

class Patient(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    patient_number = db.Column(db.String(20), unique=True, nullable=False, index=True)  # Student/Employee no.
    email = db.Column(db.String(120), unique=True, nullable=False)
    password_hash = db.Column(db.String(255), nullable=True)  # For patient login
    
    # Personal Information
    first_name = db.Column(db.String(50), nullable=False)
    middle_name = db.Column(db.String(50), nullable=True)
    last_name = db.Column(db.String(50), nullable=False)
    age = db.Column(db.Integer, nullable=False)
    sex = db.Column(db.String(10), nullable=False)
    date_of_birth = db.Column(db.Date, nullable=False)
    
    # Contact Information
    phone = db.Column(db.String(20), nullable=False)
    
    # Address
    lot_number = db.Column(db.String(20), nullable=True)
    barangay_subdivision = db.Column(db.String(100), nullable=True)
    street = db.Column(db.String(100), nullable=True)
    city_municipality = db.Column(db.String(100), nullable=False)
    province = db.Column(db.String(100), nullable=False)
    
    # Academic/Work Information
    campus = db.Column(db.String(50), nullable=False)  # Malolos, Meneses, Hagonoy, etc.
    college_office = db.Column(db.String(100), nullable=False)  # CICT, CON, COE, HR, Accounting, etc.
    course_designation = db.Column(db.String(100), nullable=False)  # Course & Year/Designation
    patient_type = db.Column(db.String(20), nullable=False)  # student, faculty, non_academic
    
    # Emergency Contact
    emergency_contact_name = db.Column(db.String(100), nullable=False)
    emergency_contact_relation = db.Column(db.String(50), nullable=False)
    emergency_contact_number = db.Column(db.String(20), nullable=False)
    
    # Medical Information
    blood_type = db.Column(db.String(5), nullable=True)
    allergies = db.Column(db.Text, nullable=True)
    
    # System fields
    is_active = db.Column(db.Boolean, default=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    created_by_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=True)
    
    # Relationships
    appointments = db.relationship('Appointment', backref='patient', lazy=True)
    medical_records = db.relationship('MedicalRecord', backref='patient', lazy=True)
    queue_tickets = db.relationship('QueueTicket', backref='patient', lazy=True)
    created_by = db.relationship('User', backref='patients_created')
    
    def set_password(self, password):
        self.password_hash = generate_password_hash(password)
    
    def check_password(self, password):
        if self.password_hash:
            return check_password_hash(self.password_hash, password)
        return False
    
    def get_full_name(self):
        if self.middle_name:
            return f"{self.first_name} {self.middle_name} {self.last_name}"
        return f"{self.first_name} {self.last_name}"
    
    def get_full_address(self):
        address_parts = []
        if self.lot_number:
            address_parts.append(self.lot_number)
        if self.barangay_subdivision:
            address_parts.append(self.barangay_subdivision)
        if self.street:
            address_parts.append(self.street)
        address_parts.extend([self.city_municipality, self.province])
        return ", ".join(address_parts)

class Doctor(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=True)  # Link to User account
    first_name = db.Column(db.String(50), nullable=False)
    last_name = db.Column(db.String(50), nullable=False)
    specialization = db.Column(db.String(100), nullable=False)
    email = db.Column(db.String(100), unique=True, nullable=False)
    phone = db.Column(db.String(20), nullable=False)
    license_number = db.Column(db.String(50), nullable=True)
    is_active = db.Column(db.Boolean, default=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    # Relationships
    user = db.relationship('User', backref='doctor_profile')
    appointments = db.relationship('Appointment', backref='doctor', lazy=True)
    medical_records = db.relationship('MedicalRecord', backref='doctor', lazy=True)

class Appointment(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    patient_id = db.Column(db.Integer, db.ForeignKey('patient.id'), nullable=False)
    doctor_id = db.Column(db.Integer, db.ForeignKey('doctor.id'), nullable=False)
    attending_staff_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=True)
    
    appointment_date = db.Column(db.DateTime, nullable=False)
    appointment_type = db.Column(db.String(50), nullable=False)  # consultation, follow_up, etc.
    status = db.Column(db.String(20), default='scheduled')  # scheduled, completed, cancelled, no_show
    notes = db.Column(db.Text, nullable=True)
    
    # Queue management
    queue_number = db.Column(db.String(20), nullable=True)
    window_number = db.Column(db.Integer, nullable=True)
    
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

class MedicalRecord(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    patient_id = db.Column(db.Integer, db.ForeignKey('patient.id'), nullable=False)
    doctor_id = db.Column(db.Integer, db.ForeignKey('doctor.id'), nullable=True)
    attending_staff_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    
    # Basic Information
    transaction_type = db.Column(db.String(50), nullable=False)  # consultation, certificate, etc.
    date_time = db.Column(db.DateTime, default=datetime.utcnow)
    transaction_details = db.Column(db.Text, nullable=True)
    
    # Consultation specific fields
    height = db.Column(db.Float, nullable=True)  # in cm
    weight = db.Column(db.Float, nullable=True)  # in kg
    hr = db.Column(db.Integer, nullable=True)  # Heart Rate
    rr = db.Column(db.Integer, nullable=True)  # Respiratory Rate
    temperature = db.Column(db.Float, nullable=True)  # in Celsius
    bp_systolic = db.Column(db.Integer, nullable=True)  # Blood Pressure Systolic
    bp_diastolic = db.Column(db.Integer, nullable=True)  # Blood Pressure Diastolic
    pain_scale = db.Column(db.Integer, nullable=True)  # 0-10
    other_symptoms = db.Column(db.Text, nullable=True)
    assessment = db.Column(db.Text, nullable=True)
    
    # Diagnosis (dropdown options)
    initial_diagnosis = db.Column(db.String(100), nullable=True)
    
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

class QueueTicket(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    patient_id = db.Column(db.Integer, db.ForeignKey('patient.id'), nullable=True)  # Can be null for anonymous walk-ins
    
    # Queue Information
    queue_number = db.Column(db.String(20), nullable=False, unique=True)
    ticket_type = db.Column(db.String(20), nullable=False)  # appointment, walk_in
    transaction_type = db.Column(db.String(50), nullable=False)  # consultation_medical, consultation_dental, certificate_absence, etc.
    
    # Priority System
    priority_level = db.Column(db.Integer, default=3)  # 1=Faculty, 2=Personnel, 3=Senior Citizens, 4=Regular
    priority_type = db.Column(db.String(20), nullable=True)  # faculty, personnel, senior_citizen, regular
    
    # Queue Management
    window_number = db.Column(db.Integer, nullable=True)
    status = db.Column(db.String(20), default='waiting')  # waiting, serving, completed, cancelled
    called_at = db.Column(db.DateTime, nullable=True)
    completed_at = db.Column(db.DateTime, nullable=True)
    
    # Walk-in patient information (for non-registered patients)
    walk_in_name = db.Column(db.String(100), nullable=True)
    walk_in_type = db.Column(db.String(20), nullable=True)  # student, faculty, personnel, visitor
    
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    def get_patient_name(self):
        if self.patient:
            return self.patient.get_full_name()
        return self.walk_in_name or "Anonymous"

class Certificate(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    patient_id = db.Column(db.Integer, db.ForeignKey('patient.id'), nullable=False)
    issued_by_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    
    certificate_type = db.Column(db.String(50), nullable=False)  # absence, employment, ojt, osra
    purpose = db.Column(db.Text, nullable=True)
    date_issued = db.Column(db.Date, default=datetime.utcnow().date)
    valid_until = db.Column(db.Date, nullable=True)
    
    # Certificate content
    content = db.Column(db.Text, nullable=True)
    
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    # Relationships
    issued_by = db.relationship('User', backref='certificates_issued')

# Diagnosis options for dropdown
DIAGNOSIS_OPTIONS = [
    'A2024', 'ABDL DCFT (Abdominal Discomfort)', 'Acute Asthma', 
    'AGE (Acute Gastroenteritis)', 'Allergy', 'Animal Bite', 
    'APE (Annual Physical Examination)', 'ATP (Acute Tonsilo-Pharyngitis)', 
    'Burn', 'Cardiac Related Disorder', 'Dental Disorder', 'Dysmenorrhea', 
    'Ear Disorder', 'Epistaxis', 'Eye Disorder', 
    'GERD (Gastro-Esophageal Reflux Disease)', 'SRI (Sports Related Injury)', 
    'Surgical Procedure', 'SVI (Systemic Viral Infection)', 'TO Rule-out', 
    'Trauma', 'URTI (Upper Respiratory Tract Infection)'
]

BLOOD_TYPES = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']

CAMPUS_OPTIONS = ['Malolos', 'Meneses', 'Hagonoy', 'Bustos']

COLLEGE_OFFICE_OPTIONS = [
    'CICT', 'CON', 'COE', 'CABE', 'CAS', 'CCJE', 'CHTM',
    'HR', 'Accounting', 'Registrar', 'Library', 'Guidance', 'OSA'
]
