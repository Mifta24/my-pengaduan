# 🚀 Production Deployment Checklist

## ✅ Pre-Deployment Checklist

### **1. Environment Configuration**

- [ ] **.env Variables Set:**
  ```bash
  FIREBASE_PROJECT_ID=mypengaduan
  FIREBASE_CREDENTIALS=firebase/firebase-credentials.json
  QUEUE_CONNECTION=database  # or redis (recommended)
  ```

- [ ] **Firebase Credentials:**
  ```bash
  # Check file exists
  ls -la storage/app/firebase/firebase-credentials.json
  
  # Check permissions (should be 600)
  chmod 600 storage/app/firebase/firebase-credentials.json
  
  # Verify not in git
  cat .gitignore | grep firebase-credentials.json
  ```

- [ ] **Database Tables:**
  ```bash
  # Run migrations
  php artisan migrate
  
  # Verify tables created
  php artisan tinker --execute="echo 'user_devices: ' . \DB::table('user_devices')->count() . PHP_EOL; echo 'notification_settings: ' . \DB::table('notification_settings')->count() . PHP_EOL;"
  ```

---

### **2. Dependencies & Packages**

- [ ] **Firebase SDK Installed:**
  ```bash
  composer show kreait/laravel-firebase
  # Should show: ^6.1
  ```

- [ ] **Config Published:**
  ```bash
  ls -la config/firebase.php
  # Should exist
  ```

---

### **3. Code Verification**

- [ ] **Events Registered:**
  ```bash
  php artisan event:list
  # Should show:
  # - ComplaintCreated → SendComplaintNotificationToAdmin
  # - ComplaintStatusChanged → SendStatusChangeNotificationToUser
  # - AnnouncementCreated → SendAnnouncementNotificationToAll
  ```

- [ ] **Routes Registered:**
  ```bash
  php artisan route:list --path=api/device-tokens
  php artisan route:list --path=api/notifications
  # Should show all 8 API endpoints
  ```

- [ ] **Controllers Updated:**
  - [ ] `Api/ComplaintController.php` dispatches `ComplaintCreated`
  - [ ] `Admin/ComplaintController.php` dispatches `ComplaintStatusChanged`
  - [ ] `Admin/AnnouncementController.php` dispatches `AnnouncementCreated`

---

### **4. Testing**

- [ ] **Firebase Connection:**
  ```bash
  php test-firebase.php
  # Should show: ✅ Firebase Service: CONFIGURED
  ```

- [ ] **Queue Worker Test:**
  ```bash
  php artisan queue:work --once
  # Should start without errors
  ```

- [ ] **No Failed Jobs:**
  ```bash
  php artisan queue:failed
  # Should be empty
  ```

---

## 🔧 Production Setup

### **STEP 1: Install Supervisor**

```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install supervisor

# Check installation
supervisorctl version
```

---

### **STEP 2: Create Supervisor Config**

```bash
sudo nano /etc/supervisor/conf.d/mypengaduan-worker.conf
```

**Paste this config (ADJUST PATHS!):**
```ini
[program:mypengaduan-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/mypengaduan/artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=60
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/mypengaduan/storage/logs/worker.log
stopwaitsecs=3600
```

**Important: Replace `/var/www/mypengaduan` with your actual path!**

---

### **STEP 3: Configure Supervisor**

```bash
# Reread config files
sudo supervisorctl reread

# Update supervisor to use new config
sudo supervisorctl update

# Start workers
sudo supervisorctl start mypengaduan-worker:*

# Check status
sudo supervisorctl status mypengaduan-worker:*
```

**Expected output:**
```
mypengaduan-worker:mypengaduan-worker_00   RUNNING   pid 12345, uptime 0:00:05
mypengaduan-worker:mypengaduan-worker_01   RUNNING   pid 12346, uptime 0:00:05
```

---

### **STEP 4: Setup Cron Jobs**

```bash
crontab -e
```

**Add these lines:**
```bash
# Restart queue workers every hour (prevent memory leaks)
0 * * * * php /var/www/mypengaduan/artisan queue:restart >> /dev/null 2>&1

# Retry failed jobs daily at 2 AM
0 2 * * * php /var/www/mypengaduan/artisan queue:retry all >> /dev/null 2>&1

# Clean up old failed jobs monthly (keep only last 30 days)
0 0 1 * * php /var/www/mypengaduan/artisan queue:prune-failed --hours=720 >> /dev/null 2>&1

# Standard Laravel scheduler (if using)
* * * * * cd /var/www/mypengaduan && php artisan schedule:run >> /dev/null 2>&1
```

**Important: Replace `/var/www/mypengaduan` with your actual path!**

---

### **STEP 5: Configure Logging**

**Create log rotation:**
```bash
sudo nano /etc/logrotate.d/mypengaduan
```

