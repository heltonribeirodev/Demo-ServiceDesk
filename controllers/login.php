<?php
session_start();
include "../config/conexao.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$email = $_POST['email'] ?? '';
$senha = $_POST['password'] ?? '';

// O SELECT * já está correto, ele traz todas as colunas (id, nome, email, setor, perfil)
$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($senha, $user['senha'])) {

    // ✅ LOGIN OK - SALVANDO DADOS NA SESSÃO
    $_SESSION['id']     = $user['id'];
    $_SESSION['nome']   = $user['nome'];
    
    // As linhas abaixo foram adicionadas no lugar certo agora:
    $_SESSION['email']  = $user['email'];
    $_SESSION['setor']  = $user['setor'];
    $_SESSION['perfil'] = $user['perfil'];

    // Agora sim redireciona
    header("Location: ../inicio.php");
    exit;

} else {
    // ❌ LOGIN ERRADO
    $_SESSION['erro_login'] = "E-mail ou senha inválidos";
    header("Location: ../login.php");
    exit;
}
?>