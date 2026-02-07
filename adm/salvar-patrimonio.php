<?php
session_start();
include_once __DIR__ . '/../config/conexao.php';

// 1. Verifica se está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../inicio.php");
    exit;
}

// 2. DEFINIÇÃO DE PERMISSÕES
// Aqui definimos quem pode salvar: Admin e Suporte
$perfisPermitidos = ['Admin', 'Suporte'];

// Limpa espaços vazios do perfil para evitar erros de leitura
$perfilUsuario = isset($_SESSION['perfil']) ? trim($_SESSION['perfil']) : '';

// 3. O GUARDIÃO
// Se o perfil do usuário NÃO estiver na lista permitida, bloqueia.
if (!in_array($perfilUsuario, $perfisPermitidos)) {
    // Redireciona de volta para a lista com mensagem de erro
    header("Location: ../adm-painel.php?page=patrimonio&msg=sem_permissao");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Recebendo dados com segurança
    $matricula               = $_POST['matricula'] ?? '';
    $ativo                   = $_POST['ativo'] ?? '';
    $numero_serial           = $_POST['numero_serial'] ?? '';
    $colaborador_responsavel = $_POST['colaborador_responsavel'] ?? '';
    $departamento            = $_POST['departamento'] ?? '';
    $status                  = $_POST['status'] ?? '';
    $categoria               = $_POST['categoria'] ?? '';
    $detalhes                = $_POST['detalhes'] ?? '';
    $data_de_entrega         = $_POST['data_de_entrega'] ?? '';

    // Tratamento para data vazia (para salvar NULL no banco se não for preenchida)
    if (empty($data_de_entrega)) {
        $data_de_entrega = null;
    }

    // 2. Preparação do SQL
    $sql = "INSERT INTO controle_ativos 
            (matricula, ativo, numero_serial, colaborador_responsavel, departamento, status, categoria, detalhes, data_de_entrega) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // 3. Bind Param (9 parâmetros)
        $stmt->bind_param(
            "sssssssss", 
            $matricula, 
            $ativo, 
            $numero_serial, 
            $colaborador_responsavel, 
            $departamento, 
            $status, 
            $categoria, 
            $detalhes,
            $data_de_entrega
        );

        if ($stmt->execute()) {
            // Sucesso: volta para o painel com mensagem de sucesso
            header("Location: ../adm-painel.php?page=patrimonio&msg=sucesso-patrimonio");
            exit;
        } else {
            // Erro SQL (ex: matrícula duplicada)
            // É útil redirecionar com erro para o usuário ver
            header("Location: ../adm-painel.php?page=patrimonio&msg=erro_banco-patrimonio");
            exit;
        }
        $stmt->close();
    } else {
        die("Erro na preparação do SQL: " . $conn->error);
    }
} else {
    // Se tentar acessar o arquivo direto pela URL sem enviar POST
    header("Location: ../adm-painel.php?page=patrimonio");
    exit;
}
?>