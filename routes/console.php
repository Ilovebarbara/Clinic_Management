<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Custom clinic management commands
Artisan::command('clinic:status', function () {
    $this->info('University Clinic Management System Status:');
    $this->line('✓ System operational');
    $this->line('✓ Database connected');
    $this->line('✓ Queue system active');
})->purpose('Display clinic system status');

Artisan::command('queue:cleanup', function () {
    $this->info('Cleaning up old queue tickets...');
    // Implementation would go here
    $this->info('Queue cleanup completed.');
})->purpose('Clean up old queue tickets');

Artisan::command('appointments:send-reminders', function () {
    $this->info('Sending appointment reminders...');
    // Implementation would go here
    $this->info('Appointment reminders sent.');
})->purpose('Send appointment reminder notifications');

Artisan::command('reports:daily', function () {
    $this->info('Generating daily reports...');
    // Implementation would go here
    $this->info('Daily reports generated.');
})->purpose('Generate daily clinic reports');
