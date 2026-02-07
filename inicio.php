<?php
session_start(); // Inicia a sessão para ler os dados
// var_dump($_SESSION); // Remova ou comente esta linha depois de testar!


// Verificação de segurança (opcional, mas recomendado):
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles/style-home.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="assets/img/F.png">
    <script src="assets/js/script.js" defer></script>
    <title>ForteCare | Início</title>
</head>

<body>
    <Header>
        <div class="logo">
            <img src="assets/img/fortecare h branco.png" alt="Logo ForteCare">
        </div>

        <div class="navigation" id="navigation">
            <nav>
                <a href="inicio.php" class="select-a">Inicio</a>
                <a href="meus-tickets.php">Meus Tickets</a>
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

    <section id="home" class="hero-wrapper">
        <article class="hero">
            <div>
                <h1>
                    Bem vindo a central de serviços ForteCare!
                </h1>

                <p> 
                    Abra e acompanhe chamados de TI e Manutenção Predial em um só lugar. Mais agilidade para o seu ambiente de trabalho.
                </p>

                <nav class="hero-buttons">
                    <a href="novo-ticket.php" class="btn-outline">🎟️ Novo Ticket</a>
                </nav>
            </div>
            <img src="assets/img/support.png" alt="support">
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
</body>

</html> 