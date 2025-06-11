# 🔧 SYSTEM MAINTENANCE GUIDE
## University Clinic Management System

---

## 📅 **MAINTENANCE SCHEDULE**

### **Daily Tasks**
- [ ] Check system health status
- [ ] Monitor error logs
- [ ] Verify backup completion
- [ ] Check disk space usage
- [ ] Monitor queue performance

### **Weekly Tasks**
- [ ] Review security logs
- [ ] Update user accounts
- [ ] Clean temporary files
- [ ] Performance analysis
- [ ] Test backup restoration

### **Monthly Tasks**
- [ ] Security audit
- [ ] Database optimization
- [ ] Software updates
- [ ] User training review
- [ ] Documentation updates

### **Quarterly Tasks**
- [ ] Full system backup
- [ ] Disaster recovery test
- [ ] Performance benchmarking
- [ ] Security penetration test
- [ ] Feature usage analysis

---

## 🔍 **SYSTEM MONITORING**

### **Key Performance Indicators (KPIs)**

#### **System Health**
```bash
# Check system status
curl http://localhost:8000/health-check

# Monitor response times
time curl -s http://localhost:8000/ > /dev/null

# Check memory usage
ps aux | grep php | awk '{sum+=$4} END {print "PHP Memory Usage: " sum "%"}'
```

#### **Database Performance**
```sql
-- Check database size
SELECT page_count * page_size as size FROM pragma_page_count(), pragma_page_size();

-- Analyze table usage
ANALYZE;

-- Check slow queries (if logging enabled)
.timer on
SELECT * FROM patients LIMIT 1;
```

#### **Queue Statistics**
```php
// Monitor queue performance
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_tickets,
        AVG(CASE WHEN completed_at IS NOT NULL THEN 
            (strftime('%s', completed_at) - strftime('%s', created_at)) / 60.0 
        END) as avg_service_time,
        COUNT(CASE WHEN status = 'waiting' THEN 1 END) as waiting_count
    FROM queue_tickets 
    WHERE DATE(created_at) = DATE('now')
");
```

### **Automated Monitoring Script**
```php
<?php
// monitor.php - Run via cron every 5 minutes

function checkSystemHealth() {
    $alerts = [];
    
    // Check database connectivity
    try {
        $pdo = new PDO('sqlite:database/clinic.sqlite');
        $pdo->query('SELECT 1');
    } catch (Exception $e) {
        $alerts[] = "Database connection failed: " . $e->getMessage();
    }
    
    // Check disk space
    $freeSpace = disk_free_space('.');
    $totalSpace = disk_total_space('.');
    $usagePercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;
    
    if ($usagePercent > 90) {
        $alerts[] = "Disk space critical: {$usagePercent}% used";
    } elseif ($usagePercent > 80) {
        $alerts[] = "Disk space warning: {$usagePercent}% used";
    }
    
    // Check memory usage
    $memoryUsage = memory_get_usage(true);
    $memoryLimit = ini_get('memory_limit');
    $memoryLimitBytes = convertToBytes($memoryLimit);
    $memoryPercent = ($memoryUsage / $memoryLimitBytes) * 100;
    
    if ($memoryPercent > 90) {
        $alerts[] = "Memory usage critical: {$memoryPercent}%";
    }
    
    // Check error logs
    $errorLog = 'storage/logs/clinic.log';
    if (file_exists($errorLog)) {
        $errors = file($errorLog, FILE_IGNORE_NEW_LINES);
        $recentErrors = array_filter($errors, function($line) {
            return strpos($line, date('Y-m-d')) !== false && 
                   (strpos($line, 'ERROR') !== false || strpos($line, 'CRITICAL') !== false);
        });
        
        if (count($recentErrors) > 10) {
            $alerts[] = "High error count today: " . count($recentErrors);
        }
    }
    
    return $alerts;
}

function convertToBytes($value) {
    $unit = strtolower(substr($value, -1));
    $value = (int) $value;
    
    switch ($unit) {
        case 'g': return $value * 1024 * 1024 * 1024;
        case 'm': return $value * 1024 * 1024;
        case 'k': return $value * 1024;
        default: return $value;
    }
}

function sendAlert($message) {
    // Log alert
    error_log("CLINIC ALERT: " . $message);
    
    // Send email (if configured)
    // mail('admin@clinic.edu', 'Clinic System Alert', $message);
    
    // Write to monitoring file
    file_put_contents('storage/logs/monitoring.log', 
        date('Y-m-d H:i:s') . " - " . $message . "\n", 
        FILE_APPEND | LOCK_EX
    );
}

// Run monitoring
$alerts = checkSystemHealth();
foreach ($alerts as $alert) {
    sendAlert($alert);
}

// Log successful check
if (empty($alerts)) {
    file_put_contents('storage/logs/monitoring.log', 
        date('Y-m-d H:i:s') . " - System health check passed\n", 
        FILE_APPEND | LOCK_EX
    );
}
?>
```

