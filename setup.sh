#!/bin/bash

# Laravel Clinic Management System Setup Script
echo "Setting up Laravel Clinic Management System..."

# Navigate to project directory
cd "$(dirname "$0")"

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    echo "Environment file created from .env.example"
fi

# Generate application key
php artisan key:generate --force

# Create symbolic link for storage
php artisan storage:link

# Clear all cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed the database with sample data
echo "Seeding database with sample data..."
php artisan db:seed --force

# Set proper permissions (for Linux/Mac)
chmod -R 775 storage bootstrap/cache

echo "Setup complete! The Laravel Clinic Management System is ready."
echo ""
echo "To start the development server, run:"
echo "php artisan serve"
echo ""
echo "Default admin credentials:"
echo "Email: admin@clinic.edu"
echo "Password: password"
