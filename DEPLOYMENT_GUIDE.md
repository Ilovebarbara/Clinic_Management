# 🚀 DEPLOYMENT GUIDE
## University Clinic Management System

---

## 📋 **PRE-DEPLOYMENT CHECKLIST**

### **✅ System Requirements**
- **PHP**: 8.0 or higher (8.2+ recommended)
- **Database**: SQLite (included) or MySQL 8.0+
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Memory**: 256MB minimum, 512MB recommended
- **Storage**: 100MB minimum for application

### **✅ Environment Setup**
- [ ] PHP extensions: PDO, SQLite, OpenSSL, JSON, Ctype, Tokenizer
- [ ] Web server configured with proper document root
- [ ] PHP error reporting configured for production
- [ ] File permissions set correctly
- [ ] SSL certificate configured (recommended)

---

## 🏗️ **DEPLOYMENT OPTIONS**

### **Option 1: Shared Hosting Deployment**

#### **Step 1: Upload Files**
```bash
# Upload all files to your hosting account
# Ensure public/ directory is set as document root
```

#### **Step 2: Configure Environment**
```bash
# Copy .env.example to .env
# Set production values:
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/clinic.sqlite
```

#### **Step 3: Set Permissions**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 database/clinic.sqlite
```

### **Option 2: VPS/Dedicated Server**

#### **Step 1: Server Setup**
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo apt install php8.2 php8.2-cli php8.2-sqlite3 php8.2-curl php8.2-zip -y

# Install web server (Nginx example)
sudo apt install nginx -y
```

#### **Step 2: Nginx Configuration**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/clinic/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

#### **Step 3: Deploy Application**
```bash
# Clone or upload files
cd /var/www/
sudo git clone <repository-url> clinic
cd clinic

# Set ownership and permissions
sudo chown -R www-data:www-data .
sudo chmod -R 755 storage/ bootstrap/cache/
sudo chmod 644 database/clinic.sqlite

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

### **Option 3: Docker Deployment**

#### **Create Dockerfile**
```dockerfile
FROM php:8.2-apache

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 755 /var/www/html/storage

EXPOSE 80
```

#### **Deploy with Docker**
```bash
# Build image
docker build -t clinic-management .

# Run container
docker run -d -p 8080:80 --name clinic clinic-management
```

---

## ⚙️ **PRODUCTION CONFIGURATION**

### **Environment Variables (.env)**
```env
# Application
APP_NAME="University Clinic Management"
APP_ENV=production
APP_KEY=your-32-character-secret-key
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/clinic.sqlite

# Session
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true

# Security
BCRYPT_ROUNDS=12
PASSWORD_RESET_EXPIRATION=60

# Features
ENABLE_REAL_TIME_QUEUE=true
ENABLE_ANALYTICS=true
ENABLE_MOBILE_INTERFACE=true
```

### **Production PHP Configuration (php.ini)**
```ini
# Security
expose_php = Off
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /var/log/php_errors.log

# Performance
memory_limit = 512M
max_execution_time = 30
upload_max_filesize = 10M
post_max_size = 10M

# Session Security
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
```

---

## 🔒 **SECURITY HARDENING**

### **File Permissions**
```bash
# Application files (read-only)
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Writable directories
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Sensitive files
chmod 600 .env
chmod 600 database/clinic.sqlite
```

### **Web Server Security**

#### **Apache (.htaccess)**
```apache
# Disable directory browsing
Options -Indexes

# Hide sensitive files
<FilesMatch "\.(env|log|sqlite)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
```

#### **Nginx Security**
```nginx
# Hide sensitive files
location ~ /\.(env|git|log) {
    deny all;
    return 404;
}

location ~ \.sqlite$ {
    deny all;
    return 404;
}

# Security headers
add_header X-Content-Type-Options nosniff;
add_header X-Frame-Options DENY;
add_header X-XSS-Protection "1; mode=block";
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains";
```

---

## 📊 **MONITORING & MAINTENANCE**

### **Health Check Script**
```php
<?php
// health-check.php
$checks = [
    'database' => checkDatabase(),
    'storage' => checkStorage(),
    'memory' => checkMemory(),
    'disk_space' => checkDiskSpace(),
];

function checkDatabase() {
    try {
        $pdo = new PDO('sqlite:database/clinic.sqlite');
        $pdo->query('SELECT 1');
        return ['status' => 'OK', 'message' => 'Database accessible'];
    } catch (Exception $e) {
        return ['status' => 'ERROR', 'message' => 'Database connection failed'];
    }
}

