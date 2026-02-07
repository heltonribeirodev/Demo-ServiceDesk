<?php
// Garante que a sessão está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../config/conexao.php';

// 1. DEFINIÇÃO DE PERMISSÃO (Igual ao código de Patrimônio)
// Limpa o perfil para evitar erros com espaços
$perfilUsuario = isset($_SESSION['perfil']) ? trim($_SESSION['perfil']) : '';

// Lista de quem pode acessar essa página
$perfisPermitidos = ['Admin', 'Suporte', 'Manutenção']; 

// 2. O GUARDIÃO (Verificação de Segurança)
// Se não estiver logado OU o perfil não estiver na lista permitida...
if (!isset($_SESSION['id']) || !in_array($perfilUsuario, $perfisPermitidos)) {
    header("Location: ../inicio.php"); // Manda embora
    exit;
}

// --- DAQUI PARA BAIXO O CÓDIGO PERMANECE IGUAL ---

$sql = "SELECT id, nome, email, setor, perfil FROM usuarios ORDER BY nome";
$result = $conn->query($sql);

if ($result === false) {
    die("Erro na consulta: " . $conn->error);
}

if (isset($_GET['msg']) && $_GET['msg'] == 'criado') {
    echo "<script>alert('Usuário cadastrado com sucesso!');</script>";
}
?>

<script src="../assets/js/script.js"></script>
<link rel="stylesheet" href="assets/styles/adm.css">

<div class="usuarios-container">

    <div class="usuarios-header">
        <h2>Central de Usuários</h2>
        <div class="retorn">
            <button type="button" onclick="window.location.href='adm-painel.php'"><i class='bx bx-x'></i></button>
        </div>
    </div>

    <div class="card-actions">
        <div class="usuarios-actions">
            <input type="text" placeholder="Buscar usuário..." class="search">
            <button class="btn-primary" onclick="abrirNovoUsuario()">
                <i class='bx bx-user-plus'></i> Novo Usuário
            </button>

        </div>
    </div>



    <?php if (isset($_GET['msg'])): ?>
        
        <?php if ($_GET['msg'] == 'sem_permissao' || $_GET['msg'] == 'erro_permissao'): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0px; border-radius: 5px; border: 1px solid #f5c6cb; display:flex; align-items:center; gap:10px;">
                <i class='bx bxs-error-circle' style="font-size: 24px;"></i>
                <span><strong>Acesso Negado:</strong> Seu perfil não tem permissão para realizar esta ação.</span>
            </div>
    <?php endif; ?>

        <?php if ($_GET['msg'] == 'erro_self' || $_GET['msg'] == 'erro_self'): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0px; border-radius: 5px; border: 1px solid #f5c6cb; display:flex; align-items:center; gap:10px;">
                <i class='bx bxs-error-circle' style="font-size: 24px;"></i>
                <span><strong>Acesso Negado:</strong> Você não pode excluir seu próprio usuário.</span>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'erro_hierarquia'): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0px; border-radius: 5px; border: 1px solid #f5c6cb; display:flex; align-items:center; gap:10px;">
                <i class='bx bxs-error-circle' style="font-size: 24px;"></i>
            <span><strong>Ação Bloqueada:</strong> O perfil Suporte não pode editar e nem excluir Administradores.</span>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'erro_permissao_admin'): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0px; border-radius: 5px; border: 1px solid #f5c6cb; display:flex; align-items:center; gap:10px;">
                <i class='bx bxs-error-circle' style="font-size: 24px;"></i>
            <span><strong>Ação Bloqueada:</strong> O perfil Suporte não pode promover um usuário a Admin.</span>
        </div>
    <?php endif; ?>



    <div class="novo-usuario-box" id="novoUsuarioBox">
        <h3>Cadastro de Usuário</h3>

        <form action="adm/salvar-usuario.php" method="POST" class="form-usuario">
            <div>
                <span class="form-label">Nome Completo</span>
                <input type="text" name="nome" placeholder="Nome completo" required>
            </div>

            <div>
                <span class="form-label">E-mail</span>
                <input type="email" name="email" placeholder="E-mail" required>
            </div>

            <div class="select-row">
                <div>
                    <span class="form-label">Setor</span>
                    <select name="setor" required>
                        <option value="" disabled selected>Selecione uma opção</option>
                        <option value="Almoxarifado">Almoxarifado</option>
                        <option value="Comercial">Comercial</option>
                        <option value="Compras">Compras/Comex</option>
                        <option value="Diretoria">Diretoria</option>
                        <option value="Embalagem">Embalagem</option>
                        <option value="Extrusora">Extrusora</option>
                        <option value="Financeiro">Financeiro</option>
                        <option value="Logistica">Logística</option>
                        <option value="Manutenção">Manutenção</option>
                        <option value="Producao">Produção</option>
                        <option value="Qualidade">Qualidade</option>
                        <option value="RH">RH</option>
                        <option value="Ti">TI</option>
                    </select>
                </div>

                <div>
                    <span class="form-label">Tipo de acesso</span>
                    <select name="perfil" required>
                        <option value="" disabled selected>Selecione uma opção</option>
                        <option>Admin</option>
                        <option>Manutenção</option>
                        <option>Suporte</option>
                        <option>Padrão</option>
                    </select>
                </div>
            </div>


            <div>
                <span class="form-label">Senha</span>
                <input type="password" name="senha" placeholder="Senha" required>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="fecharNovoUsuario()">Cancelar</button>
                <button class="btn-clear" type="reset">Limpar dados <i class='bx bxs-eraser'></i></button>
                <button type="submit" class="btn-primary"><i class='bx bx-user-plus'></i>Criar Usuário</button>
            </div>
        </form>
    </div>


    <div class="table-wrapper">
        <table class="usuarios-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Setor</th>
                    <th>Perfil</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($usuario = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['nome']) ?></td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td><?= htmlspecialchars($usuario['setor']) ?></td>
                            <td>
                                <span
                                    class="badge <?= strtolower($usuario['perfil']) ?>"><?= htmlspecialchars($usuario['perfil']) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn-icon edit"
                                    onclick="window.location.href='adm/editar-usuario.php?id=<?= $usuario['id'] ?>'">
                                    <i class='bx bx-edit'></i>
                                </button>

                                <button class="btn-icon delete" onclick="confirmarExclusao(<?= $usuario['id'] ?>)">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.querySelector('.search').addEventListener('keyup', function () {
        const termo = this.value.toLowerCase();
        const linhas = document.querySelectorAll('.usuarios-table tbody tr');

        linhas.forEach(linha => {
            const texto = linha.innerText.toLowerCase();
            linha.style.display = texto.includes(termo) ? '' : 'none';
        });
    });

    function abrirNovoUsuario() {
        document.getElementById('novoUsuarioBox').style.display = 'block';
    }

    function fecharNovoUsuario() {
        document.getElementById('novoUsuarioBox').style.display = 'none';
    }

    // Função de busca (que você já tem)
    document.querySelector('.search').addEventListener('keyup', function () {
        const termo = this.value.toLowerCase();
        const linhas = document.querySelectorAll('.usuarios-table tbody tr');

        linhas.forEach(linha => {
            const texto = linha.innerText.toLowerCase();
            linha.style.display = texto.includes(termo) ? '' : 'none';
        });
    });

</script>
   