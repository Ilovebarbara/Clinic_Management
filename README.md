# 🏥 University Clinic Management System

A comprehensive web-based clinic management system designed specifically for university healthcare services. Built with Laravel framework and enhanced with **real-time capabilities**, advanced analytics, and modern notification systems.

## 🏥 Overview

This system provides complete clinic management functionality including patient registration, appointment scheduling, **real-time queue management**, medical records, analytics dashboard, and multiple interface options (web, mobile, kiosk) for comprehensive healthcare service delivery.

## 🚀 **NEW ENHANCED FEATURES**

### 🔔 Real-time Notifications
- **Browser Push Notifications** with permission handling
- **Audio Alerts** using Web Audio API  
- **Speech Synthesis** for accessibility announcements
- **Visual In-app Notifications** with smooth animations
- **Customizable Notification Preferences**

### 📊 Advanced Analytics Dashboard
- **Live Data Visualization** with interactive charts
- **Comprehensive Metrics** (completion rates, service times)
- **Multi-period Analysis** (Today, Week, Month, Year)
- **Priority Distribution Analytics**
- **Wait Time Analysis by Priority Level**
- **Data Export Capabilities** (JSON format)

### 🚀 Enhanced Queue Management  
- **Real-time Updates** using Server-Sent Events
- **Priority-based Queuing** (Emergency → Faculty → Personnel → Students)
- **Multi-window Service Management** (4 service windows)
- **Live Status Monitoring** across all interfaces
- **Automatic Queue Number Generation**
- **Estimated Wait Time Calculations**

## ✨ Features

### 🔐 Authentication & User Management
- **Dual Authentication System**: Separate login systems for staff and patients
- **Role-Based Access Control**: Super Admin, Admin, Doctor, Nurse, Patient roles
- **User Profile Management**: Profile editing, password change, profile picture upload
- **Session Management**: Secure session handling with timeout

### 👥 Patient Management
- **Patient Registration**: Comprehensive 13-field registration form
- **Patient Types**: Student, Faculty, Staff, and Non-academic personnel
- **Medical Information**: Blood type, allergies, medical conditions tracking
- **Emergency Contacts**: Complete emergency contact information
- **Patient Search & Filter**: Advanced search by multiple criteria

### 📅 Appointment System
- **Online Booking**: Web-based appointment scheduling
- **Doctor Assignment**: Automatic or manual doctor assignment
- **Appointment Types**: Various service types (consultation, certificates, etc.)
- **Status Tracking**: Scheduled, Confirmed, Completed, Cancelled status
- **Email Notifications**: Automatic appointment confirmations and reminders

### 🎫 Queue Management System
- **Real-time Queue Updates**: Live status updates using Server-Sent Events
- **Priority-based System**: Emergency, Faculty, Personnel, Senior Citizens, Regular
- **Multi-window Management**: Support for multiple service windows
- **Digital Queue Display**: Real-time queue status for patients
- **Ticket Generation**: Automatic queue number assignment
- **Wait Time Estimation**: Dynamic wait time calculations
- **Audio & Visual Notifications**: Queue calling with accessibility features
- **Mobile Queue Viewing**: Check queue status from mobile devices

### 📈 Analytics & Reporting  
- **Real-time Dashboard**: Live metrics and KPI tracking
- **Patient Demographics**: Student vs employee statistics
- **Service Utilization**: Most requested services and peak hours
- **Queue Performance**: Average wait times and completion rates
- **Priority Analysis**: Emergency vs regular traffic patterns
- **Exportable Reports**: JSON format for external integration
- **Interactive Charts**: Visual data representation with Chart.js

### 🔔 Advanced Notification System
- **Browser Notifications**: Native push notification support
- **Audio Alerts**: Custom sound generation using Web Audio API
- **Speech Synthesis**: Text-to-speech for accessibility
- **In-app Notifications**: Visual alerts with animations
- **Notification Preferences**: User-customizable alert settings
- **Priority-Based Queuing**: Faculty > Personnel > Senior Citizens > Regular
- **Real-time Updates**: Live queue status and waiting times
- **Window Management**: Multiple service windows with staff assignment
- **Ticket Generation**: Automated ticket numbering system
- **SMS Notifications**: Queue status updates via SMS

