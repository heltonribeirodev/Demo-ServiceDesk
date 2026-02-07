<?php
session_start();
include_once __DIR__ . '/../config/conexao.php';

// 1. Verificação básica de Login
if (!isset($_SESSION['id'])) {
    header("Location: ../inicio.php");
    exit;
}

// 2. SEGURANÇA: Se não for Admin, bloqueia e avisa
if (!in_array($_SESSION['perfil'], ['Admin', 'Suporte'])) {
    // MUDANÇA AQUI: Redireciona de volta para o painel com a mensagem de erro
    header("Location: ../adm-painel.php?page=patrimonio&msg=erro_permissao");
    exit;
}

// 2. VERIFICA SE TEM ID
if (isset($_GET['id'])) {
    
    // Converte para inteiro para segurança
    $id = intval($_GET['id']);

    // 3. DELETA DO BANCO
    $sql = "DELETE FROM controle_ativos WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // SUCESSO: Redireciona com mensagem
            header("Location: ../adm-painel.php?page=patrimonio&msg=sucesso-exclusao");
        } else {
            // ERRO DO BANCO
            die("Erro ao tentar excluir: " . $stmt->error);
        }
        $stmt->close();
    } else {
        die("Erro na query SQL: " . $conn->error);
    }

} else {
    // Se não mandou ID, volta para a tabela
    header("Location: ../adm-painel.php?page=patrimonio");
}
exit;
?>