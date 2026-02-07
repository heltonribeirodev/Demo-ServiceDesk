<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sistema_chamados";
$port = 3307; // Adicione a porta aqui se mudou no XAMPP

// Adicione a variável $port no final dos parâmetros
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>