### 🏥 Medical Records
- **Complete Medical History**: Comprehensive patient medical records
- **Vital Signs Tracking**: Temperature, BP, heart rate, weight, height
- **Diagnosis Management**: Predefined diagnosis list with custom options
- **Treatment Records**: Prescription and treatment documentation
- **Doctor Notes**: Additional observations and recommendations

### 🖥️ Kiosk System
- **Self-Service Registration**: Patient registration at kiosk terminals
- **Ticket Generation**: Walk-in queue ticket printing
- **Appointment Check-in**: Appointment confirmation via kiosk
- **Multilingual Support**: English and Filipino language options
- **Touch-Screen Interface**: User-friendly touch interface

### 📱 Mobile Portal
- **Patient Mobile App**: Dedicated mobile interface for patients
- **Queue Status**: Real-time queue position and wait times
- **Appointment Management**: View and manage appointments
- **Medical History**: Access to personal medical records
- **Notification Center**: Push notifications for appointments and queue updates

### 📊 Dashboard & Analytics
- **Real-time Statistics**: Live clinic performance metrics
- **Patient Demographics**: Age, gender, type distribution charts
- **Appointment Analytics**: Booking trends and patterns
- **Queue Performance**: Average wait times and service efficiency
- **Medical Records Summary**: Most common diagnoses and treatments

## 🛠️ Technology Stack

### Backend
- **Framework**: Laravel 10.x
- **PHP**: Version 8.1+
- **Database**: MySQL 8.0
- **Authentication**: Laravel Sanctum
- **Queue System**: Laravel Queue with Redis

### Frontend
- **CSS Framework**: Bootstrap 5.3
- **JavaScript**: Vanilla JS with modern ES6+
- **Icons**: Font Awesome 6.4
- **Charts**: Chart.js for analytics
- **Responsive Design**: Mobile-first approach

## 🚀 Quick Start

### Demo System
A working demo is available in the `demo/` folder:

1. **Access the Demo**:
   - Navigate to the `demo/` folder
   - Open `login.php` in your browser
   - Use demo credentials: admin@clinic.com / password

2. **Demo Features**:
   - **Dashboard**: `demo/dashboard.php` - Overview and statistics
   - **Patients**: `demo/patients.php` - Patient management
   - **Appointments**: `demo/appointments.php` - Appointment scheduling
   - **Medical Records**: `demo/medical-records.php` - Patient medical history
   - **Queue Management**: `demo/queue.php` - Real-time queue system
   - **Kiosk**: `demo/kiosk.php` - Self-service terminal
   - **Mobile Portal**: `demo/mobile.php` - Patient mobile interface

### Full Laravel Installation

1. **Install Dependencies**:
   ```bash
   composer install
   ```

2. **Environment Setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

4. **Start Server**:
   ```bash
   php artisan serve
   ```

## 📁 Project Structure

```
Clinic_Management/
├── app/
│   ├── Http/Controllers/        # Application controllers
│   ├── Models/                  # Eloquent models
│   └── Middleware/              # Custom middleware
├── database/
│   ├── migrations/              # Database schema
│   └── seeders/                 # Sample data
├── resources/views/             # Blade templates
├── routes/web.php               # Application routes
├── demo/                        # Working demo system
│   ├── dashboard.php            # Staff dashboard
│   ├── patients.php             # Patient management
│   ├── appointments.php         # Appointment system
│   ├── medical-records.php      # Medical records
│   ├── queue.php                # Queue management
│   ├── kiosk.php                # Kiosk interface
│   ├── mobile.php               # Mobile portal
│   └── login.php                # Authentication
└── config/                      # Configuration files
```

## 👨‍💼 User Roles & Permissions

### Super Admin
- Complete system access
- User management
- System configuration
- Reports and analytics

