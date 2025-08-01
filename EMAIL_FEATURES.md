# 📧 Email Notification Features

## Overview
The system now includes enhanced email notifications that send a brief summary to administrators when users submit reports.

## Features

### 📊 Brief Summary Email
- **Sender Information**: Name, email, and role of the person submitting the report
- **Report Summary**: Key details including company, work type, task type, and visit status
- **File Information**: Image count, total file size, and signature status
- **Quick Stats**: Visual indicators for attachments and file sizes

### 📧 Email Format
- **Subject Line**: `📋 レポート送信通知 - [Company Name] ([Sender Name])`
- **HTML Version**: Rich formatted email with summary section and detailed information
- **Text Version**: Plain text version for better email client compatibility

### 🔧 Technical Implementation

#### Email Template Structure
```
📊 レポート概要
├── 送信者情報 (Sender Info)
├── 基本情報 (Basic Info)
├── 添付ファイル統計 (Attachment Stats)
└── 詳細情報 (Detailed Info)
```

#### Summary Data Structure
```php
$summary = [
    'sender' => [
        'name' => 'User Name',
        'email' => 'user@example.com',
        'role' => 'User'
    ],
    'report' => [
        'id' => 123,
        'company' => 'Company Name',
        'work_type' => 'Work Type',
        'task_type' => 'Task Type',
        'visit_status' => 'Visit Status',
        'created_at' => '2025年08月01日 06:35',
        'image_count' => 3,
        'has_signature' => true,
        'total_size' => '1.8 MB'
    ],
    'quick_info' => [
        'person' => 'Contact Person',
        'site' => 'Site Name',
        'store' => 'Store Name',
        'start_time' => '09:00',
        'end_time' => '17:00'
    ]
];
```

## Commands

### Test Email Notification
```bash
# Test with latest report
php artisan test:email-notification

# Test with specific report
php artisan test:email-notification --report-id=11
```

### Preview Email Summary
```bash
# Preview latest report summary
php artisan email:preview-summary

# Preview specific report summary
php artisan email:preview-summary --report-id=11
```

## Email Templates

### HTML Template
- **File**: `resources/views/emails/report_submitted.blade.php`
- **Features**: 
  - Responsive design
  - Summary section with key information
  - Detailed report sections
  - Embedded images and signatures
  - Action buttons for dashboard and edit links

### Text Template
- **File**: `resources/views/emails/report_submitted_text.blade.php`
- **Features**:
  - Plain text format
  - Compatible with all email clients
  - Same information as HTML version
  - Clear section separators

## Email Content

### Summary Section
- **Sender Details**: Name, email, submission time
- **Report ID**: Unique identifier
- **Key Information**: Company, contact person, work details
- **Attachment Stats**: Image count, file size, signature status

### Detailed Sections
- **Basic Information**: Company, contact person, site/store details
- **Work Information**: Work type, task type, request details
- **Time Information**: Start and end times
- **Visit Information**: Status, location, conditions
- **Work Details**: Detailed work description
- **Attachments**: Images and signatures (if any)

## Error Handling

### Email Sending
- **Try-Catch Block**: Prevents email failures from affecting report submission
- **Logging**: Comprehensive logging of success and failure cases
- **Fallback**: Report submission continues even if email fails

### Log Messages
```php
// Success
\Log::info('Report notification sent successfully', [
    'report_id' => $report->id,
    'admin_emails' => $adminEmails,
    'user_id' => auth()->id(),
    'company' => $report->company
]);

// Warning - No admin emails
\Log::warning('No admin emails found for report notification', [
    'report_id' => $report->id,
    'user_id' => auth()->id()
]);

// Error - Email sending failed
\Log::error('Failed to send report notification email', [
    'report_id' => $report->id,
    'error' => $e->getMessage(),
    'user_id' => auth()->id()
]);
```

## Benefits

### For Administrators
- **Quick Overview**: Brief summary shows key information at a glance
- **Sender Identification**: Clear identification of who submitted the report
- **File Information**: Immediate knowledge of attachments and file sizes
- **Direct Access**: Links to dashboard and edit pages

### For Users
- **Confirmation**: Clear confirmation that report was submitted successfully
- **Notification**: Assurance that administrators are notified
- **No Interruption**: Email failures don't affect report submission

### For System
- **Reliability**: Robust error handling ensures system stability
- **Compatibility**: Both HTML and text versions for all email clients
- **Logging**: Comprehensive logging for debugging and monitoring
- **Scalability**: Efficient email generation and sending

## Future Enhancements

### Potential Improvements
- **Email Templates**: Additional template variations
- **Attachment Limits**: Email size optimization
- **Scheduling**: Delayed email sending options
- **Customization**: User-configurable email preferences
- **Notifications**: SMS or push notification integration 