**Paste:**
```
/var/www/mypengaduan/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
    sharedscripts
}
```

---

## 🧪 Post-Deployment Testing

### **Test 1: Queue Worker Running**

```bash
# Check Supervisor status
sudo supervisorctl status mypengaduan-worker:*

# Check process
ps aux | grep "queue:work"

# Check logs
tail -f /var/www/mypengaduan/storage/logs/worker.log
```

**Expected:** 2 queue:work processes running

---

### **Test 2: Create Test Complaint**

```bash
# Via cURL (replace TOKEN and URL)
curl -X POST https://your-domain.com/api/complaints \
  -H "Authorization: Bearer YOUR_USER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Production Notification",
    "description": "Testing queue worker in production",
    "category_id": 1,
    "location": "RT 01"
  }'
```

**Expected:**
1. Complaint created (API returns 201)
2. Queue processes job (check worker.log)
3. Admin receives FCM notification

---

### **Test 3: Update Complaint Status**

```bash
# Via web admin panel:
# 1. Login as admin
# 2. Open complaint detail
# 3. Change status
# 4. Submit

# Check logs
tail -f /var/www/mypengaduan/storage/logs/laravel.log | grep "Status change notification"
```

**Expected:**
1. Status updated
2. Queue processes job
3. User receives FCM notification

---

### **Test 4: Create Announcement**

```bash
# Via web admin panel:
# 1. Login as admin
# 2. Create new announcement
# 3. Submit

# Monitor queue
watch -n 2 'php artisan queue:monitor'
```

**Expected:**
1. Announcement created
2. Queue processes job
3. All users receive FCM notification

---

## 📊 Monitoring Commands

### **Queue Status**

```bash
# Real-time queue monitoring
php artisan queue:monitor

# Check pending jobs
php artisan tinker --execute="echo 'Pending: ' . \DB::table('jobs')->count() . PHP_EOL;"

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

---

### **Supervisor Management**

```bash
# Check status
sudo supervisorctl status mypengaduan-worker:*

# Stop workers
sudo supervisorctl stop mypengaduan-worker:*

# Start workers
sudo supervisorctl start mypengaduan-worker:*

# Restart workers
sudo supervisorctl restart mypengaduan-worker:*

# View logs
sudo tail -f /var/www/mypengaduan/storage/logs/worker.log
```

---

### **Application Logs**

```bash
# Watch all logs
tail -f /var/www/mypengaduan/storage/logs/laravel.log

# Filter notification logs
tail -f /var/www/mypengaduan/storage/logs/laravel.log | grep -i notification

# Watch queue worker logs
tail -f /var/www/mypengaduan/storage/logs/worker.log

# Check for errors only
tail -f /var/www/mypengaduan/storage/logs/laravel.log | grep ERROR
```

---

### **Database Monitoring**

```sql
-- Pending jobs
SELECT COUNT(*) as pending_jobs FROM jobs;

-- Failed jobs in last 24 hours
SELECT COUNT(*) as failed_jobs 
FROM failed_jobs 
WHERE failed_at > NOW() - INTERVAL '24 hours';

-- Active device tokens by type
SELECT 
    device_type,
    COUNT(*) as total,
    COUNT(CASE WHEN is_active = true THEN 1 END) as active
FROM user_devices
GROUP BY device_type;

-- Users with notification settings disabled
SELECT 
    u.name,
    u.email,
    ns.complaint_updates,
    ns.admin_announcements
FROM users u
JOIN notification_settings ns ON u.id = ns.user_id
WHERE ns.complaint_updates = false 
   OR ns.admin_announcements = false;
```

---

## 🚨 Troubleshooting

### **Issue 1: Queue Worker Not Running**

**Symptoms:**
- Notifications not sent
- Jobs piling up in database
- No process when running `ps aux | grep queue:work`

**Solutions:**
```bash
# Check Supervisor status
sudo supervisorctl status mypengaduan-worker:*

# If not running, start it
sudo supervisorctl start mypengaduan-worker:*

# If still failing, check logs
sudo tail -50 /var/www/mypengaduan/storage/logs/worker.log

# Restart supervisor
sudo systemctl restart supervisor
```

---

### **Issue 2: Jobs Failing**

**Symptoms:**
- Failed jobs in queue
- Errors in logs
- Notifications not delivered

**Solutions:**
```bash
# Check failed jobs
php artisan queue:failed

# View specific failed job
php artisan tinker
>>> $job = \DB::table('failed_jobs')->latest()->first();
>>> echo $job->exception;

# Common fixes:
# 1. Invalid FCM token → Auto-deactivated, no action needed
# 2. Firebase credentials missing → Check firebase-credentials.json
# 3. Syntax error → Check recent code changes

