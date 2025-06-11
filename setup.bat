@echo off
echo Setting up Laravel Clinic Management System...

REM Navigate to project directory
cd /d "%~dp0"

REM Copy environment file if it doesn't exist
if not exist .env (
    copy .env.example .env
    echo Environment file created from .env.example
)

REM Generate application key
php artisan key:generate --force

REM Create symbolic link for storage
php artisan storage:link

REM Clear all cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

REM Run database migrations
echo Running database migrations...
php artisan migrate --force

REM Seed the database with sample data
echo Seeding database with sample data...
php artisan db:seed --force

echo Setup complete! The Laravel Clinic Management System is ready.
echo.
echo To start the development server, run:
echo php artisan serve
echo.
echo Default admin credentials:
echo Email: admin@clinic.edu
echo Password: password

pause
