<?php
$steps = 0;

// Load dependencies
require './vendor/autoload.php';
++$steps;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Create log
$log = new Logger("LogWorkerDB");
// Define logs location
$log->pushHandler(new StreamHandler("../logs/WorkerDB.log", Level::Error)); 
++$steps;

// Read from miConf.ini (assuming the file exists and contains db credentials)
$config = parse_ini_file('../miConf.ini');
$db = [
    "host" => $config['host'],
    "user" => $config['user'],
    "pwd" => $config['pwd'],
    "db_name" => $config['db_name']
];

try {
    // Connect to the database
    $mysqli = new mysqli($db["host"], $db["root"], $db[""], $db["worker"]); // 4 db
    
    // Log connection success
    $log->info("Connection successfully to the database: " . $db["db_name"]);
    ++$steps;

    // Create operation
    $sql_sentence = "INSERT INTO worker(dni, name, surname, salary, phone) 
                     VALUES('71111111D', 'Juan', 'González', 20000, '93500202')";

    try {
        $result = $mysqli->query($sql_sentence);
        
        // Log successful insertion
        $log->info("Record inserted successfully: DNI '71111111D', Name 'Juan González'");
        ++$steps;
    } catch (mysqli_sql_exception $e) {
        // Log error message for failed insert
        $log->error("Error inserting a record: " . $e->getMessage());
        ++$steps;
    }
} catch (mysqli_sql_exception $e) {
    // Log error message for failed connection
    $log->error("Error connection db: " . $e->getMessage() . " - Host: " . $db["host"] . " - User: " . $db["user"]);
    ++$steps;
}

echo "Steps executed correctly: " . $steps;
