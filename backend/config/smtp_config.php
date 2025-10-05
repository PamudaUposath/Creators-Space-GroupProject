<?php
// backend/config/smtp_config.php
// SMTP Configuration for sending emails

/**
 * Configure SMTP settings for different providers
 * Uncomment and configure the one you want to use
 */

// Gmail SMTP Configuration (Recommended)
function configureGmailSMTP() {
    // You need to:
    // 1. Enable 2-factor authentication in Gmail
    // 2. Generate an App Password
    // 3. Update the credentials below
    
    return [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => 'your-email@gmail.com', // Replace with your Gmail address
        'password' => 'your-app-password',     // Replace with your Gmail App Password
        'from_email' => 'your-email@gmail.com',
        'from_name' => 'Creators Space'
    ];
}

// Alternative: Outlook/Hotmail SMTP
function configureOutlookSMTP() {
    return [
        'host' => 'smtp-mail.outlook.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => 'your-email@outlook.com',
        'password' => 'your-password',
        'from_email' => 'your-email@outlook.com',
        'from_name' => 'Creators Space'
    ];
}

// Alternative: Mailtrap (for testing)
function configureMailtrapSMTP() {
    return [
        'host' => 'smtp.mailtrap.io',
        'port' => 2525,
        'encryption' => 'tls',
        'username' => 'your-mailtrap-username',
        'password' => 'your-mailtrap-password',
        'from_email' => 'test@example.com',
        'from_name' => 'Creators Space Test'
    ];
}

// Get the active SMTP configuration
function getSMTPConfig() {
    // Change this to use the SMTP provider you want
    // return configureGmailSMTP();
    // return configureOutlookSMTP();
    // return configureMailtrapSMTP();
    
    // For localhost testing - return null to use PHP mail() function
    return null;
}
?>