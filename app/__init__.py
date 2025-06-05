from flask import Flask
from flask_sqlalchemy import SQLAlchemy
from flask_login import LoginManager
from flask_migrate import Migrate
from config import Config
import os

db = SQLAlchemy()
login_manager = LoginManager()
migrate = Migrate()

def create_app():
    app = Flask(__name__)
    app.config.from_object(Config)
    
    # Initialize extensions
    db.init_app(app)
    login_manager.init_app(app)
    migrate.init_app(app, db)
    
    # Configure login manager
    login_manager.login_view = 'auth.login'
    login_manager.login_message = 'Please log in to access this page.'
    login_manager.login_message_category = 'info'
    
    @login_manager.user_loader
    def load_user(user_id):
        from app.models import User
        return User.query.get(int(user_id))
    
    # Create upload directories
    uploads_dir = os.path.join(app.static_folder, 'uploads')
    profile_pics_dir = os.path.join(uploads_dir, 'profile_pics')
    os.makedirs(profile_pics_dir, exist_ok=True)
    
    # Register blueprints
    from app.routes import main
    from app.auth import auth
    from app.admin import admin
    from app.queue import queue
    from app.patient_portal import patient_portal
    from app.api import api
    
    app.register_blueprint(main)
    app.register_blueprint(auth, url_prefix='/auth')
    app.register_blueprint(admin, url_prefix='/admin')
    app.register_blueprint(queue, url_prefix='/queue')
    app.register_blueprint(patient_portal, url_prefix='/patient')
    app.register_blueprint(api, url_prefix='/api')
    
    # Create database tables and default admin user
    with app.app_context():
        db.create_all()
        create_default_admin()
    
    return app

def create_default_admin():
    from app.models import User
    
    # Check if admin exists
    admin = User.query.filter_by(username='admin').first()
    if not admin:
        admin = User(
            username='admin',
            email='admin@clinic.com',
            first_name='Super',
            last_name='Admin',
            role='super_admin'
        )
        admin.set_password('admin123')  # Change this in production
        db.session.add(admin)
        db.session.commit()
        print("Default admin user created: admin/admin123")
