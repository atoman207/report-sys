# Admin Email Notification System

## Overview
This system sends email notifications to administrators when a user submits a report. The email includes the submitting user's email address and a brief description of the report.

## Administrator Emails
The following email addresses receive notifications for all report submissions:

- `daise2ac@ibaraki.email.ne.jp`
- `d2d_hachiouji@icloud.com`
- `daise2denko@themis.ocn.ne.jp`
- `goodsman@gmail.com`

## Email Content

### Subject Line
```
📋 レポート送信通知 - [Company Name] ([User Name])
```

### Email Structure

#### 1. Sender Information Section
- **Prominent display** of the submitting user's information
- User's name and email address
- User's role in the system
- Submission timestamp
- Report ID

#### 2. Report Summary Section
- Company name
- Work type and task type
- Visit status
- Number of attached images
- Signature status

#### 3. Detailed Report Information
- Basic company information
- Work details
- Time information
- Visit information
- Work details
- Attached images (if any)
- Signature (if any)

## Technical Implementation

### Files Modified

1. **`app/Http/Controllers/RequestController.php`**
   - Updated email sending logic to use hardcoded admin emails
   - Enhanced logging to include user email information

2. **`app/Mail/ReportSubmitted.php`**
   - Enhanced email template with prominent sender information
   - Added brief description section

3. **`resources/views/emails/report_submitted.blade.php`**
   - Added prominent sender information section with yellow background
   - Added brief description section with blue background
   - Enhanced visual hierarchy for better readability

4. **`resources/views/emails/report_submitted_text.blade.php`**
   - Updated plain text version to match HTML structure
   - Added clear sender information section

### Testing

Use the following command to test the email notification system:

```bash
php artisan test:admin-email
```

Or test with a specific report:

```bash
php artisan test:admin-email --report-id=13
```

## Email Features

### Visual Design
- **Sender Information**: Highlighted with yellow background and prominent styling
- **Report Summary**: Blue background with clear structure
- **Responsive Design**: Works on both desktop and mobile email clients
- **Professional Layout**: Clean, modern design with proper spacing

### Content Features
- **User Email Prominence**: The submitting user's email is prominently displayed
- **Brief Description**: Quick overview of the report contents
- **Detailed Information**: Complete report details for administrators
- **Attachment Information**: Clear indication of images and signatures
- **Action Links**: Direct links to view and edit the report

### Error Handling
- **Graceful Failure**: Email sending errors don't prevent report submission
- **Comprehensive Logging**: All email activities are logged for debugging
- **Fallback Support**: System continues to work even if email fails

## Configuration

### Adding/Removing Admin Emails
To modify the administrator email list, edit the `$adminEmails` array in:
`app/Http/Controllers/RequestController.php` (line ~90)

```php
$adminEmails = [
    'daise2ac@ibaraki.email.ne.jp',
    'd2d_hachiouji@icloud.com',
    'daise2denko@themis.ocn.ne.jp',
    'goodsman@gmail.com'
];
```

### Email Template Customization
- **HTML Template**: `resources/views/emails/report_submitted.blade.php`
- **Plain Text Template**: `resources/views/emails/report_submitted_text.blade.php`
- **Email Class**: `app/Mail/ReportSubmitted.php`

## Benefits

1. **Immediate Notification**: Administrators are notified instantly when reports are submitted
2. **User Identification**: Clear identification of who submitted the report
3. **Quick Overview**: Brief description allows for quick assessment
4. **Complete Information**: Full report details available in the email
5. **Professional Communication**: Well-designed email maintains professional appearance
6. **Reliable Delivery**: Robust error handling ensures system reliability

## Future Enhancements

- **Email Preferences**: Allow administrators to customize notification preferences
- **Digest Emails**: Option to receive daily/weekly summaries instead of individual emails
- **Email Templates**: Multiple template options for different types of reports
- **Attachment Handling**: Better handling of large image attachments
- **Reply Functionality**: Allow administrators to reply directly to the submitting user 