# Retry failed jobs after fixing
php artisan queue:retry all
```

---

### **Issue 3: Firebase Not Configured**

**Symptoms:**
- Logs show "Firebase not configured"
- isConfigured = false

**Solutions:**
```bash
# Check firebase-credentials.json exists
ls -la storage/app/firebase/firebase-credentials.json

# Check .env variables
cat .env | grep FIREBASE

# Test Firebase connection
php test-firebase.php

# If file missing, get from Firebase Console:
# 1. Go to Firebase Console
# 2. Project Settings → Service Accounts
# 3. Generate New Private Key
# 4. Download as firebase-credentials.json
# 5. Place in storage/app/firebase/
```

---

### **Issue 4: High Memory Usage**

**Symptoms:**
- Worker process using too much memory
- Server slowing down

**Solutions:**
```bash
# Add memory limit to Supervisor config
command=php /var/www/mypengaduan/artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --memory=128

# Restart workers more frequently (cron)
*/30 * * * * php /var/www/mypengaduan/artisan queue:restart

# Monitor memory usage
watch -n 5 'ps aux | grep queue:work | grep -v grep'
```

---

### **Issue 5: Notifications Delayed**

**Symptoms:**
- Notifications arrive late
- Queue processing slow

**Solutions:**
```bash
# Increase number of workers (Supervisor config)
numprocs=4  # Increase from 2 to 4

# Use Redis instead of database queue (.env)
QUEUE_CONNECTION=redis

# Install Redis if not installed
sudo apt-get install redis-server
sudo systemctl start redis
sudo systemctl enable redis

# Update supervisor config after changes
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart mypengaduan-worker:*
```

---

## 📈 Performance Optimization

### **1. Use Redis for Queue**

**Why:** Much faster than database queue

```bash
# Install Redis
sudo apt-get install redis-server

# Update .env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Restart queue workers
sudo supervisorctl restart mypengaduan-worker:*
```

---

### **2. Scale Queue Workers**

**Supervisor config:**
```ini
# Increase based on traffic
numprocs=4  # For high traffic
numprocs=2  # For medium traffic (default)
numprocs=1  # For low traffic
```

---

### **3. Configure Queue Priorities**

```bash
# High priority for critical notifications
php artisan queue:work database --queue=high,default,low
```

**Update code to use priorities:**
```php
// High priority (status changes, urgent announcements)
event(new ComplaintStatusChanged($complaint, $oldStatus, $newStatus))
    ->onQueue('high');

// Normal priority (new complaints)
event(new ComplaintCreated($complaint));

// Low priority (batch operations)
event(new AnnouncementCreated($announcement))
    ->onQueue('low');
```

---

### **4. Batch Processing**

Already implemented in `SendAnnouncementNotificationToAll` listener using `sendToMultipleDevices()`.

---

## 🎯 Success Metrics

After deployment, monitor these metrics:

- [ ] **Queue Health:**
  - Pending jobs < 100
  - Failed job rate < 1%
  - Average processing time < 5 seconds

- [ ] **Notification Delivery:**
  - Delivery success rate > 95%
  - Average delivery time < 10 seconds
  - No silent failures

- [ ] **System Stability:**
  - Queue workers uptime > 99%
  - No memory leaks
  - No CPU spikes

- [ ] **User Engagement:**
  - Device registration rate > 80%
  - Notification open rate (track in future)
  - User satisfaction (track in future)

---

## ✅ Final Checklist

- [ ] Supervisor configured and running
- [ ] Cron jobs configured
- [ ] Log rotation configured
- [ ] All tests passed
- [ ] Firebase credentials in place
- [ ] Environment variables set
- [ ] Queue workers running (2+)
- [ ] Monitoring commands bookmarked
- [ ] Troubleshooting guide reviewed
- [ ] Team trained on monitoring
- [ ] Mobile app ready to integrate
- [ ] Documentation shared with team

---

## 📞 Support

**Quick Commands:**
```bash
# Essential monitoring commands
sudo supervisorctl status                    # Supervisor status
php artisan queue:monitor                     # Queue status
tail -f storage/logs/laravel.log | grep notif  # Notification logs
php artisan queue:failed                      # Failed jobs

# Emergency commands
sudo supervisorctl restart mypengaduan-worker:*  # Restart workers
php artisan queue:retry all                      # Retry failed jobs
php artisan cache:clear                          # Clear cache
php artisan config:clear                         # Clear config
```

---

**🎉 You're Production Ready!**

Follow this checklist step by step. After deployment, monitor logs for the first 24 hours to catch any issues early.

**Questions?** Check documentation files:
- NOTIFICATION_SUMMARY.md
- NOTIFICATION_DAY2_COMPLETE.md
- NOTIFICATION_QUICK_TEST.md
- TESTING_GUIDE.md

**Good luck! 🚀**
