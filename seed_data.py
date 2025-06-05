#!/usr/bin/env python3
"""
Database seeding script for the Clinic Management System
Run this script to populate the database with sample data for testing
"""

from app import create_app, db
from app.models import User, Patient, Doctor, Appointment, MedicalRecord, QueueTicket
from datetime import datetime, timedelta
import random
from werkzeug.security import generate_password_hash

def seed_database():
    app = create_app()
    
    with app.app_context():
        print("🌱 Starting database seeding...")
        
        # Clear existing data (optional - uncomment if needed)
        # db.drop_all()
        # db.create_all()
        
        # Create staff users
        create_staff_users()
        
        # Create doctors
        create_doctors()
        
        # Create patients
        create_patients()
        
        # Create appointments
        create_appointments()
        
        # Create medical records
        create_medical_records()
        
        # Create queue tickets
        create_queue_tickets()
        
        print("✅ Database seeding completed successfully!")
        print("\n📊 Database Statistics:")
        print(f"👨‍💼 Staff Users: {User.query.count()}")
        print(f"👨‍⚕️ Doctors: {Doctor.query.count()}")
        print(f"🏥 Patients: {Patient.query.count()}")
        print(f"📅 Appointments: {Appointment.query.count()}")
        print(f"📋 Medical Records: {MedicalRecord.query.count()}")
        print(f"🎫 Queue Tickets: {QueueTicket.query.count()}")
        
        print("\n🔑 Login Credentials:")
        print("Super Admin: admin / admin123")
        print("Doctor: dr.smith / doctor123")
        print("Nurse: nurse.jane / nurse123")
        print("Receptionist: reception.mary / reception123")

def create_staff_users():
    """Create staff users with different roles"""
    staff_data = [
        {
            'username': 'dr.smith',
            'email': 'dr.smith@clinic.com',
            'first_name': 'John',
            'last_name': 'Smith',
            'role': 'doctor',
            'password': 'doctor123'
        },
        {
            'username': 'dr.johnson',
            'email': 'dr.johnson@clinic.com',
            'first_name': 'Sarah',
            'last_name': 'Johnson',
            'role': 'doctor',
            'password': 'doctor123'
        },
        {
            'username': 'nurse.jane',
            'email': 'jane.doe@clinic.com',
            'first_name': 'Jane',
            'last_name': 'Doe',
            'role': 'nurse',
            'password': 'nurse123'
        },
        {
            'username': 'nurse.bob',
            'email': 'bob.wilson@clinic.com',
            'first_name': 'Bob',
            'last_name': 'Wilson',
            'role': 'nurse',
            'password': 'nurse123'
        },
        {
            'username': 'reception.mary',
            'email': 'mary.brown@clinic.com',
            'first_name': 'Mary',
            'last_name': 'Brown',
            'role': 'receptionist',
            'password': 'reception123'
        }
    ]
    
    for staff in staff_data:
        existing_user = User.query.filter_by(username=staff['username']).first()
        if not existing_user:
            user = User(
                username=staff['username'],
                email=staff['email'],
                first_name=staff['first_name'],
                last_name=staff['last_name'],
                role=staff['role']
            )
            user.set_password(staff['password'])
            db.session.add(user)
    
    db.session.commit()
    print("👨‍💼 Staff users created")

def create_doctors():
    """Create doctor profiles"""
    doctors_data = [
        {
            'first_name': 'John',
            'last_name': 'Smith',
            'specialization': 'General Medicine',
            'license_number': 'MD-001-2023',
            'phone': '+1-555-0101',
            'email': 'dr.smith@clinic.com'
        },
        {
            'first_name': 'Sarah',
            'last_name': 'Johnson',
            'specialization': 'Pediatrics',
            'license_number': 'MD-002-2023',
            'phone': '+1-555-0102',
            'email': 'dr.johnson@clinic.com'
        },
        {
            'first_name': 'Michael',
            'last_name': 'Davis',
            'specialization': 'Cardiology',
            'license_number': 'MD-003-2023',
            'phone': '+1-555-0103',
            'email': 'dr.davis@clinic.com'
        }
    ]
    
    for doctor_data in doctors_data:
        existing_doctor = Doctor.query.filter_by(license_number=doctor_data['license_number']).first()
        if not existing_doctor:
            doctor = Doctor(**doctor_data)
            db.session.add(doctor)
    
    db.session.commit()
    print("👨‍⚕️ Doctors created")

