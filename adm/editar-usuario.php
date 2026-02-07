<?php
session_start();

include_once __DIR__ . '/../config/conexao.php';

// 1. Segurança: Só admin entra
if (!isset($_SESSION['id']) || !in_array($_SESSION['perfil'], ['Admin', 'Suporte'])) {
    header("Location: ../adm-painel.php?page=usuarios&msg=erro_permissao");
    exit;
}

// 2. Verifica se tem ID na URL
if (!isset($_GET['id'])) {
    header("Location: ../adm-painel.php?page=usuarios");
    exit;
}

$id = intval($_GET['id']);

// 3. Busca os dados atuais do usuário
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../adm-painel.php?page=usuarios");
    exit;
}

$user = $result->fetch_assoc();

if ($_SESSION['perfil'] === 'Suporte' && $user['perfil'] === 'Admin') {
    header("Location: ../adm-painel.php?page=usuarios&msg=erro_hierarquia");
    exit;
}
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ForteCare | Editar Usuário</title>
    <link rel="stylesheet" href="../assets/styles/editar-usuario.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="../assets/img/F.png">   
    <script src="../assets/js/script.js" defer></script>
</head>
<body class="body-editar">


    <Header>
        <div class="logo">
            <img src="../assets/img/fortecare h branco.png" alt="Logo ForteCare">
        </div>

        <div class="navigation" id="navigation">
            <nav>
                <a href="inicio.php">Inicio</a>
                <a href="meus-tickets.php">Meus Tickets</a>
                <a href="adm-painel.php"  class="select-a" >Central de Administração</a>
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

    <section class="usuarios-container container-editar">
        <div class="usuarios-header">
            <h2>Editar Usuário</h2>
            <div class="retorn">
                <button type="button" onclick="window.location.href='../adm-painel.php?page=usuarios'"><i class='bx bx-x'></i></button>
            </div>
        </div>

        <form action="atualizar-usuario.php" method="POST" class="form-usuario">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">

            <div>
                <span class="form-label">Nome Completo:</span>
                <input type="text" name="nome" value="<?= htmlspecialchars($user['nome']) ?>" required>
            </div>

            <div>
                <span class="form-label">E-mail:</span>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="select-row">
                <div>
                    <span class="form-label">Setor:</span>
                        <select name="setor" required>
                            <option value="Almoxarifado" <?= $user['setor'] == 'Almoxarifado' ? 'selected' : '' ?>>Almoxarifado</option>
                            <option value="Comercial" <?= $user['setor'] == 'Comercial' ? 'selected' : '' ?>>Comercial</option>
                            <option value="Compras" <?= $user['setor'] == 'Compras' ? 'selected' : '' ?>>Compras/Comex</option>
                            <option value="Diretoria" <?= $user['setor'] == 'Diretoria' ? 'selected' : '' ?>>Diretoria</option>
                            <option value="Embalagem" <?= $user['setor'] == 'Embalagem' ? 'selected' : '' ?>>Embalagem</option>
                            <option value="Extrusora" <?= $user['setor'] == 'Extrusora' ? 'selected' : '' ?>>Extrusora</option>
                            <option value="Financeiro" <?= $user['setor'] == 'Financeiro' ? 'selected' : '' ?>>Financeiro</option>
                            <option value="Logistica" <?= $user['setor'] == 'Logistica' ? 'selected' : '' ?>>Logística</option>
                            <option value="Manutenção" <?= $user['setor'] == 'Manutenção' ? 'selected' : '' ?>>Manutenção</option>
                            <option value="Producao" <?= $user['setor'] == 'Producao' ? 'selected' : '' ?>>Produção</option>
                            <option value="RH" <?= $user['setor'] == 'RH' ? 'selected' : '' ?>>RH</option>
                            <option value="Qualidade" <?= $user['setor'] == 'Qualidade' ? 'selected' : '' ?>>Qualidade</option>
                            <option value="Ti" <?= $user['setor'] == 'Ti' ? 'selected' : '' ?>>TI</option>
                        </select>
                </div>

                <div>
                    <span class="form-label">Perfil:</span>
                    <select name="perfil" required>
                        <option value="Admin" <?= $user['perfil'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="Manutenção" <?= $user['perfil'] == 'Manutenção' ? 'selected' : '' ?>>Manutenção</option>
                        <option value="Suporte" <?= $user['perfil'] == 'Suporte' ? 'selected' : '' ?>>Suporte</option>
                        <option value="Padrão" <?= $user['perfil'] == 'Padrão' ? 'selected' : '' ?>>Padrão</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 15px;">
                <span class="form-label">Nova Senha:</span>
                <span class="aviso-senha">* Deixe em branco para manter a senha atual</span>
                <input type="password" name="senha" placeholder="Digite apenas se quiser alterar">
            </div>

            <div class="form-actions" style="margin-top: 20px;">
                <button type="button" class="btn-secondary" onclick="window.location.href='../adm-painel.php?page=usuarios'">Cancelar</button>
                <button type="submit" class="btn-primary">Salvar Alterações</button>
            </div>
        </form>
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