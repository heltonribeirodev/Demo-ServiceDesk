<?php
session_start();

// AJUSTE 1: Caminho da conexão (baseado na sua pasta 'controllers')
require_once '../config/conexao.php'; 

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$senha = $_POST['senha'];

/* BUSCA O USUÁRIO NO BANCO */
// AJUSTE 2: Adicionei 'email' e 'setor' que estavam faltando na busca
$sql = "SELECT id, nome, email, senha, perfil, setor FROM usuarios WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

/* CONFERE LOGIN */
if ($usuario && password_verify($senha, $usuario['senha'])) {

    // 🔥 AQUI NASCE A SESSION
    $_SESSION['id']     = $usuario['id'];
    $_SESSION['nome']   = $usuario['nome'];
    $_SESSION['email']  = $usuario['email'];
    $_SESSION['setor']  = $usuario['setor'];
    
    // AJUSTE 3: Trim remove espaços invisíveis antes ou depois da palavra
    $_SESSION['perfil'] = trim($usuario['perfil']); 

    // Debug Temporário (Se continuar dando erro, descomente a linha abaixo para ver o que pegou)
    // echo "Logado como: " . $_SESSION['perfil']; exit;

    header("Location: inicio.php");
    exit;

} else {
    header("Location: login.php?erro=1");
    exit;
}
?>