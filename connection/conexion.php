<?php
$host = "localhost";   // servidor
$usuario = "root";     // usuario por defecto en XAMPP
$password = "";        // contraseña vacía por defecto
$base_datos = "prod";  // nombre de tu base de datos

// Crear conexión
$conn = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>