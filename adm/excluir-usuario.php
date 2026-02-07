<?php
session_start();
include_once __DIR__ . '/../config/conexao.php';

// 1. Segurança: Verifica se o usuário é Admin ou Suporte
if (!isset($_SESSION['id']) || !in_array($_SESSION['perfil'], ['Admin', 'Suporte'])) {
    header("Location: ../adm-painel.php?page=usuarios&msg=sem_permissao");
    exit;
}

// 2. Verifica se existe um ID na URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // 3. Proteção: Impedir que você apague a si mesmo
    if ($id == $_SESSION['id']) {
        header("Location: ../adm-painel.php?page=usuarios&msg=erro_self");
        exit;
    }

    // =================================================================
    // 3.5. NOVO: VERIFICAR HIERARQUIA
    // Antes de apagar, precisamos saber QUEM é o usuário alvo
    // =================================================================
    
    // Busca o perfil do usuário que queremos excluir
    $sqlBusca = "SELECT perfil FROM usuarios WHERE id = ?";
    $stmtBusca = $conn->prepare($sqlBusca);
    $stmtBusca->bind_param("i", $id);
    $stmtBusca->execute();
    $resultBusca = $stmtBusca->get_result();

    // Se o usuário nem existe, volta
    if ($resultBusca->num_rows === 0) {
        header("Location: ../adm-painel.php?page=usuarios");
        exit;
    }

    $alvo = $resultBusca->fetch_assoc();

    // A REGRA DE OURO:
    // Se eu sou Suporte E o alvo é Admin -> BLOQUEIA
    if ($_SESSION['perfil'] === 'Suporte' && $alvo['perfil'] === 'Admin') {
        header("Location: ../adm-painel.php?page=usuarios&msg=erro_hierarquia");
        exit;
    }
    
    $stmtBusca->close(); // Fecha a busca para liberar o banco

    // =================================================================

    // 4. Se passou pela regra acima, pode Deletar
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../adm-painel.php?page=usuarios&msg=excluido");
    } else {
        header("Location: ../adm-painel.php?page=usuarios&msg=erro_banco");
    }
    
    $stmt->close();
    $conn->close();

} else {
    header("Location: ../adm-painel.php?page=usuarios");
    exit;
}
?>