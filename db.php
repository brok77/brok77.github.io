<?php
$host = 'localhost';
$db   = 'system_rezerwacji';
$user = 'root'; 
$pass = '';    
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
     $pdo = new PDO($dsn, $user, $pass);
     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
     die("Błąd połączenia: " . $e->getMessage());
}
?>
