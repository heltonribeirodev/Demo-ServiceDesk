<?php
session_start();
include_once __DIR__ . '/../config/conexao.php';

// 1. Segurança: Só admin entra
if (!isset($_SESSION['id']) || !in_array($_SESSION['perfil'], ['Admin', 'Suporte'])) {
    header("Location: ../adm-painel.php?page=patrimonio&msg=sem_permissao");
    exit;
}

// 2. Verifica se tem ID na URL
if (!isset($_GET['id'])) {
    header("Location: ../adm-painel.php?page=patrimonio");
    exit;
}

$id = intval($_GET['id']);

// 3. Busca os dados atuais do patrimônio
$sql = "SELECT * FROM controle_ativos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../adm-painel.php?page=patrimonio");
    exit;
}

$p = $result->fetch_assoc(); // Usei $p para abreviar 'patrimonio'
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ForteCare | Editar Patrimônio</title>
    <link rel="stylesheet" href="../assets/styles/editar-patrimonio.css">
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
                <a href="../inicio.php">Inicio</a>
                <a href="../meus-tickets.php">Meus Tickets</a>
                <a href="../adm-painel.php"  class="select-a" >Central de Administração</a>
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

    <section class="patrimonio-container container-editar">
        <div class="patrimonio-header">
            <h2>Editar Patrimônio</h2>
            <div class="retorn">
                <button type="button" onclick="window.location.href='../adm-painel.php?page=patrimonio'"><i class='bx bx-x'></i></button>
            </div>
        </div>

        <form action="atualizar-patrimonio.php" method="POST" class="form-patrimonio">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">

            <div>
                <span class="form-label">Matrícula:</span>
                <input type="number" name="matricula" value="<?= htmlspecialchars($p['matricula']) ?>" required>
            </div>

            <div>
                <span class="form-label">Nome do Ativo:</span>
                <input type="text" name="ativo" value="<?= htmlspecialchars($p['ativo']) ?>" required>
            </div>

            <div>
                <span class="form-label">Número Serial / UUID:</span>
                <input type="text" name="numero_serial" value="<?= htmlspecialchars($p['numero_serial']) ?>" required>
            </div>

            <div>
                <span class="form-label">Colaborador Responsável:</span>
                <input type="text" name="colaborador_responsavel" value="<?= htmlspecialchars($p['colaborador_responsavel']) ?>">
            </div>

            <div class="select-row">
                <div>
                    <span class="form-label">Departamento:</span>
                    <select name="departamento" required>
                        <option value="Almoxarifado" <?= $p['departamento'] == 'Almoxarifado' ? 'selected' : '' ?>>Almoxarifado</option>
                        <option value="Comercial" <?= $p['departamento'] == 'Comercial' ? 'selected' : '' ?>>Comercial</option>
                        <option value="Compras" <?= $p['departamento'] == 'Compras' ? 'selected' : '' ?>>Compras/Comex</option>
                        <option value="Diretoria" <?= $p['departamento'] == 'Diretoria' ? 'selected' : '' ?>>Diretoria</option>
                        <option value="Embalagem" <?= $p['departamento'] == 'Embalagem' ? 'selected' : '' ?>>Embalagem</option>
                        <option value="Em estoque" <?= $p['departamento'] == 'Em-Estoque' ? 'selected' : '' ?>>Em Estoque</option>
                        <option value="Extrusora" <?= $p['departamento'] == 'Extrusora' ? 'selected' : '' ?>>Extrusora</option>
                        <option value="Financeiro" <?= $p['departamento'] == 'Financeiro' ? 'selected' : '' ?>>Financeiro</option>
                        <option value="Logistica" <?= $p['departamento'] == 'Logistica' ? 'selected' : '' ?>>Logística</option>
                        <option value="Manutencao" <?= $p['departamento'] == 'Manutencao' ? 'selected' : '' ?>>Manutenção</option>
                        <option value="Producao" <?= $p['departamento'] == 'Producao' ? 'selected' : '' ?>>Produção</option>
                        <option value="Qualidade" <?= $p['departamento'] == 'Qualidade' ? 'selected' : '' ?>>Qualidade</option>
                        <option value="RH" <?= $p['departamento'] == 'RH' ? 'selected' : '' ?>>RH</option>
                        <option value="Ti" <?= $p['departamento'] == 'Ti' ? 'selected' : '' ?>>TI</option>
                        
                    </select>
                </div>

                <div>
                    <span class="form-label">Status:</span>
                    <select name="status" required>
                        <option value="Em Estoque" <?= $p['status'] == 'Em Estoque' ? 'selected' : '' ?>>Em Estoque</option>
                        <option value="Em Uso" <?= $p['status'] == 'Em Uso' ? 'selected' : '' ?>>Em Uso</option>
                        <option value="Em Manutenção" <?= $p['status'] == 'Em Manutenção' ? 'selected' : '' ?>>Em Manutenção</option>
                    </select>
                </div>
            </div>

            <div class="select-row">
                <div>
                    <span class="form-label">Categoria:</span>
                    <select name="categoria" required>
                        <option value="Celular" <?= $p['categoria'] == 'Celular' ? 'selected' : '' ?>>Celular</option>
                        <option value="Computador" <?= $p['categoria'] == 'Computador' ? 'selected' : '' ?>>Computador</option>
                        <option value="Diversos" <?= $p['categoria'] == 'Diversos' ? 'selected' : '' ?>>Diversos</option>
                        <option value="Impressora" <?= $p['categoria'] == 'Impressora' ? 'selected' : '' ?>>Impressora</option>
                        <option value="Monitor" <?= $p['categoria'] == 'Monitor' ? 'selected' : '' ?>>Monitor</option>
                        <option value="Notebook" <?= $p['categoria'] == 'Notebook' ? 'selected' : '' ?>>Notebook</option>
                        <option value="Tablet" <?= $p['categoria'] == 'Tablet' ? 'selected' : '' ?>>Tablet</option>
                        <option value="Televisão" <?= $p['categoria'] == 'Televisão' ? 'selected' : '' ?>>Televisão</option>
                    </select>
                </div>
                
                <div>
                    <span class="form-label">Data de Entrega:</span>
                    <input type="date" name="data_de_entrega" value="<?= $p['data_de_entrega'] ?>">
                </div>
            </div>

            <div>
                <span class="form-label">Detalhes / Periféricos:</span>
                <input type="text" name="detalhes" value="<?= htmlspecialchars($p['detalhes']) ?>">
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="window.location.href='../adm-painel.php?page=patrimonio'">Cancelar</button>
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