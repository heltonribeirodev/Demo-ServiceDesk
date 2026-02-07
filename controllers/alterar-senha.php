<?php
session_start();
include "../config/conexao.php";

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

// Verifica se veio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id_usuario = $_SESSION['id'];
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirma_senha'];

    // 1. Verifica se as senhas novas coincidem
    if ($nova_senha !== $confirma_senha) {
        $_SESSION['msg_erro'] = "A nova senha e a confirmação não conferem.";
        header("Location: ../alterar-senha.php");
        exit;
    }

    // 2. Busca a senha atual no banco para conferir
    $sql = "SELECT senha FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    // 3. Verifica se a senha atual digitada está correta
    if ($usuario && password_verify($senha_atual, $usuario['senha'])) {
        
        // 4. Cria o HASH da nova senha
        $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // 5. Atualiza no Banco de Dados
        $sql_update = "UPDATE usuarios SET senha = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $nova_senha_hash, $id_usuario);
        
        if ($stmt_update->execute()) {
            $_SESSION['msg_sucesso'] = "Senha alterada com sucesso!";
        } else {
            $_SESSION['msg_erro'] = "Erro ao atualizar no banco de dados.";
        }

    } else {
        $_SESSION['msg_erro'] = "A senha atual está incorreta.";
    }

    header("Location: ../alterar-senha.php");
    exit;

} else {
    // Se tentar acessar o arquivo direto sem post
    header("Location: ../alterar-senha.php");
    exit;
}
?>