---

## 🗄️ **DATABASE MAINTENANCE**

### **Regular Optimization**
```sql
-- SQLite maintenance commands
VACUUM;                    -- Reclaim unused space
REINDEX;                   -- Rebuild indexes
ANALYZE;                   -- Update query planner statistics
PRAGMA integrity_check;    -- Check database integrity
PRAGMA optimize;           -- Optimize database
```

### **Backup Procedures**
```bash
#!/bin/bash
# Database backup script

BACKUP_DIR="/backups/clinic"
DB_FILE="/var/www/clinic/database/clinic.sqlite"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Hot backup (SQLite)
sqlite3 "$DB_FILE" ".backup $BACKUP_DIR/clinic_backup_$DATE.sqlite"

# Verify backup integrity
sqlite3 "$BACKUP_DIR/clinic_backup_$DATE.sqlite" "PRAGMA integrity_check;"

# Compress backup
gzip "$BACKUP_DIR/clinic_backup_$DATE.sqlite"

# Clean old backups (keep 30 days)
find "$BACKUP_DIR" -name "*.gz" -mtime +30 -delete

echo "Database backup completed: $DATE"
```

### **Data Archival**
```sql
-- Archive old queue tickets (older than 6 months)
CREATE TABLE IF NOT EXISTS queue_tickets_archive AS 
SELECT * FROM queue_tickets WHERE created_at < DATE('now', '-6 months');

DELETE FROM queue_tickets WHERE created_at < DATE('now', '-6 months');

VACUUM;
```

---

## 🔐 **SECURITY MAINTENANCE**

### **Security Audit Checklist**
- [ ] Review user accounts and permissions
- [ ] Check for unused accounts
- [ ] Verify password policies
- [ ] Review failed login attempts
- [ ] Check file permissions
- [ ] Scan for vulnerabilities
- [ ] Update security headers
- [ ] Review access logs

### **User Account Management**
```php
// Disable inactive users (90+ days)
$stmt = $pdo->prepare("
    UPDATE users 
    SET status = 'inactive' 
    WHERE last_login < DATE('now', '-90 days') 
    AND status = 'active'
");

// Generate user activity report
$stmt = $pdo->query("
    SELECT 
        role,
        COUNT(*) as total_users,
        COUNT(CASE WHEN last_login > DATE('now', '-30 days') THEN 1 END) as active_users,
        COUNT(CASE WHEN last_login < DATE('now', '-90 days') THEN 1 END) as inactive_users
    FROM users 
    GROUP BY role
");
```

### **Log Analysis**
```bash
# Check for suspicious activity
grep -i "failed\|error\|hack\|attack" storage/logs/clinic.log

# Monitor login attempts
grep "login attempt" storage/logs/clinic.log | tail -100

# Check for unusual patterns
awk '{print $1}' access.log | sort | uniq -c | sort -nr | head -20
```

---

## 🚀 **PERFORMANCE OPTIMIZATION**

### **Database Performance**
```sql
-- Add indexes for frequently queried columns
CREATE INDEX IF NOT EXISTS idx_patients_student_number ON patients(student_number);
CREATE INDEX IF NOT EXISTS idx_queue_tickets_status ON queue_tickets(status);
CREATE INDEX IF NOT EXISTS idx_queue_tickets_created_at ON queue_tickets(created_at);
CREATE INDEX IF NOT EXISTS idx_appointments_date ON appointments(appointment_date);

-- Update table statistics
ANALYZE;
```

### **File System Optimization**
```bash
# Clean temporary files
find storage/framework/cache/ -type f -mtime +7 -delete
find storage/logs/ -name "*.log" -mtime +30 -delete

# Optimize file permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage/ bootstrap/cache/
```

### **Web Server Optimization**
```nginx
# Nginx performance tuning
location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    access_log off;
}

location ~* \.(woff|woff2|ttf|eot)$ {
    expires 1y;
    add_header Cache-Control "public";
    access_log off;
}

# Enable gzip compression
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/plain text/css application/json application/javascript;
```

---

## 🔄 **UPDATE PROCEDURES**

### **Application Updates**
```bash
# Backup before update
./backup.sh

# Update application code
git pull origin main

# Clear caches
rm -rf bootstrap/cache/*
rm -rf storage/framework/cache/*

# Run migrations (if any)
php artisan migrate

# Test application
curl -f http://localhost:8000/health-check
```

