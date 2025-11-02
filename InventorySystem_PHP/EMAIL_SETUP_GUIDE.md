# Email Configuration Guide for XAMPP

## The Problem

PHP's `mail()` function **doesn't work on XAMPP by default** because:
1. XAMPP doesn't include a mail server
2. No SMTP server is configured
3. Windows doesn't have sendmail like Linux

That's why you see: *"However, email could not be sent to supplier"*

---

## Solutions

### ✅ Solution 1: Quick Test - Use Gmail SMTP (Temporary)

**For Testing Only** - Edit `php.ini`:

1. Open: `C:\xampp\php\php.ini`
2. Find `[mail function]` section
3. Uncomment and modify:
   ```ini
   [mail function]
   SMTP = smtp.gmail.com
   smtp_port = 587
   sendmail_from = your-email@gmail.com
   ```
4. Restart Apache

**⚠️ Note:** Gmail requires "App Passwords" for this to work, and it's not recommended for production.

---

### ✅ Solution 2: Use PHPMailer (RECOMMENDED - Best Solution)

PHPMailer is a library that properly handles SMTP email sending through Gmail, Outlook, etc.

**Advantages:**
- ✅ Works with Gmail, Outlook, Yahoo, etc.
- ✅ Better error handling
- ✅ More reliable
- ✅ Works on XAMPP and production servers
- ✅ Supports authentication

**I can set this up for you automatically if you want!**

---

### ✅ Solution 3: Use Production Server

When you deploy to a live server:
- Most hosting providers (cPanel, shared hosting) have mail() configured
- Emails will work automatically
- No additional setup needed

---

## How to Check Current Status

1. Visit: `http://localhost/Inventory-Management-System/InventorySystem_PHP/test_email_config.php`
2. This will show you:
   - What's configured
   - What's missing
   - Test email sending

---

## Quick Fix for Now

If you need emails to work **right now**, here are your options:

1. **I can install PHPMailer for you** (takes 2 minutes)
2. **Configure XAMPP sendmail** (more complex)
3. **Wait until you deploy to production server** (emails will work automatically)

---

## What I Recommend

**Let me set up PHPMailer for you!** It's the best long-term solution and will work both locally and in production.

Just tell me:
- Your Gmail address (or other email provider)
- Or I can set it up to use the email from `functions.php` (nuwaniprabhashi2003@gmail.com)

---

## Current Email Configuration

Your current `send_email()` function uses:
- **From Email:** `nuwaniprabhashi2003@gmail.com`
- **Method:** PHP's `mail()` function (not working on XAMPP)

**This is why emails fail!** The `mail()` function needs a server that XAMPP doesn't have.

---

## Next Steps

1. Run the diagnostic tool: `test_email_config.php`
2. Choose a solution:
   - PHPMailer (recommended)
   - Wait for production server
   - Manual SMTP configuration

Would you like me to set up PHPMailer now?

