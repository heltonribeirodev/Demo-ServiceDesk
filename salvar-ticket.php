<?php
session_start();
include_once 'config/conexao.php'; // Verifique se o caminho da conexão está correto

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Pega o ID do usuário logado na sessão
    $usuario_id = $_SESSION['id'];

    // Recebe os dados do formulário
    $tipo = $_POST['tipo'] ?? '';
    $patrimonio = $_POST['patrimonio'] ?? '';
    $setor = $_POST['setor'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $prioridade = $_POST['prioridade'] ?? '';
    $assunto = $_POST['assunto'] ?? '';
    $descricao = $_POST['descricao'] ?? '';

    // Lógica simples para upload de 1 arquivo (Podemos melhorar para múltiplos depois)
    $nome_arquivo = null;
    if (isset($_FILES['anexos']) && $_FILES['anexos']['error'][0] === UPLOAD_ERR_OK) {
        // Pega apenas o primeiro arquivo por enquanto para facilitar
        $extensao = pathinfo($_FILES['anexos']['name'][0], PATHINFO_EXTENSION);
        $novo_nome = md5(time()) . "." . $extensao;
        $diretorio = "assets/uploads/";

        // Cria a pasta se não existir
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0777, true);
        }

        move_uploaded_file($_FILES['anexos']['tmp_name'][0], $diretorio . $novo_nome);
        $nome_arquivo = $novo_nome;
    }

    // Insere no banco
    $sql = "INSERT INTO chamados (usuario_id, tipo, patrimonio, setor, categoria, prioridade, assunto, descricao, anexo, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Aberto')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssss", $usuario_id, $tipo, $patrimonio, $setor, $categoria, $prioridade, $assunto, $descricao, $nome_arquivo);

    if ($stmt->execute()) {
        // Redireciona para a página de Meus Tickets (que vamos criar a seguir)
        header("Location: meus-tickets.php?msg=sucesso");
        exit;
    } else {
        echo "Erro ao abrir chamado: " . $conn->error;
    }

} else {
    header("Location: novo-ticket.php");
    exit;
}
?>