<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "sistema_gestion";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

session_start();  # Para manejo de sesiones