<?php
session_start();
include_once __DIR__ . '/../config/conexao.php';

// 1. Verifica se está logado e se é POST
if (!isset($_SESSION['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../inicio.php");
    exit;
}

// 2. Recebe os dados do formulário
$matricula      = $_POST['matricula']; // O usuário digitou a matrícula
$data_ocorrido  = $_POST['data_ocorrido'];
$data_resolvido = !empty($_POST['data_resolvido']) ? $_POST['data_resolvido'] : NULL;
$defeito        = $_POST['defeito'];
$valor          = !empty($_POST['valor']) ? str_replace(',', '.', $_POST['valor']) : 0.00;
$responsavel    = $_POST['responsavel'];

// 3. BUSCAR O ID DO ATIVO USANDO A MATRÍCULA
// A tabela historico_manutencao usa 'ativo_id', então precisamos descobrir qual é.
$sqlBusca = "SELECT id FROM controle_ativos WHERE matricula = ?";
$stmtBusca = $conn->prepare($sqlBusca);
$stmtBusca->bind_param("s", $matricula);
$stmtBusca->execute();
$resultBusca = $stmtBusca->get_result();

if ($resultBusca->num_rows === 0) {
    // Se não achou a matrícula, volta com erro
    header("Location: ../adm-painel.php?page=patrimonio&msg=matricula_nao_encontrada");
    exit;
}

$ativo = $resultBusca->fetch_assoc();
$ativo_id = $ativo['id']; // Aqui está o ID real (ex: 6)

// 4. INSERIR NA TABELA DE HISTÓRICO
$sqlInsert = "INSERT INTO historico_manutencao (ativo_id, data_ocorrido, data_resolvido, defeito, valor, responsavel) VALUES (?, ?, ?, ?, ?, ?)";
$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bind_param("isssds", $ativo_id, $data_ocorrido, $data_resolvido, $defeito, $valor, $responsavel);

if ($stmtInsert->execute()) {
    
    // 5. (Opcional) ATUALIZAR STATUS DO ATIVO
    // Se a manutenção ainda não foi resolvida (data_resolvido vazio), muda status para 'Em Manutenção'
    if (empty($data_resolvido)) {
        $sqlUpdate = "UPDATE controle_ativos SET status = 'Em Manutenção' WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("i", $ativo_id);
        $stmtUpdate->execute();
    }

    // Redireciona com sucesso
    header("Location: ../adm-painel.php?page=patrimonio&msg=sucesso-manutencao");
} else {
    // Erro de banco
    header("Location: ../adm-painel.php?page=patrimonio&msg=erro_banco");
}

$stmtBusca->close();
$stmtInsert->close();
$conn->close();
?>