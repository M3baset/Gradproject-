<?php
// Database connection parameters
$host = 'aws-0-eu-central-1.pooler.supabase.com';
$port = '5432';
$dbname = 'postgres';
$user = 'postgres.czytuqnowdogaowfnfhe';
$password = '235711';

// Get JSON data from POST request
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo "Invalid JSON";
    exit();
}

// Connect to PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo "Database connection failed!";
    exit();
}

// Insert data into table
$query = "INSERT INTO gas_readings (co2, nh3, alcohol, toluene, acetone, reading_time)
          VALUES ($1, $2, $3, $4, $5, NOW())";

$result = pg_query_params($conn, $query, array(
    $data['co2'],
    $data['nh3'],
    $data['alcohol'],
    $data['toluene'],
    $data['acetone']
));

if ($result) {
    echo "Data inserted successfully!";
} else {
    http_response_code(500);
    echo "Failed to insert data!";
}

pg_close($conn);
?>
