<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include_once __DIR__ . '/../config/conexao.php';

// 1. Segurança: Verifica Login
if (!isset($_SESSION['id'])) {
    header("Location: ../inicio.php");
    exit;
}

// 2. Segurança: Lista de quem pode acessar esta tela
// Adicionei 'Suporte' também, pois geralmente eles precisam ver.
// Se quiser remover, basta apagar da lista.
$perfisPermitidos = [
    'Admin', 
    'Suporte', 
    'Manutenção', 
    'Manutencao' // Garante acesso mesmo se o banco estiver sem acento
];

$perfilUsuario = isset($_SESSION['perfil']) ? trim($_SESSION['perfil']) : '';

// O Porteiro: Se não estiver na lista, bloqueia.
if (!in_array($perfilUsuario, $perfisPermitidos)) {
    // Você pode redirecionar para o painel ou para o início
    header("Location: ../inicio.php");
    exit;
}

// Verifica ID do chamado
if (!isset($_GET['id'])) {
    header("Location: ../adm-painel.php?page=chamados");
    exit;
}

$id_chamado = intval($_GET['id']);
$id_usuario_logado = $_SESSION['id'];

// --- PROCESSAMENTOS (POST) ---

// A. Atualizar Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $novo_status = $_POST['status'];
    $resposta = $_POST['resposta_admin'] ?? '';

    if ($novo_status === 'Resolvido') {
        $sql_update = "
            UPDATE chamados 
            SET status = ?, resposta_admin = ?, data_conclusao = NOW()
            WHERE id = ?
        ";
    } else {
        $sql_update = "
            UPDATE chamados 
            SET status = ?, resposta_admin = ?, data_conclusao = NULL
            WHERE id = ?
        ";
    }

    $stmt_up = $conn->prepare($sql_update);
    $stmt_up->bind_param("ssi", $novo_status, $resposta, $id_chamado);

    if ($stmt_up->execute()) {
        $msg_sucesso = "Status atualizado com sucesso!";
        echo "<script>setTimeout(() => { window.location.href = window.location.href; }, 1000);</script>";
    } else {
        $msg_erro = "Erro ao atualizar: " . $conn->error;
    }
}

// B. Chat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensagem_chat'])) {
    $msg = trim($_POST['mensagem_chat']);
    
    if (!empty($msg)) {
        $sql_chat = "INSERT INTO interacoes (chamado_id, usuario_id, mensagem, data_envio) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql_chat);
        // "iis" = integer, integer, string
        $stmt->bind_param("iis", $id_chamado, $id_usuario_logado, $msg);
        
        if ($stmt->execute()) {
            // Redireciona para evitar reenvio do formulário ao atualizar a página
            header("Location: ver-chamado.php?id=$id_chamado");
            exit;
        }
    }
}

// 2. Buscas
$ticket = $conn->query("SELECT c.*, u.nome AS nome_usuario, u.email FROM chamados c LEFT JOIN usuarios u ON c.usuario_id = u.id WHERE c.id = $id_chamado")->fetch_assoc();

if (!$ticket) { echo "Chamado não encontrado."; exit; }

