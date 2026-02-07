<?php
session_start();
// Verifica se está logado
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
    <link rel="stylesheet" href="assets/styles/alterar-senha.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="assets/img/F.png">
    <script src="assets/js/script.js"></script>
    <title>ForteCare | Alterar Senha</title>
</head>

<body>
    <Header>
        <div class="logo">
            <img src="assets/img/fortecare h branco.png" alt="Logo ForteCare">
        </div>

        <div class="navigation" id="navigation">
            <nav>
                <a href="inicio.php">Inicio</a>
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

    <section class="hero-wrapper">
        <div class="form-container">
            <div class="senha-header">
                <h2>Alterar Senha</h2>
            <div class="retorn">
                <button type="button" onclick="history.length > 1 ? history.back() : window.location.href='inicio.php';"><i class='bx bx-x'></i></button>
            </div>
        </div>
            

            <?php if (isset($_SESSION['msg_erro'])): ?>
                <p class="msg-erro"><?= $_SESSION['msg_erro']; ?></p>
                <?php unset($_SESSION['msg_erro']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['msg_sucesso'])): ?>
                <p class="msg-sucesso"><?= $_SESSION['msg_sucesso']; ?></p>
                <?php unset($_SESSION['msg_sucesso']); ?>
            <?php endif; ?>

            <form action="controllers/alterar-senha-processa.php" method="POST">
                
                <div class="input-group password-group">
                    <label for="senha_atual">Senha Atual:</label>
                    <div class="password-wrapper">
                        <input type="password" name="senha_atual" id="senha_atual" required placeholder="Digite sua senha atual">
                        <i class='bx bx-show toggle-password' data-target="senha_atual"></i>
                    </div>
                </div>

                <div class="input-group password-group">
                    <label for="nova_senha">Nova Senha:</label>
                    <div class="password-wrapper">
                        <input type="password" name="nova_senha" id="nova_senha" required minlength="6" placeholder="Mínimo 6 caracteres">
                        <i class='bx bx-show toggle-password' data-target="nova_senha"></i>
                    </div>
                </div>

                <div class="input-group password-group">
                    <label for="confirma_senha">Confirmar Nova Senha:</label>
                    <div class="password-wrapper">
                        <input type="password" name="confirma_senha" id="confirma_senha" required placeholder="Repita a nova senha">
                        <i class='bx bx-show toggle-password' data-target="confirma_senha"></i>
                    </div>
                </div>
                
                <div class="save">
                    <button type="submit" class="btn-save">Salvar Nova Senha</button>
                </div>

            </form>
        </div>

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