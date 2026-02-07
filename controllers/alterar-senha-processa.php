<?php
session_start();
require_once "../config/conexao.php";

// 🔒 Verifica se está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

$idUsuario     = $_SESSION['id'];
$senhaAtual    = trim($_POST['senha_atual'] ?? '');
$novaSenha     = trim($_POST['nova_senha'] ?? '');
$confirmaSenha = trim($_POST['confirma_senha'] ?? '');

// 🔎 Validações básicas
if ($senhaAtual === '' || $novaSenha === '' || $confirmaSenha === '') {
    $_SESSION['msg_erro'] = "Preencha todos os campos.";
    header("Location: ../alterar-senha.php");
    exit;
}

if ($novaSenha !== $confirmaSenha) {
    $_SESSION['msg_erro'] = "A nova senha e a confirmação não coincidem.";
    header("Location: ../alterar-senha.php");
    exit;
}

if (strlen($novaSenha) < 6) {
    $_SESSION['msg_erro'] = "A nova senha deve ter no mínimo 6 caracteres.";
    header("Location: ../alterar-senha.php");
    exit;
}

// 🔍 Busca senha atual no banco
$sql = "SELECT senha FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    $_SESSION['msg_erro'] = "Usuário não encontrado.";
    header("Location: ../alterar-senha.php");
    exit;
}

// 🔐 Verifica se a senha atual está correta
if (!password_verify($senhaAtual, $usuario['senha'])) {
    $_SESSION['msg_erro'] = "Senha atual incorreta.";
    header("Location: ../alterar-senha.php");
    exit;
}

// 🔐 Gera novo hash
$novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

// 💾 Atualiza no banco
$update = "UPDATE usuarios SET senha = ? WHERE id = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("si", $novaSenhaHash, $idUsuario);

if ($stmt->execute()) {
    $_SESSION['msg_sucesso'] = "Senha alterada com sucesso!";
} else {
    $_SESSION['msg_erro'] = "Erro ao alterar a senha.";
}

header("Location: ../alterar-senha.php");
exit;
