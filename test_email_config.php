<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "=== Email Configuration Test ===\n\n";

// Check current mail configuration
echo "📧 Current Mail Configuration:\n";
echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "MAIL_ENCRYPTION: " . config('mail.mailers.smtp.encryption') . "\n";
echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n\n";

// Test simple email sending
try {
    echo "🧪 Testing email configuration...\n";
    
    Mail::raw('This is a test email from the Work Report System.', function($message) {
        $message->to('goodsman207@gmail.com')
                ->subject('Test Email - Work Report System')
                ->from('kindman207@gmail.com', 'Work Report System');
    });
    
    echo "✅ Test email sent successfully!\n";
    echo "📧 Email sent to: goodsman207@gmail.com\n";
    echo "📧 From: kindman207@gmail.com\n\n";
    
    echo "If you received this email, your configuration is working correctly.\n";
    echo "If not, please check your .env file and Gmail app password settings.\n";
    
} catch (Exception $e) {
    echo "❌ Email test failed:\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    
    echo "🔧 Troubleshooting Tips:\n";
    echo "1. Make sure you've updated your .env file with the correct settings\n";
    echo "2. Verify your Gmail app password is correct\n";
    echo "3. Ensure 2-factor authentication is enabled on your Gmail account\n";
    echo "4. Check that your Gmail account allows less secure app access\n";
    echo "5. Try using Mailtrap for testing instead\n";
} 