<?php
session_start();
include_once 'config/conexao.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: meus-tickets.php");
    exit;
}

$id_chamado = intval($_GET['id']);
$id_usuario_logado = $_SESSION['id'];

// 1. Processar Nova Mensagem (Chat)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensagem'])) {
    $mensagem = trim($_POST['mensagem']);
    if (!empty($mensagem)) {
        $sql_msg = "INSERT INTO interacoes (chamado_id, usuario_id, mensagem) VALUES (?, ?, ?)";
        $stmt_msg = $conn->prepare($sql_msg);
        $stmt_msg->bind_param("iis", $id_chamado, $id_usuario_logado, $mensagem);
        $stmt_msg->execute();
        
        // Recarrega a página para não reenviar form
        header("Location: ver-ticket.php?id=$id_chamado");
        exit;
    }
}

// 2. Buscar Detalhes do Chamado
$sql = "SELECT * FROM chamados WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_chamado, $id_usuario_logado);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();

if (!$ticket) {
    echo "<script>alert('Chamado não encontrado ou sem permissão.'); window.location='meus-tickets.php';</script>";
    exit;
}

// 3. Buscar Histórico de Conversa
$sql_chat = "SELECT i.*, u.nome, u.perfil 
             FROM interacoes i 
             JOIN usuarios u ON i.usuario_id = u.id 
             WHERE i.chamado_id = ? 
             ORDER BY i.data_envio ASC";
$stmt_chat = $conn->prepare($sql_chat);
$stmt_chat->bind_param("i", $id_chamado);
$stmt_chat->execute();
$chat_result = $stmt_chat->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="assets/styles/ver-ticket.css">
    
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="assets/img/F.png">
    <script src="assets/js/script.js" defer></script>
    <title>ForteCare | Detalhes do Ticket #<?= $ticket['id'] ?></title>
</head>
<body>
    
    <Header>
        <div class="logo">
            <img src="assets/img/fortecare h branco.png" alt="Logo ForteCare">
        </div>

        <div class="navigation" id="navigation">
            <nav>
                <a href="inicio.php">Inicio</a>
                <a href="meus-tickets.php"  class="select-a">Meus Tickets</a>
                <a href="adm-painel.php" >Central de Administração</a>
            </nav>
        </div>


        <div class="btn-account">
            <div class="user-menu">
                <a href="#" class="btn-usuario" id="btnUsuario" title="Minha Conta">
                    <i class='bx bxs-user'></i>
                </a>

                <div class="user-dropdown" id="userDropdown">
                    <div class="txt-user-div">
                        <h3 class="txt-user">
                            Minha conta
                        </h3>   
                    </div>

                    <p class="user-name"> <span><Strong>Nome: </Strong></span><?= $_SESSION['nome'] ?></p>
                    <p class="user-email"> <span><Strong>E-mail: </Strong></span><?= $_SESSION['email'] ?></p>

                <div class="user-info">
                    <span><strong>Setor:</strong> <?= $_SESSION['setor'] ?></span>

                </div>
                <div class="user-info">
                    <span><strong>Perfil:</strong> <?= $_SESSION['perfil'] ?></span>
                </div>

                <div class="txt-p">
                    <p>
                        <span>* </span>Caso seja necessário editar alguma informação, entre em contato com o setor de TI.
                    </p>
                </div>
                    <div class="user-actions">
                        <a href="alterar-senha.php" class="btn-action">Alterar Senha</a>
                        <a href="logout.php" class="btn-logout">Sair</a>
                    </div>
                </div>
            </div>
        </div>

                    <!-- BOTÃO HAMBURGUER (MOBILE) -->
        <div class="menu-toggle" id="menuToggle">
            <i class='bx bx-menu'></i>
        </div>
    </Header>

    <section class="wrapper">
        <article class="content-box">
            
            <div class="tickets-header">
                <div class="title-group">
                    <a href="meus-tickets.php" > <i class='bx bx-arrow-back'></i>
                    </a>
                    <h2>Ticket #<?= $ticket['id'] ?> - <?= htmlspecialchars($ticket['assunto']) ?></h2>
                </div>
                
                <?php $cssStatus = strtolower(str_replace(' ', '-', $ticket['status'])); ?>
                <span class="status-badge <?= $cssStatus ?>"><?= htmlspecialchars($ticket['status']) ?></span>
            </div>

            <div class="ticket-details">
                <p><strong>Categoria:</strong> <?= htmlspecialchars($ticket['categoria']) ?></p>
                <p><strong>Patrimônio:</strong> <?= htmlspecialchars($ticket['patrimonio']) ?></p>
                <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($ticket['data_abertura'])) ?></p>
                <p><strong>Tipo de Ticket:</strong> <?= htmlspecialchars($ticket['tipo']) ?></p>
                <hr>
                <p><strong>Descrição:</strong></p>
                <div class="descricao-box">
                    <?= nl2br(htmlspecialchars($ticket['descricao'])) ?>
                </div>
                
                <?php if (!empty($ticket['anexo'])): ?>
                    <a href="assets/uploads/<?= $ticket['anexo'] ?>" target="_blank" class="btn-anexo">
                        <i class='bx bx-paperclip'></i> Ver Anexo Enviado
                    </a>
                <?php endif; ?>
            </div>

            <div class="chat-section">
                <h3><i class='bx bx-chat'></i> Conversa com Suporte</h3>
                
                <div class="chat-box">
                    <?php if ($chat_result->num_rows > 0): ?>
                        <?php while ($msg = $chat_result->fetch_assoc()): ?>
                            <?php 
                                $eh_minha = ($msg['usuario_id'] == $id_usuario_logado);
                                $classe_msg = $eh_minha ? 'msg-me' : 'msg-other';
                                
                                if ($eh_minha) {
                                    $autor = 'Você';
                                } elseif ($msg['perfil'] == 'Admin') {
                                    $autor = 'Suporte ForteCare'; 
                                } else {
                                    $autor = htmlspecialchars($msg['nome']);
                                }
                            ?>
                            <div class="message <?= $classe_msg ?>">
                                <div class="msg-header">
                                    <strong><?= $autor ?> </strong>
                                    <span><?= date('d/m - H:i', strtotime($msg['data_envio'])) ?></span>
                                </div>
                                <div class="msg-body">
                                    <?= nl2br(htmlspecialchars($msg['mensagem'])) ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="empty-chat">Nenhuma mensagem registrada. </p>
                    <?php endif; ?>
                </div>

                <?php if ($ticket['status'] !== 'Concluido'): ?>
                    <form method="POST" class="chat-form">
                        <textarea name="mensagem" placeholder="Digite uma resposta ou dúvida..." required></textarea>
                        <button type="submit"><i class='bx bx-send'></i></button>
                    </form>
                <?php else: ?>
                    <div class="alert-closed">
                        Este ticket está fechado. Não é possível enviar novas mensagens.
                    </div>
                <?php endif; ?> 

            </div>

        </article>
    </section>

    <footer class="copy">
        <section>
            <p>&copy; 2026 ForteCare. Todos os direitos reservados.</p>
        </section>
        <section>
            <p> Desenvolvido com ❤️ por <a href="#" target="_blank">HR | DEV</a>.</p>
        </section>
    </footer>
</body>
</html>