### Admin/Doctor
- Patient management
- Appointment scheduling
- Medical records
- Queue management

### Patient
- Profile management
- Appointment booking
- Medical history viewing
- Queue status checking

## 🔐 Security Features

- **Authentication**: Secure login with password hashing
- **CSRF Protection**: All forms protected against CSRF attacks
- **Input Validation**: Comprehensive data validation
- **Role-Based Access**: Middleware-based permission system
- **Session Security**: Secure session management

## 📊 Database Schema

### Core Tables
- **users**: Staff authentication and profiles
- **patients**: Patient information and medical data
- **appointments**: Appointment scheduling and tracking
- **medical_records**: Patient medical history
- **queue_tickets**: Real-time queue management
- **doctors**: Doctor profiles and specializations
- **certificates**: Medical certificate generation

## 🌐 **ACCESS POINTS**

### 🏠 Main Application
- **Production URL**: http://localhost:8000
- **Login Page**: http://localhost:8000/login  
- **Patient Registration**: http://localhost:8000/register
- **Patient Portal**: http://localhost:8000/patient-portal
- **Staff Dashboard**: http://localhost:8000/staff-dashboard
- **Admin Panel**: http://localhost:8000/admin-dashboard

### 🚀 Enhanced Features  
- **Enhanced Queue Management**: http://localhost:8000/enhanced-queue
- **Real-time Analytics Dashboard**: http://localhost:8000/analytics-dashboard
- **Enhanced Features Demo**: http://localhost:8000/enhanced-features
- **Mobile Interface**: http://localhost:8000/mobile
- **Kiosk Interface**: http://localhost:8000/kiosk

## 🧪 Demo Credentials

### 👑 Admin Access
- **Email**: admin@clinic.edu
- **Password**: admin123
- **Permissions**: Full system access, analytics, user management

### 👨‍⚕️ Doctor Access  
- **Email**: dr.smith@clinic.edu
- **Password**: doctor123
- **Permissions**: Patient records, queue management, appointments

### 👩‍⚕️ Nurse Access
- **Email**: nurse.jones@clinic.edu  
- **Password**: nurse123
- **Permissions**: Queue management, basic patient info

### 🎯 Features Demonstrated
- **Real-time queue updates** with live notifications
- **Advanced analytics** with interactive charts
- **Complete patient registration** (13 required fields)
- **Priority-based queue system** with multiple service windows
- **Browser and audio notifications** with accessibility features
- **Multi-device responsive design** (desktop, tablet, mobile, kiosk)
- **Data export capabilities** for reporting and integration

## 📱 Responsive Design

The system is fully responsive and optimized for:
- **Desktop**: Full-featured admin interface
- **Tablet**: Touch-friendly kiosk interface
- **Mobile**: Patient-focused mobile portal
- **Kiosk**: Large-screen self-service terminals

## 🚀 Production Deployment

### System Requirements
- PHP 8.1+
- MySQL 8.0+
- Nginx/Apache
- Composer
- SSL Certificate

### Environment Configuration
```env
APP_ENV=production
DB_CONNECTION=mysql
MAIL_MAILER=smtp
SMS_DRIVER=twilio
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

## 📞 Support & Documentation

### Getting Help
- Check the comprehensive documentation in each demo file
- Review the code comments for implementation details
- Test the demo system to understand functionality
- Submit issues for bug reports or feature requests

### Features Ready for Production
- ✅ Authentication system
- ✅ Patient management
- ✅ Appointment scheduling
- ✅ Queue management
- ✅ Medical records
- ✅ Kiosk interface
- ✅ Mobile portal
- ✅ Responsive design

### Pending Implementation
- Email notifications
- SMS integration
- PDF generation
- Real-time WebSocket updates
- Advanced reporting
- File upload management

## 📄 License

This project is licensed under the MIT License.

---

**Version**: 1.0.0  
**Last Updated**: June 2024  
**Status**: Demo system fully functional, Laravel integration pending
