<?php
session_start();
// Verifica se tem sessão aberta
if (!isset($_SESSION['id'])) {
    header("Location: ../controllers/login.php");
    exit;
}

// Define quem pode realizar essa ação
$perfisPermitidos = ['Admin', 'Suporte'];
$perfilUsuario = isset($_SESSION['perfil']) ? trim($_SESSION['perfil']) : '';

// Se NÃO for Admin nem Suporte, manda para o Login
if (!in_array($perfilUsuario, $perfisPermitidos)) {
    // Redireciona de volta para a lista com mensagem de erro
    header("Location: ../adm-painel.php?page=usuarios&msg=sem_permissao");
    exit;
}
// ==========================================================


// Ajuste o caminho do include conforme a estrutura das suas pastas
include_once __DIR__ . '/../config/conexao.php';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Recebe e limpa os dados
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $setor = $_POST['setor'] ?? null;
    $perfil = $_POST['perfil'] ?? null;
    $senha = $_POST['senha'];

    // 2. Validação básica (Backend)
    if (empty($nome) || empty($email) || empty($setor) || empty($perfil) || empty($senha)) {
        echo "<script>alert('Por favor, preencha todos os campos!'); window.history.back();</script>";
        exit;
    }

    // 3. Verifica se o e-mail já existe
    $checkEmail = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $resultEmail = $checkEmail->get_result();

    if ($resultEmail->num_rows > 0) {
        echo "<script>alert('Este e-mail já está cadastrado!'); window.history.back();</script>";
        exit;
    }

    // 4. Criptografa a senha (ESSENCIAL PARA SEGURANÇA)
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // 5. Prepara a inserção no banco
    $sql = "INSERT INTO usuarios (nome, email, setor, perfil, senha) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    // "sssss" indica que são 5 strings
    $stmt->bind_param("sssss", $nome, $email, $setor, $perfil, $senha_hash);

    // 6. Executa e redireciona
    if ($stmt->execute()) {
        // Redireciona de volta para o painel com mensagem de sucesso
        // Usando msg=criado para você poder exibir um alerta na lista se quiser
        header("Location: ../adm-painel.php?page=usuarios");
        exit;
    } else {
        echo "Erro ao cadastrar: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // Se tentar acessar o arquivo diretamente sem enviar formulário
    header("Location: ../adm-painel.php?page=usuarios");
    exit;
}
?>