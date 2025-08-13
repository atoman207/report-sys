# Email Configuration for kindman207@gmail.com

## Gmail SMTP Setup

### Step 1: Enable 2-Factor Authentication
1. Go to your Google Account settings
2. Navigate to Security
3. Enable 2-Step Verification if not already enabled

### Step 2: Generate App Password
1. Go to Google Account settings
2. Navigate to Security → 2-Step Verification
3. Scroll down to "App passwords"
4. Generate a new app password for "Mail"
5. Copy the 16-character password

### Step 3: Update .env File
Add these settings to your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=kindman207@gmail.com
MAIL_PASSWORD=your-16-character-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=kindman207@gmail.com
MAIL_FROM_NAME="Work Report System"
```

### Step 4: Clear Configuration Cache
Run these commands:
```bash
php artisan config:clear
php artisan cache:clear
```

### Step 5: Test Email Configuration
```bash
php artisan test:admin-email
```

## Alternative: Use Mailtrap for Testing

If you want to test without Gmail setup:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=kindman207@gmail.com
MAIL_FROM_NAME="Work Report System"
```

## Email Flow
- **From**: kindman207@gmail.com
- **To**: daise2ac@ibaraki.email.ne.jp, d2d_hachiouji@icloud.com, daise2denko@themis.ocn.ne.jp, goodsman@gmail.com
- **Subject**: 📋 レポート送信通知 - [Company Name] ([User Name])
- **Content**: Report details with sender information and brief description 