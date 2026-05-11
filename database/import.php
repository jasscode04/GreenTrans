<?php
/**
 * GreenTrans - Database Import Script
 * Run this once to create the database and tables
 */

try {
    // Connect without database name first
    $pdo = new PDO('mysql:host=localhost;port=3308', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "Connected to MySQL successfully.\n";
    
    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/greentrans.sql');
    
    // Split by semicolons and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $stmt) {
        if (!empty($stmt) && $stmt !== '') {
            try {
                $pdo->exec($stmt);
            } catch (PDOException $e) {
                // Skip duplicate key errors for re-runs
                if (strpos($e->getMessage(), 'Duplicate') === false && 
                    strpos($e->getMessage(), 'already exists') === false) {
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "\nDatabase 'greentrans' created and populated successfully!\n";
    echo "You can now login with:\n";
    echo "  Admin: admin@gmail.com / password\n";
    echo "  Manager: manager@gmail.com / password\n";
    echo "  Driver: driver1@gmail.com / password\n";
    echo "  Customer: customer@gmail.com / password\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
