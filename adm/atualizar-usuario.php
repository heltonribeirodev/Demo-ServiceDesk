<?php
session_start();

$caminho_conexao = __DIR__ . '/../config/conexao.php';
if (!file_exists($caminho_conexao)) {
    die("Erro Crítico: Arquivo de conexão não encontrado.");
}
include_once $caminho_conexao;

// 1. Segurança: Verifica se está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

$perfil_logado = $_SESSION['perfil'];

// Verifica se tem permissão mínima (Admin ou Suporte)
if (!in_array($perfil_logado, ['Admin', 'Suporte'])) {
    header("Location: ../adm-painel.php?page=usuario&msg=erro_permissao");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $setor = isset($_POST['setor']) ? $_POST['setor'] : '';
    $perfil_alvo = isset($_POST['perfil']) ? $_POST['perfil'] : ''; // Perfil que vem do form
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

    // --- NOVA TRAVA DE SEGURANÇA ---
    // Se o usuário logado for 'Suporte' e tentar definir o perfil como 'Admin'
    if ($perfil_logado === 'Suporte' && $perfil_alvo === 'Admin') {
        header("Location: ../adm-painel.php?page=usuarios&msg=erro_permissao_admin");
        exit;
    }
    // -------------------------------

    if ($id === 0 || empty($nome) || empty($email)) {
        die("Erro: Dados obrigatórios faltando.");
    }

    if (!empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nome=?, email=?, setor=?, perfil=?, senha=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nome, $email, $setor, $perfil_alvo, $senhaHash, $id);
    } else {
        $sql = "UPDATE usuarios SET nome=?, email=?, setor=?, perfil=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nome, $email, $setor, $perfil_alvo, $id);
    }

    if ($stmt->execute()) {
        header("Location: ../adm-painel.php?page=usuarios&msg=sucesso");
        exit;
    } else {
        die("Erro ao atualizar: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}