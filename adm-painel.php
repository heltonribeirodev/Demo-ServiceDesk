<?php
session_start();

// 1. Verifica login
if (!isset($_SESSION['id'])) { 
    header("Location: inicio.php");
    exit;
}

// 2. Limpeza e Definições
$perfilUsuario = isset($_SESSION['perfil']) ? trim($_SESSION['perfil']) : '';
$perfisPermitidos = ['Admin', 'Suporte', 'Manutenção', 'Manutencao'];

// 3. Lógica do Bloqueio
// Se NÃO estiver na lista, marcamos como Inapto
if (!in_array($perfilUsuario, $perfisPermitidos)) {
    $perfilInapto = true; 
} else {
    $perfilInapto = false;
}

// Variável auxiliar Admin
$isAdmin = ($perfilUsuario === 'Admin');
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles/adm.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="assets/img/F.png">
    <script src="assets/js/script.js"></script>
    <title>ForteCare | Central de Administração</title>
    
    <style>
        .access-denied {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 60vh;
            text-align: center;
            color: var(--blue); /* Usando sua variável se disponível, senão #35457b */
        }
        .access-denied i {
            font-size: 80px;
            color: #c95451; /* Vermelho */
            margin-bottom: 20px;
        }
        .access-denied h2 { font-size: 32px; margin-bottom: 10px; }
        .access-denied p { font-size: 18px; color: #6c757d; }
        .btn-voltar {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #35457b;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <Header>
        <div class="logo">
            <img src="assets/img/fortecare h branco.png" alt="Logo ForteCare">
        </div>

        <div class="navigation" id="navigation">
            <nav>
                <a href="inicio.php" >Inicio</a>
                <a href="meus-tickets.php">Meus Tickets</a>
                <a href="adm-painel.php" class="select-a">Central de Administração</a>
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

    <?php if (!$perfilInapto): ?>
        
        <section class="wrapper">

            <article class="menu"> 
                <div>
                    <nav>
                        <a href="adm-painel.php?page=usuarios"
                           class="<?= ($_GET['page'] ?? 'dashboard') == 'usuarios' ? 'active' : '' ?>">
                            Central de Usuários
                        </a>
                    </nav>
                </div>

                <div>
                    <nav>
                        <a href="adm-painel.php?page=patrimonio"
                           class="<?= ($_GET['page'] ?? '') == 'patrimonio' ? 'active' : '' ?>">
                            Controle de Patrimônio
                        </a>
                    </nav>
                </div>

                <div>
                    <nav>
                        <a href="adm-painel.php?page=chamados"
                           class="<?= ($_GET['page'] ?? '') == 'chamados' ? 'active' : '' ?>">
                            Gerenciador de Chamados
                        </a>
                    </nav>
                </div>
                

                <div>
                    <nav>
                        <a href="adm-painel.php?page=relatorios"
                           class="<?= ($_GET['page'] ?? '') == 'relatorios' ? 'active' : '' ?>">
                            Relatórios
                        </a>
                    </nav>
                </div>

            </article>
            
            <?php if (empty($_GET['page'])): ?>
                <div class="txt-adm">
                <h1>Bem-vindo, <?= $_SESSION['nome'] ?> 👋</h1>

                <p>
                    Este painel concentra as funcionalidades administrativas do sistema de chamados da <strong>ForteCare</strong>. 
                </p>

                <p>
                    Utilize o menu acima para acessar as funcionalidades disponíveis.
                </p>
                </div>
            <?php endif; ?>


            <section class="adm-content">
                <?php
                $page = $_GET['page'] ?? 'dashboard';

                // Garante que só carrega arquivos se for admin (segurança extra)
                switch ($page) {
                    case 'usuarios':
                        include __DIR__ . '/adm/usuarios.php';
                        break;

                    case 'patrimonio':
                        include __DIR__ . '/adm/patrimonio.php';
                        break;

                    case 'chamados':
                        include __DIR__ . '/adm/chamados.php';
                        break;

                    case 'relatorios':
                        include __DIR__ . '/adm/relatorios.php';
                        break;
                }
                ?>
            </section>
        </section>

    <?php else: ?>

        <div class="access-denied">
            <i class='bx bxs-lock-alt'></i>
            <h2>Acesso Restrito</h2>
            <p>Você não possui permissão para visualizar este painel.</p>
            <br>
            <a href="inicio.php" class="btn-voltar">Voltar ao Início</a>
        </div>

    <?php endif; ?>

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
</body>

</html>