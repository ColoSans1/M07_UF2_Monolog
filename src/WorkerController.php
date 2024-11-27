<?php
$steps = 0;

// Cargar dependencias
require './vendor/autoload.php';
++$steps;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Crear log
$log = new Logger("LogWorkerDB");
// Definir ubicación de los logs
$log->pushHandler(new StreamHandler("../logs/WorkerDB.log", Level::Error)); 
++$steps;

// Leer desde el archivo miConf.ini (asumiendo que el archivo existe y contiene las credenciales de la base de datos)
$config = parse_ini_file(__DIR__ . "/../conf/miConf.ini", true);

if ($config === false) {
    $log->error("No se pudo cargar el archivo de configuración miConf.ini.");
    die("Error al cargar el archivo de configuración.");
}

// Obtener valores de configuración
$db = [
    "host" => $config['params_db_sql']['host'],
    "port" => $config['params_db_sql']['port'],
    "user" => $config['params_db_sql']['user'],
    "pwd" => $config['params_db_sql']['pwd'],
    "db_name" => $config['params_db_sql']['db_name']
];

try {
// Conectar a la base de datos
$mysqli = new mysqli($db["host"], $db["user"], $db["pwd"], $db["db_name"], $db["port"]);

    if ($mysqli->connect_error) {
    $log->error("Connection failed: " . $mysqli->connect_error);
    die("Connection failed: " . $mysqli->connect_error);  
}

    
    // Log de éxito en la conexión
    $log->info("Conexión exitosa a la base de datos: " . $db["db_name"]);
    ++$steps;

    // Crear sentencia de operación (inserción de datos)
    $sql_sentence = "INSERT INTO worker(dni, name, surname, salary, phone) 
                     VALUES('71111111D', 'Juan', 'González', 20000, '93500202')";

    try {
        $result = $mysqli->query($sql_sentence);

        // Log de inserción exitosa
        $log->info("Registro insertado exitosamente: DNI '71111111D', Nombre 'Juan González'");
        ++$steps;
    } catch (mysqli_sql_exception $e) {
        // Log de error en la inserción
        $log->error("Error al insertar un registro: " . $e->getMessage());
        ++$steps;
    }
} catch (mysqli_sql_exception $e) {
    // Log de error en la conexión
    $log->error("Error de conexión a la base de datos: " . $e->getMessage() . " - Host: " . $db["host"] . " - Usuario: " . $db["user"]);
    ++$steps;
}

echo "Pasos ejecutados correctamente: " . $steps;
