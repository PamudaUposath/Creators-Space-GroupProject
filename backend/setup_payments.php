<?php
/**
 * Setup script to create the payments table for PayHere integration
 */

require_once 'config/db_connect.php';

echo "Setting up payments table...\n";

try {
    // Read and execute the payments schema SQL
    $sql = file_get_contents('sql/payments_schema.sql');
    $pdo->exec($sql);
    
    echo "✅ Payments table created successfully!\n";
    
    // Also create logs directory if it doesn't exist
    if (!file_exists('../logs')) {
        mkdir('../logs', 0755, true);
        echo "✅ Logs directory created successfully!\n";
    }
    
    echo "\n🎉 PayHere integration setup complete!\n";
    echo "\n📋 Next steps:\n";
    echo "1. Update your PayHere merchant secret in checkout.php and notify.php\n";
    echo "2. Test the payment flow with sandbox credentials\n";
    echo "3. Update URLs for production environment\n";
    
} catch (PDOException $e) {
    echo "❌ Error setting up payments table: " . $e->getMessage() . "\n";
    exit(1);
}
?>