<?php
/**
 * University Clinic Management System
 * Production Configuration
 */

return [
    // Application Settings
    'app' => [
        'name' => 'University Clinic Management System',
        'version' => '2.0.0',
        'debug' => false,
        'timezone' => 'Asia/Manila',
        'locale' => 'en',
    ],
    
    // Database Configuration
    'database' => [
        'default' => 'sqlite',
        'connections' => [
            'sqlite' => [
                'driver' => 'sqlite',
                'database' => __DIR__ . '/../database/clinic.sqlite',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'mysql' => [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_DATABASE', 'clinic_management'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
            ],
        ],
    ],
    
    // Session Configuration
    'session' => [
        'lifetime' => 120, // 2 hours
        'expire_on_close' => false,
        'encrypt' => false,
        'files' => __DIR__ . '/../storage/framework/sessions',
        'connection' => null,
        'table' => 'sessions',
        'store' => null,
        'lottery' => [2, 100],
        'cookie' => 'clinic_session',
        'path' => '/',
        'domain' => env('SESSION_DOMAIN', null),
        'secure' => env('SESSION_SECURE_COOKIE', false),
        'http_only' => true,
        'same_site' => 'lax',
    ],
    
    // Security Configuration
    'security' => [
        'password_hash_cost' => 12,
        'csrf_protection' => true,
        'max_login_attempts' => 5,
        'lockout_duration' => 900, // 15 minutes
    ],
    
    // Queue Configuration
    'queue' => [
        'priority_levels' => [
            0 => 'Emergency',
            1 => 'Faculty',
            2 => 'Personnel', 
            3 => 'Senior Citizen',
            4 => 'Regular',
        ],
        'max_windows' => 4,
        'average_service_time' => 10, // minutes
        'auto_refresh_interval' => 5000, // milliseconds
    ],
    
    // Notification Configuration
    'notifications' => [
        'audio_enabled' => true,
        'browser_notifications' => true,
        'speech_synthesis' => true,
        'default_volume' => 0.7,
    ],
    
    // File Upload Configuration
    'uploads' => [
        'max_file_size' => 5242880, // 5MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
        'path' => __DIR__ . '/../storage/app/uploads',
    ],
    
    // Logging Configuration
    'logging' => [
        'default' => 'file',
        'channels' => [
            'file' => [
                'driver' => 'file',
                'path' => __DIR__ . '/../storage/logs/clinic.log',
                'level' => 'info',
                'max_files' => 30,
            ],
        ],
    ],
    
    // API Configuration
    'api' => [
        'rate_limit' => [
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
        'version' => 'v1',
        'prefix' => 'api',
    ],
    
    // Feature Flags
    'features' => [
        'real_time_queue' => true,
        'analytics_dashboard' => true,
        'mobile_interface' => true,
        'kiosk_mode' => true,
        'sms_notifications' => false,
        'email_notifications' => false,
        'appointment_reminders' => false,
    ],
];

/**
 * Environment helper function
 */
function env($key, $default = null) {
    $value = $_ENV[$key] ?? getenv($key);
    
    if ($value === false) {
        return $default;
    }
    
    // Convert string representations to appropriate types
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return null;
    }
    
    // Remove quotes if present
    if (strlen($value) > 1 && $value[0] === '"' && $value[-1] === '"') {
        return substr($value, 1, -1);
    }
    
    return $value;
}