$chat_result = $conn->query("SELECT i.*, u.nome, u.perfil FROM interacoes i JOIN usuarios u ON i.usuario_id = u.id WHERE i.chamado_id = $id_chamado ORDER BY i.data_envio ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Chamado #<?= $ticket['id'] ?></title>   

    <link rel="stylesheet" href="../assets/styles/ver-chamados.css">
    <link rel="stylesheet" href="../assets/styles/chat.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="../assets/img/F.png">
    
    <script src="assets/js/script.js"></script>
</head>

<body> 
    <Header>
        <div class="logo">
            <img src="../assets/img/fortecare h branco.png" alt="Logo ForteCare">
        </div>


        <div class="navigation" id="navigation">
            <nav>
                <a href="../inicio.php">Inicio</a>
                <a href="../meus-tickets.php">Meus Tickets</a>
                <a href="../adm-painel.php"  class="select-a">Central de Administração</a>
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
                    <h2>Chamado #<?= $ticket['id'] ?> - <?= htmlspecialchars($ticket['assunto']) ?></h2>
                </div>

                <div class="retorn">
                    <button type="button" onclick="window.location.href='../adm-painel.php? page=chamados'"><i class='bx bx-x'></i></button>
                </div>

            </div>

            <?php if (isset($msg_sucesso)): ?>
                <div class="alert success"><i class='bx bx-check'></i> <?= $msg_sucesso ?></div>
            <?php endif; ?>
            <?php if (isset($msg_erro)): ?>
                <div class="alert error"><i class='bx bx-error'></i> <?= $msg_erro ?></div>
            <?php endif; ?>

            <div class="info-grid">
                <div class="info-item">
                    <label>Solicitante:</label>
                    <p><?= htmlspecialchars($ticket['nome_usuario'] ?? 'Removido') ?></p>
                </div>
                <div class="info-item">
                    <label>E-mail:</label>
                    <p><?= htmlspecialchars($ticket['email'] ?? '-') ?></p>
                </div>
                <div class="info-item">
                    <label>Tipo de Ticket:</label>
                    <p><?= htmlspecialchars($ticket['tipo']) ?></p>
                </div>
                <div class="info-item">
                    <label>Setor:</label>
                    <p><?= htmlspecialchars($ticket['setor']) ?></p>
                </div>
                <div class="info-item">
                    <label>Data:</label>
                    <p><?= date('d/m/Y H:i', strtotime($ticket['data_abertura'])) ?></p>
                </div>
                <div class="info-item" >
                    <label>Concluído em:</label>
                    <p>
                        <?= !empty($ticket['data_conclusao'])
                            ? date('d/m/Y H:i', strtotime($ticket['data_conclusao']))
                            : '-' ?>
                    </p>
                </div>

                <div class="info-item">
                    <label>Patrimônio:</label>
                    <p><?= htmlspecialchars($ticket['patrimonio']) ?></p>
                </div>
                <div class="info-item">
                    <label>Prioridade:</label>
                    <span class="priority <?= strtolower($ticket['prioridade']) ?>">
                        <?= htmlspecialchars($ticket['prioridade']) ?>
                    </span>
                </div>

                <div class="info-item">
                    <label>Status:</label>
                    <?php $cssClass = strtolower(str_replace(' ', '-', $ticket['status'])); ?>
                    
                    <span class="status <?= $cssClass ?>">
                        <?= htmlspecialchars($ticket['status']) ?>
                    </span>
                </div>
            </div>
            
            <div class="conteudo-chamado">
                <h3> Descrição</h3>
                <div class="descricao-box">
                    <?= nl2br(htmlspecialchars($ticket['descricao'])) ?>
                </div>
                <?php if (!empty($ticket['anexo'])): ?>
                    <div class="anexo-area">
                        <a href="../assets/uploads/<?= $ticket['anexo'] ?>" target="_blank" class="btn-anexo">
                            <i class='bx bx-paperclip'></i> Visualizar Anexo
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="chat-section">
                <h3><i class='bx bx-chat'></i> Conversa com Solicitante </h3>
                
                <div class="chat-box" id="chatBox">
                    <?php if ($chat_result && $chat_result->num_rows > 0): ?>
                        <?php while ($msg = $chat_result->fetch_assoc()): ?>
                            <?php 
                                // Lógica para alinhar a mensagem
                                // Verifica se o ID de quem enviou é o MESMO ID de quem está logado
                                $sou_eu = ($msg['usuario_id'] == $_SESSION['id']);
                                
                                // Define a classe CSS
                                $classe_msg = $sou_eu ? 'msg-me' : 'msg-other';
                                
                                // Define o nome do autor
                                $autor = $sou_eu ? 'Você' : htmlspecialchars($msg['nome']);
                                
                                // Adiciona o perfil ao lado do nome (Ex: Helton - Admin)
                                $perfil_autor = !$sou_eu ? " <small>({$msg['perfil']})</small>" : "";
                            ?>
                            <div class="message <?= $classe_msg ?>">
                                <div class="msg-header">
                                    <strong><?= $autor . $perfil_autor ?></strong> 
                                    <span><?= date('d/m - H:i', strtotime($msg['data_envio'])) ?></span>
                                </div>
                                <div class="msg-body"><?= nl2br(htmlspecialchars($msg['mensagem'])) ?></div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="empty-chat">Nenhuma mensagem registrada.</p>
                    <?php endif; ?>
                </div>

                <form method="POST" class="form-chat-reply">
                    <textarea name="mensagem_chat" placeholder="Digite uma resposta ou dúvida..." required></textarea>
                    <button type="submit" class="btn-send"><i class='bx bx-send'></i></button>
                </form>
            </div>

            <div class="admin-actions-area">
                <h3><i class='bx bx-edit-alt'></i> Gerenciamento</h3>
                <form method="POST" class="form-admin">
                    <div class="form-row">
                        <div class="form-group half">
                            <label>Atualizar Status:</label>
                            <select name="status" class="status-select">
                                <option value="Aberto" <?= $ticket['status'] == 'Aberto' ? 'selected' : '' ?>>Aberto</option>
                                
                                <option value="Em Andamento" <?= $ticket['status'] == 'Em Andamento' ? 'selected' : '' ?>>Em Andamento</option>
                                
                                <option value="Resolvido" <?= $ticket['status'] == 'Resolvido' ? 'selected' : '' ?>>Resolvido</option>
                                </option>
                            </select>
                        </div>
                        <div class="form-group full" >
                            <label>Observação Técnica (Resolução):</label>
                            <textarea name="resposta_admin" rows="2" required ><?= htmlspecialchars($ticket['resposta_admin'] ?? '' ) ?></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn-save"><i class='bx bx-save'></i> Salvar Alterações</button>
                </form>
            </div>

        </article>
    </section>

    <footer class="copy">
        <section>
            <p>
                &copy; 2026 ForteCare. Todos os direitos reservados.
            </p>
        </section>

        <section>
            <p>
                 Desenvolvido com ❤️ por <a href="#" target="_blank">HR | DEV</a>.
            </p>
        </section>

    </footer>

    <script src="../assets/js/script.js"></script>
</body>
</html>