function checkStorage() {
    $writable = is_writable('storage/') && is_writable('bootstrap/cache/');
    return [
        'status' => $writable ? 'OK' : 'ERROR',
        'message' => $writable ? 'Storage directories writable' : 'Storage permissions issue'
    ];
}

function checkMemory() {
    $usage = memory_get_usage(true);
    $limit = ini_get('memory_limit');
    return [
        'status' => 'OK',
        'message' => 'Memory usage: ' . round($usage / 1024 / 1024, 2) . 'MB'
    ];
}

function checkDiskSpace() {
    $free = disk_free_space('.');
    $total = disk_total_space('.');
    $percent = round(($free / $total) * 100, 2);
    return [
        'status' => $percent > 10 ? 'OK' : 'WARNING',
        'message' => "Disk space: {$percent}% free"
    ];
}

header('Content-Type: application/json');
echo json_encode($checks, JSON_PRETTY_PRINT);
?>
```

### **Backup Script**
```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/clinic"
APP_DIR="/var/www/clinic"

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Backup database
cp "$APP_DIR/database/clinic.sqlite" "$BACKUP_DIR/clinic_$DATE.sqlite"

# Backup uploaded files
tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" -C "$APP_DIR" storage/app/uploads/

# Backup configuration
cp "$APP_DIR/.env" "$BACKUP_DIR/env_$DATE.backup"

# Clean old backups (keep 30 days)
find "$BACKUP_DIR" -name "*.sqlite" -mtime +30 -delete
find "$BACKUP_DIR" -name "*.tar.gz" -mtime +30 -delete
find "$BACKUP_DIR" -name "*.backup" -mtime +30 -delete

echo "Backup completed: $DATE"
```

### **Log Rotation**
```bash
# /etc/logrotate.d/clinic
/var/www/clinic/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
```

---

## 🚀 **PERFORMANCE OPTIMIZATION**

### **PHP OpCache Configuration**
```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### **Database Optimization**
```sql
-- SQLite optimization
PRAGMA journal_mode = WAL;
PRAGMA synchronous = NORMAL;
PRAGMA cache_size = 1000;
PRAGMA temp_store = MEMORY;
```

### **Caching Strategy**
```php
// config/cache.php
return [
    'default' => 'file',
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],
        'redis' => [
            'driver' => 'redis',
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'port' => env('REDIS_PORT', '6379'),
        ],
    ],
];
```

---

## 🎯 **TESTING IN PRODUCTION**

### **Smoke Tests**
```bash
# Test critical endpoints
curl -f http://your-domain.com/health-check
curl -f http://your-domain.com/login
curl -f http://your-domain.com/queue

# Test API endpoints
curl -X POST http://your-domain.com/api/queue/status
```

### **Load Testing**
```bash
# Using Apache Bench
ab -n 1000 -c 10 http://your-domain.com/

# Using wrk
wrk -t12 -c400 -d30s http://your-domain.com/
```

---

## 📞 **SUPPORT & TROUBLESHOOTING**

### **Common Issues**

#### **Database Permission Errors**
```bash
# Fix SQLite permissions
sudo chown www-data:www-data database/clinic.sqlite
sudo chmod 664 database/clinic.sqlite
sudo chmod 775 database/
```

#### **Session Issues**
```bash
# Clear session cache
rm -rf storage/framework/sessions/*
rm -rf bootstrap/cache/*
```

#### **Memory Errors**
```bash
# Increase PHP memory limit
echo "memory_limit = 512M" >> /etc/php/8.2/apache2/php.ini
```

### **Log Locations**
- **Application Logs**: `storage/logs/clinic.log`
- **Web Server Logs**: `/var/log/nginx/` or `/var/log/apache2/`
- **PHP Logs**: `/var/log/php_errors.log`
- **System Logs**: `/var/log/syslog`

### **Emergency Contacts**
- **System Administrator**: [admin-email]
- **Database Administrator**: [dba-email]
- **Technical Support**: [support-email]

---

## ✅ **POST-DEPLOYMENT CHECKLIST**

- [ ] Application loads without errors
- [ ] Login system functioning
- [ ] Patient registration working
- [ ] Queue management operational
- [ ] Real-time updates functioning
- [ ] Mobile interface responsive
- [ ] Analytics dashboard accessible
- [ ] All user roles working correctly
- [ ] SSL certificate configured
- [ ] Backups configured and tested
- [ ] Monitoring setup complete
- [ ] Performance optimized
- [ ] Security hardening applied
- [ ] Documentation updated

---

**🎉 Deployment complete! Your University Clinic Management System is now live and ready to serve patients and staff efficiently!**
