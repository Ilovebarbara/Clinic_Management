#!/usr/bin/env python3
"""
Database reset script - drops and recreates all tables
"""

from app import create_app, db
import os

def reset_database():
    app = create_app()
    
    with app.app_context():
        print("🗑️  Dropping all tables...")
        db.drop_all()
        
        print("🏗️  Creating all tables...")
        db.create_all()
        
        print("✅ Database reset completed!")
        
        # Remove old database file if it exists
        db_path = os.path.join('instance', 'clinic.db')
        if os.path.exists(db_path):
            print(f"📁 Database file location: {os.path.abspath(db_path)}")

if __name__ == '__main__':
    reset_database()
