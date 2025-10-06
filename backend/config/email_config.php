<?php
// Email configuration settings
// Change these settings according to your SMTP provider

return [
    // SMTP Settings
    'smtp_host' => 'smtp.gmail.com', // Change to your SMTP server
    'smtp_port' => 587,
    'smtp_secure' => 'tls', // 'tls' or 'ssl'
    'smtp_auth' => true,
    
    // SMTP Credentials (Update these with your actual credentials)
    'smtp_username' => '', // Change to your Gmail address
    'smtp_password' => '', // Change to your Gmail app password
    
    // Default sender
    'from_email' => 'noreply@creators-space.com',
    'from_name' => 'Creators-Space Team',
    
    // Email settings
    'charset' => 'UTF-8',
    'is_html' => false
];
?>