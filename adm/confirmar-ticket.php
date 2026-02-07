<?php
session_start();
// Ajuste o caminho do include conforme sua estrutura de pastas.
// Se este arquivo estiver dentro da pasta "acoes", use "../config/conexao.php"
include_once '../config/conexao.php'; 

// 1. Segurança: Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    // Se não estiver logado, manda pro login
    header("Location: ../login.php");
    exit;
}

// 2. Verifica se recebeu o ID do ticket pela URL
if (isset($_GET['id'])) {
    
    $id_ticket = intval($_GET['id']); // Garante que é um número
    $usuario_id = $_SESSION['id'];    // Pega o ID do usuário logado

    // 3. O Pulo do Gato (A Lógica de Segurança)
    // O comando UPDATE abaixo só funciona se:
    // a) O ID do ticket for o correto.
    // b) O ticket pertencer mesmo ao usuário logado (usuario_id).
    // c) O status atual for 'Resolvido' (evita fechar tickets que o técnico ainda não mexeu).
    
    $sql = "UPDATE chamados 
            SET status = 'Concluido', 
                data_conclusao = NOW() 
            WHERE id = ? AND usuario_id = ? AND status = 'Resolvido'";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_ticket, $usuario_id);
    
    if ($stmt->execute()) {
        // Verifica se alguma linha foi alterada (ou seja, se deu certo)
        if ($stmt->affected_rows > 0) {
            // Sucesso: Volta para a lista com mensagem verde
            header("Location: ../meus-tickets.php?msg=sucesso_conclusao");
        } else {
            // Falha: Tentou fechar ticket de outro ou ticket que não estava 'Resolvido'
            header("Location: ../meus-tickets.php?msg=erro_permissao");
        }
    } else {
        // Erro de SQL
        header("Location: ../meus-tickets.php?msg=erro_banco");
    }
    
    $stmt->close();

} else {
    // Se tentou acessar o arquivo sem passar ID
    header("Location: ../meus-tickets.php");
}

$conn->close();
?>