<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Get JSON data from request body
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if ($data && 
    isset($data['heart_rate']) && 
    isset($data['spo2'])) 
{
    $heart_rate = $data['heart_rate'];
    $spo2 = $data['spo2'];

    // Database connection details (updated for Supabase)
    $host = 'aws-0-eu-central-1.pooler.supabase.com';
    $db = 'postgres';
    $user = 'postgres.czytuqnowdogaowfnfhe';
    $pass = '235711';
    $port = '5432';

    try {
        // Connect to the database
        $conn = new PDO("pgsql:host=$host;port=$port;dbname=$db;sslmode=require", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insert data into the database
        $stmt = $conn->prepare("
            INSERT INTO health_data (
                heart_rate, 
                spo2,
                timestamp
            ) VALUES (
                :heart_rate,
                :spo2,
                NOW()
            )
        ");
        $stmt->bindParam(':heart_rate', $heart_rate);
        $stmt->bindParam(':spo2', $spo2);
        $stmt->execute();

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); // Return real error message
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>

