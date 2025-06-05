from flask import Blueprint, render_template, request, redirect, url_for, flash, session
from flask_login import login_user, logout_user, login_required, current_user
from app import db
from app.models import User, Patient
from werkzeug.security import check_password_hash
import re

auth = Blueprint('auth', __name__)

@auth.route('/login', methods=['GET', 'POST'])
def login():
    if current_user.is_authenticated:
        return redirect(url_for('main.dashboard'))
    
    if request.method == 'POST':
        login_field = request.form.get('login_field')  # Can be username or email
        password = request.form.get('password')
        remember_me = bool(request.form.get('remember_me'))
        user_type = request.form.get('user_type', 'staff')  # staff or patient
        
        if user_type == 'patient':
            # Patient login
            patient = None
            if '@' in login_field:
                patient = Patient.query.filter_by(email=login_field).first()
            else:
                patient = Patient.query.filter_by(patient_number=login_field).first()
            
            if patient and patient.check_password(password):
                # Create a temporary user session for patient
                session['patient_id'] = patient.id
                flash('Welcome back!', 'success')
                return redirect(url_for('patient_portal.dashboard'))
            else:
                flash('Invalid patient credentials.', 'error')
        else:
            # Staff login
            user = None
            if '@' in login_field:
                user = User.query.filter_by(email=login_field).first()
            else:
                user = User.query.filter_by(username=login_field).first()
            
            if user and user.check_password(password) and user.is_active:
                login_user(user, remember=remember_me)
                next_page = request.args.get('next')
                flash(f'Welcome back, {user.get_full_name()}!', 'success')
                return redirect(next_page) if next_page else redirect(url_for('main.dashboard'))
            else:
                flash('Invalid username/email or password.', 'error')
    
    return render_template('auth/login.html')

@auth.route('/logout')
def logout():
    if current_user.is_authenticated:
        flash(f'Goodbye, {current_user.get_full_name()}!', 'info')
        logout_user()
    elif 'patient_id' in session:
        session.pop('patient_id', None)
        flash('You have been logged out.', 'info')
    
    return redirect(url_for('auth.login'))

@auth.route('/register', methods=['GET', 'POST'])
@login_required
def register_staff():
    # Only super admin can register new staff
    if current_user.role != 'super_admin':
        flash('Access denied. Only super administrators can create staff accounts.', 'error')
        return redirect(url_for('main.dashboard'))
    
    if request.method == 'POST':
        username = request.form.get('username')
        email = request.form.get('email')
        password = request.form.get('password')
        confirm_password = request.form.get('confirm_password')
        first_name = request.form.get('first_name')
        last_name = request.form.get('last_name')
        role = request.form.get('role')
        phone = request.form.get('phone')
        employee_id = request.form.get('employee_id')
        
        # Validation
        errors = []
        
        if not all([username, email, password, first_name, last_name, role]):
            errors.append('All required fields must be filled.')
        
        if len(password) < 6:
            errors.append('Password must be at least 6 characters long.')
        
        if password != confirm_password:
            errors.append('Passwords do not match.')
        
        if User.query.filter_by(username=username).first():
            errors.append('Username already exists.')
        
        if User.query.filter_by(email=email).first():
            errors.append('Email already exists.')
        
        if employee_id and User.query.filter_by(employee_id=employee_id).first():
            errors.append('Employee ID already exists.')
        
        if not re.match(r'^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$', email):
            errors.append('Invalid email format.')
        
        if errors:
            for error in errors:
                flash(error, 'error')
            return render_template('auth/register_staff.html')
        
        # Create new user
        user = User(
            username=username,
            email=email,
            first_name=first_name,
            last_name=last_name,
            role=role,
            phone=phone,
            employee_id=employee_id,
            created_by_id=current_user.id
        )
        user.set_password(password)
        
        db.session.add(user)
        db.session.commit()
        
        flash(f'Staff account created successfully for {user.get_full_name()}.', 'success')
        return redirect(url_for('admin.manage_staff'))
    
    return render_template('auth/register_staff.html')

@auth.route('/profile')
@login_required
def profile():
    return render_template('auth/profile.html', user=current_user)

@auth.route('/edit_profile', methods=['GET', 'POST'])
@login_required
def edit_profile():
    if request.method == 'POST':
        current_user.first_name = request.form.get('first_name')
        current_user.last_name = request.form.get('last_name')
        current_user.email = request.form.get('email')
        current_user.phone = request.form.get('phone')
        
        # Handle profile picture upload
        if 'profile_picture' in request.files:
            file = request.files['profile_picture']
            if file and file.filename:
                # Save file logic here
                pass
        
        db.session.commit()
        flash('Profile updated successfully.', 'success')
        return redirect(url_for('auth.profile'))
    
    return render_template('auth/edit_profile.html', user=current_user)

@auth.route('/change_password', methods=['GET', 'POST'])
@login_required
def change_password():
    if request.method == 'POST':
        old_password = request.form.get('old_password')
        new_password = request.form.get('new_password')
        confirm_password = request.form.get('confirm_password')
        
        if not current_user.check_password(old_password):
            flash('Current password is incorrect.', 'error')
        elif new_password != confirm_password:
            flash('New passwords do not match.', 'error')
        elif len(new_password) < 6:
            flash('New password must be at least 6 characters long.', 'error')
        else:
            current_user.set_password(new_password)
            db.session.commit()
            flash('Password changed successfully.', 'success')
            return redirect(url_for('auth.profile'))
    
    return render_template('auth/change_password.html')

@auth.route('/delete_account', methods=['POST'])
@login_required
def delete_account():
    if current_user.role == 'super_admin':
        flash('Super admin account cannot be deleted.', 'error')
        return redirect(url_for('auth.profile'))
    
    password = request.form.get('password')
    if not current_user.check_password(password):
        flash('Password is incorrect.', 'error')
        return redirect(url_for('auth.profile'))
    
    user_name = current_user.get_full_name()
    db.session.delete(current_user)
    db.session.commit()
    
    logout_user()
    flash(f'Account for {user_name} has been deleted.', 'info')
    return redirect(url_for('auth.login'))
