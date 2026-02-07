<?php
session_start();
include_once __DIR__ . '/../config/conexao.php';

// 1. Verificação básica de Login
if (!isset($_SESSION['id'])) {
    header("Location: ../inicio.php");
    exit;
}

// 2. SEGURANÇA: Se não for Admin, bloqueia e avisa
if ($_SESSION['perfil'] !== 'Admin') {
    // MUDANÇA AQUI: Redireciona de volta para o painel com a mensagem de erro
    header("Location: ../adm-painel.php?page=chamados&msg=erro_permissao");
    exit;
}

// 3. Verifica se tem ID
if (!isset($_GET['id'])) {
    header("Location: ../adm-painel.php?page=chamados");
    exit;
}

$id = intval($_GET['id']);

// 4. Excluir Mensagens do Chat primeiro
$sql_chat = "DELETE FROM interacoes WHERE chamado_id = ?";
$stmt_chat = $conn->prepare($sql_chat);
$stmt_chat->bind_param("i", $id);
$stmt_chat->execute();
$stmt_chat->close();

// 5. Excluir o Chamado
$sql = "DELETE FROM chamados WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Sucesso
    header("Location: ../adm-painel.php?page=chamados&msg=excluido");
} else {
    // Erro no banco
    die("Erro ao excluir chamado: " . $conn->error);
}

$stmt->close();
$conn->close();
?>