def create_patients():
    """Create sample patients"""
    patients_data = [
        {
            'patient_number': 'PAT-001',
            'first_name': 'Alice',
            'last_name': 'Cooper',
            'email': 'alice.cooper@email.com',
            'age': 28,
            'sex': 'Female',
            'date_of_birth': datetime(1995, 5, 15).date(),
            'phone': '+1-555-1001',
            'emergency_contact_name': 'Bob Cooper',
            'emergency_contact_relation': 'Father',
            'emergency_contact_number': '+1-555-1002',
            'blood_type': 'A+',
            'patient_type': 'student',
            'campus': 'Main Campus',
            'college_office': 'College of Engineering',
            'course_designation': 'BS Computer Science - 4th Year',
            'city_municipality': 'Malolos',
            'province': 'Bulacan'
        },
        {
            'patient_number': 'PAT-002',
            'first_name': 'Robert',
            'last_name': 'Johnson',
            'email': 'robert.johnson@email.com',
            'age': 35,
            'sex': 'Male',
            'date_of_birth': datetime(1988, 8, 22).date(),
            'phone': '+1-555-1003',
            'emergency_contact_name': 'Lisa Johnson',
            'emergency_contact_relation': 'Spouse',
            'emergency_contact_number': '+1-555-1004',
            'blood_type': 'B+',
            'patient_type': 'faculty',
            'campus': 'Main Campus',
            'college_office': 'Administration Office',
            'course_designation': 'Professor - Mathematics Department',
            'city_municipality': 'Malolos',
            'province': 'Bulacan'
        },
        {
            'patient_number': 'PAT-003',
            'first_name': 'Emily',
            'last_name': 'Davis',
            'email': 'emily.davis@email.com',
            'age': 22,
            'sex': 'Female',
            'date_of_birth': datetime(2001, 12, 3).date(),
            'phone': '+1-555-1005',
            'emergency_contact_name': 'Mike Davis',
            'emergency_contact_relation': 'Father',
            'emergency_contact_number': '+1-555-1006',
            'blood_type': 'O-',
            'patient_type': 'student',
            'campus': 'North Campus',
            'college_office': 'College of Medicine',
            'course_designation': 'BS Nursing - 2nd Year',
            'city_municipality': 'Malolos',
            'province': 'Bulacan'
        }
    ]
    
    for patient_data in patients_data:
        existing_patient = Patient.query.filter_by(patient_number=patient_data['patient_number']).first()
        if not existing_patient:
            patient = Patient(**patient_data)
            db.session.add(patient)
    
    db.session.commit()
    print("🏥 Patients created")

def create_appointments():
    """Create sample appointments"""
    patients = Patient.query.all()
    doctors = Doctor.query.all()
    
    if not patients or not doctors:
        print("⚠️ No patients or doctors found. Skipping appointments creation.")
        return
    
    # Create appointments for the next 7 days
    base_date = datetime.now()
    
    for i in range(10):  # Create 10 sample appointments
        appointment_datetime = base_date + timedelta(days=random.randint(0, 7))
        appointment_datetime = appointment_datetime.replace(
            hour=random.randint(8, 17),
            minute=random.choice([0, 30]),
            second=0,
            microsecond=0
        )
        
        appointment = Appointment(
            patient_id=random.choice(patients).id,
            doctor_id=random.choice(doctors).id,
            appointment_date=appointment_datetime,
            appointment_type=random.choice(['consultation', 'follow_up', 'check_up', 'emergency']),
            status=random.choice(['scheduled', 'completed', 'cancelled']),
            notes=f'Sample appointment #{i+1}'
        )
        db.session.add(appointment)
    
    db.session.commit()
    print("📅 Appointments created")

def create_medical_records():
    """Create sample medical records"""
    patients = Patient.query.all()
    doctors = Doctor.query.all()
    users = User.query.all()
    
    if not patients or not doctors or not users:
        print("⚠️ No patients, doctors, or users found. Skipping medical records creation.")
        return
    
    sample_diagnoses = [
        'Common Cold', 'Hypertension', 'Diabetes Type 2', 'Anxiety',
        'Back Pain', 'Migraine', 'Allergic Rhinitis', 'Gastritis'
    ]
    
    transaction_types = ['consultation', 'certificate', 'follow_up', 'emergency']
    
    for i in range(15):  # Create 15 sample medical records
        record = MedicalRecord(
            patient_id=random.choice(patients).id,
            doctor_id=random.choice(doctors).id,
            attending_staff_id=random.choice(users).id,
            transaction_type=random.choice(transaction_types),
            date_time=datetime.now() - timedelta(days=random.randint(1, 30)),
            transaction_details=f'Sample medical record #{i+1}',
            height=random.uniform(150, 180),  # cm
            weight=random.uniform(50, 100),   # kg
            hr=random.randint(60, 100),       # bpm
            rr=random.randint(12, 20),        # breaths per minute
            temperature=random.uniform(36.0, 37.5),  # Celsius
            bp_systolic=random.randint(110, 140),
            bp_diastolic=random.randint(70, 90),
            pain_scale=random.randint(0, 5),
            initial_diagnosis=random.choice(sample_diagnoses),
            assessment=f'Patient examination completed. {random.choice(sample_diagnoses)} diagnosed.'
        )
        db.session.add(record)
    
    db.session.commit()
    print("📋 Medical records created")

def create_queue_tickets():
    """Create sample queue tickets"""
    patients = Patient.query.all()
    
    if not patients:
        print("⚠️ No patients found. Skipping queue tickets creation.")
        return
    
    transaction_types = [
        'consultation_medical', 'consultation_dental', 'certificate_absence',
        'certificate_employment', 'certificate_ojt', 'laboratory', 'pharmacy'
    ]
    
    priorities = [1, 2, 3, 4]  # 1=Faculty, 2=Personnel, 3=Senior Citizens, 4=Regular
    priority_types = ['faculty', 'personnel', 'senior_citizen', 'regular']
    statuses = ['waiting', 'serving', 'completed', 'cancelled']
    
    # Create tickets for today
    today = datetime.now().date()
    
    for i in range(8):  # Create 8 sample queue tickets
        ticket = QueueTicket(
            patient_id=random.choice(patients).id,
            queue_number=f'Q{today.strftime("%Y%m%d")}{str(i+1).zfill(3)}',
            ticket_type=random.choice(['appointment', 'walk_in']),
            transaction_type=random.choice(transaction_types),
            priority_level=random.choice(priorities),
            priority_type=random.choice(priority_types),
            status=random.choice(statuses),
            created_at=datetime.now() - timedelta(hours=random.randint(0, 8)),
            window_number=random.randint(1, 5) if random.choice([True, False]) else None
        )
        db.session.add(ticket)
    
    db.session.commit()
    print("🎫 Queue tickets created")

if __name__ == '__main__':
    seed_database()
