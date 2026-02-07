<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles/style-login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="assets/img/F.png">  
    <script src="assets/js/script.js"></script>
    <title>ForteCare | Login</title>
</head>
<body>  
    <Header>
        <div class="logo">
            <img src="assets/img/fortecare h branco.png" alt="Logo ForteCare">
        </div>
    </Header>

    <div class="formulario">
        <h1>Login</h1>

        <?php if (isset($_SESSION['erro_login'])): ?>
            <div class="erro-login">
                <?= $_SESSION['erro_login']; ?>
            </div>
            <?php unset($_SESSION['erro_login']); ?>
        <?php endif; ?>

        <form action="controllers/login.php" method="POST">
            <div class="caixa-formulario">
                <input name="email" type="email" placeholder="E-mail" required>
                <i class='bx bxs-user'></i>
            </div>

            <div class="caixa-formulario">  
                <input id="inputSenha" name="password" type="password" placeholder="Senha" required>  
                
                <i class='bx bxs-lock-alt' id="btnToggleSenha"></i>
            </div>
                
            <div class="duvidas">
                <a href="#" onclick="abrirSuporte(event)" class="link-suporte">
                    Não consegue acessar?
                </a>
            </div>

            <button type="submit">Entrar</button>
        </form>

        <div id="modalSuporte" class="modal-overlay">
            <div class="modal-content">
                <i class='bx bx-support icon-modal'></i>
                <h3>Suporte de Acesso</h3>
                <p>A gestão de contas é restrita ao administrador.</p>
                
                <div class="info-box">
                    <p><i class='bx bx-phone'></i> Ramal: <strong>4436</strong></p>
                    <p><i class='bx bx-envelope'></i> suporte.ti@fortecare.com.br</p>
                </div>

                <button onclick="fecharSuporte()" class="btn-fechar">Voltar</button>
            </div>
        </div>
        </div>

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