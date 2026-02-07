<?php
session_start();
include_once __DIR__ . '/../config/conexao.php';

// 1. Verifica se está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../inicio.php");  
    exit;
}

// 2. DEFINIÇÃO DE PERMISSÕES
// Aqui dizemos quem pode salvar/atualizar o patrimônio
$perfisPermitidos = ['Admin', 'Suporte'];

// Limpa espaços vazios do perfil para evitar erros
$perfilUsuario = isset($_SESSION['perfil']) ? trim($_SESSION['perfil']) : '';

// 3. O GUARDIÃO
// Se o perfil do usuário NÃO estiver na lista (ou seja, se for Manutenção ou Padrão)   
if (!in_array($perfilUsuario, $perfisPermitidos)) {
    // Redireciona de volta com erro
    header("Location: ../adm-painel.php?page=patrimonio&msg=sem_permissao");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recebe o ID que está oculto no form
    $id = intval($_POST['id']);

    // Recebe os dados
    $matricula               = $_POST['matricula'];
    $ativo                   = $_POST['ativo'];
    $numero_serial           = $_POST['numero_serial'];
    $colaborador_responsavel = $_POST['colaborador_responsavel'];
    $departamento            = $_POST['departamento'];
    $status                  = $_POST['status'];
    $categoria               = $_POST['categoria'];
    $detalhes                = $_POST['detalhes'];
    $data_de_entrega         = $_POST['data_de_entrega'];

    // Tratamento de data vazia
    if (empty($data_de_entrega)) {
        $data_de_entrega = null;
    }

    // SQL de Atualização
    $sql = "UPDATE controle_ativos SET 
            matricula = ?, 
            ativo = ?, 
            numero_serial = ?, 
            colaborador_responsavel = ?, 
            departamento = ?, 
            status = ?, 
            categoria = ?, 
            detalhes = ?, 
            data_de_entrega = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param(
            "sssssssssi", // 9 strings + 1 inteiro (id)
            $matricula, 
            $ativo, 
            $numero_serial, 
            $colaborador_responsavel, 
            $departamento, 
            $status, 
            $categoria, 
            $detalhes,
            $data_de_entrega,
            $id
        );

        if ($stmt->execute()) {
            header("Location: ../adm-painel.php?page=patrimonio&msg=atualizado");
            exit;
        } else {
            echo "Erro ao atualizar: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro na query: " . $conn->error;
    }

} else {
    header("Location: ../adm-painel.php?page=patrimonio");
    exit;
}

?>