### **Security Updates**
```bash
# Update PHP
sudo apt update && sudo apt upgrade php8.2*

# Update web server
sudo apt upgrade nginx

# Update system packages
sudo apt update && sudo apt upgrade

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

---

## 🚨 **TROUBLESHOOTING GUIDE**

### **Common Issues and Solutions**

#### **Database Locked Error**
```bash
# Check for long-running queries
ps aux | grep sqlite

# Kill problematic processes
sudo pkill -f sqlite

# Restart web server
sudo systemctl restart nginx php8.2-fpm
```

#### **High Memory Usage**
```bash
# Check PHP processes
ps aux --sort=-%mem | grep php

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Check for memory leaks
valgrind --tool=memcheck php script.php
```

#### **Slow Response Times**
```sql
-- Check for missing indexes
.schema queue_tickets
EXPLAIN QUERY PLAN SELECT * FROM queue_tickets WHERE status = 'waiting';

-- Optimize database
VACUUM;
ANALYZE;
```

#### **Session Issues**
```bash
# Clear session files
rm -rf storage/framework/sessions/*

# Check session permissions
ls -la storage/framework/sessions/
chmod 775 storage/framework/sessions/
```

---

## 📊 **REPORTING AND ANALYTICS**

### **System Usage Report**
```php
<?php
// Generate monthly usage report

function generateUsageReport($month = null) {
    global $pdo;
    
    $month = $month ?: date('Y-m');
    
    $report = [
        'period' => $month,
        'generated_at' => date('Y-m-d H:i:s'),
    ];
    
    // Patient registrations
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM patients 
        WHERE strftime('%Y-%m', created_at) = ?
    ");
    $stmt->execute([$month]);
    $report['new_patients'] = $stmt->fetchColumn();
    
    // Queue statistics
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_tickets,
            AVG(CASE WHEN completed_at IS NOT NULL THEN 
                (strftime('%s', completed_at) - strftime('%s', created_at)) / 60.0 
            END) as avg_service_time,
            COUNT(CASE WHEN priority = 0 THEN 1 END) as emergency_tickets
        FROM queue_tickets 
        WHERE strftime('%Y-%m', created_at) = ?
    ");
    $stmt->execute([$month]);
    $report['queue_stats'] = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // User activity
    $stmt = $pdo->prepare("
        SELECT 
            role,
            COUNT(*) as active_users
        FROM users 
        WHERE strftime('%Y-%m', last_login) = ?
        GROUP BY role
    ");
    $stmt->execute([$month]);
    $report['user_activity'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $report;
}

// Generate and save report
$report = generateUsageReport();
file_put_contents(
    'storage/reports/usage_' . date('Y_m') . '.json', 
    json_encode($report, JSON_PRETTY_PRINT)
);
?>
```

### **Performance Metrics**
```bash
# Server response time monitoring
#!/bin/bash

URL="http://localhost:8000"
LOG_FILE="storage/logs/performance.log"

while true; do
    RESPONSE_TIME=$(curl -o /dev/null -s -w '%{time_total}' $URL)
    TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S')
    
    echo "$TIMESTAMP - Response time: ${RESPONSE_TIME}s" >> $LOG_FILE
    
    # Alert if response time > 5 seconds
    if (( $(echo "$RESPONSE_TIME > 5.0" | bc -l) )); then
        echo "ALERT: Slow response time: ${RESPONSE_TIME}s" >> $LOG_FILE
    fi
    
    sleep 300  # Check every 5 minutes
done
```

---

## 📞 **EMERGENCY PROCEDURES**

### **System Down Checklist**
1. **Check server status**
   ```bash
   systemctl status nginx
   systemctl status php8.2-fpm
   ```

2. **Check error logs**
   ```bash
   tail -f /var/log/nginx/error.log
   tail -f storage/logs/clinic.log
   ```

3. **Restart services**
   ```bash
   sudo systemctl restart nginx
   sudo systemctl restart php8.2-fpm
   ```

4. **Check database**
   ```bash
   sqlite3 database/clinic.sqlite "PRAGMA integrity_check;"
   ```

5. **Restore from backup** (if needed)
   ```bash
   cp /backups/clinic/latest_backup.sqlite database/clinic.sqlite
   ```

### **Contact Information**
- **Primary Administrator**: admin@clinic.edu
- **Database Administrator**: dba@clinic.edu  
- **Network Operations**: noc@university.edu
- **Emergency Hotline**: +1-xxx-xxx-xxxx

---

**🔧 Regular maintenance ensures optimal performance and reliability of your University Clinic Management System!**
