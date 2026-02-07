<?php
// Garante que a sessão está iniciada para ler o perfil
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../config/conexao.php';

$sql = "SELECT * FROM controle_ativos ORDER BY id DESC";
$result = $conn->query($sql);

// 1. DEFINIR PERMISSÃO
// Admin e Suporte podem gerenciar. Manutenção apenas vê.
$perfilUsuario = isset($_SESSION['perfil']) ? trim($_SESSION['perfil']) : '';
$podeGerenciar = ($perfilUsuario === 'Admin' || $perfilUsuario === 'Suporte');
?>

<script src="../assets/js/script.js"></script>
<link rel="stylesheet" href="assets/styles/patrimonio.css">

<div class="patrimonio-container">
    <div class="patrimonio-header">
        <h2>
            Controle de Patrimônio
        </h2>
        <div class="retorn">
           <button type="button" onclick="window.location.href='adm-painel.php'"><i class='bx bx-x'></i></button>
        </div>
    </div>

    <div class="card-actions">
        <div class="patrimonio-actions">
            <input type="text" placeholder="Buscar patrimônio..." class="search">
            
            <button class="btn-new-patrimonio" onclick="abrirNovoPatrimonio()">
                <i class='bx bxs-purchase-tag'></i> Novo Patrimônio
            </button>
            <button class="btn-new-maintenance" onclick="abrirManutencao()">
                <i class='bx bx-cog'></i> Nova Manutenção
            </button>
        </div>

    <?php if (isset($_GET['msg'])): ?>
        
        <?php if ($_GET['msg'] == 'sem_permissao' || $_GET['msg'] == 'erro_permissao'): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0px; border-radius: 5px; border: 1px solid #f5c6cb; display:flex; align-items:center; gap:10px;">
                <i class='bx bxs-error-circle' style="font-size: 24px;"></i>
                <span><strong>Acesso Negado:</strong> Seu perfil não tem permissão para realizar esta ação.</span>
            </div>
        <?php endif; ?>

    <?php endif; ?>

        <div class="novo-patrimonio-box" id="novoPatrimonioBox" style="display: none;">
            <h3>Cadastro de Patrimônio</h3>

            <form action="adm/salvar-patrimonio.php" method="post" class="form-patrimonio">
                
                <div>
                    <span class="form-label">Ativo</span>
                    <input type="text" name="ativo" placeholder="Ex: PC i5 - 1335U" required class="ativo">
                </div>

                 <div>
                    <span class="form-label">Matrícula</span>
                    <input type="number" name="matricula" placeholder="Código de Patrimônio" required class="matricula">
                </div>
                
                <div>
                    <span class="form-label">Número de Série</span>
                    <input type="text" name="numero_serial" placeholder="uuid / nº série" required>
                </div>
                
                <div>
                    <span class="form-label">Colaborador Responsável</span>
                    <input type="text" name="colaborador_responsavel" placeholder="Colaborador Responsável">
                </div>
                
                <div>
                    <span class="form-label">Departamento</span>
                    <select name="departamento" required class="departamento">
                        <option value="" disabled selected>Departamento / Setor</option>
                        <option value="Almoxarifado">Almoxarifado</option>
                        <option value="Comercial">Comercial</option>
                        <option value="Compras">Compras/Comex</option>
                        <option value="Diretoria">Diretoria</option>
                        <option value="Embalagem">Embalagem</option>
                        <option value="Em-Estoque">Em Estoque</option>
                        <option value="Extrusora">Extrusora</option>
                        <option value="Financeiro">Financeiro</option>
                        <option value="Logistica">Logística</option>
                        <option value="Producao">Produção</option>
                        <option value="Qualidade">Qualidade</option>
                        <option value="RH">RH</option>
                        <option value="Ti">TI</option>
                        
                    </select>
                </div>
                
                <div>
                    <span class="form-label">Status</span>
                    <select name="status" required>
                        <option value="" disabled selected>Status</option>
                        <option>Em Estoque</option>
                        <option>Em Uso</option>
                        <option>Em Manutenção</option>
                    </select>
                </div>
                
                <div>
                    <span class="form-label">Categoria</span>
                    <select name="categoria" required>
                        <option value="" disabled selected>Categoria</option>
                        <option>Celular</option>
                        <option>Computador</option>
                        <option>Diversos</option>
                        <option>Impressora</option>
                        <option>Monitor</option>
                        <option>Notebook</option>
                        <option>Tablet</option>
                        <option>Televisão</option>
                    </select>
                </div>
                
                <div>
                    <span class="form-label">Detalhes / Periféricos</span>
                    <input type="text" name="detalhes" placeholder="Ex: Teclado e mouse">
                </div>
                
                <div>
                    <span class="form-label">Data de Entrega</span>
                    <input type="date" name="data_de_entrega">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="fecharNovoPatrimonio()">Cancelar</button>
                    <button type="reset" class="btn-clear">Limpar dados <i class='bx bxs-eraser'></i></button>
                    <button type="submit" class="btn-primary">Cadastrar Patrimônio</button>
                </div>
            </form>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'sucesso-patrimonio'): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 15px; margin: 20px 0px; border-radius: 5px; border: 1px solid #c3e6cb;">
                <i class='bx bxs-check-circle'></i> Patrimônio cadastrado com sucesso.
            </div>
        <?php endif; ?>



        <div class="nova-manutencao-box" id="novaManutencaoBox" style="display: none;">
            <h3>Cadastro de Manutenção</h3>

            <form action="adm/salvar-manutencao.php" method="post" class="form-patrimonio">

                 <div>
                    <span class="form-label">Matrícula</span>
                    <input type="number" name="matricula" placeholder="Código de Patrimônio" required class="matricula">
                </div>
                
                <div>
                    <span class="form-label">Data do Ocorrido</span>
                    <input type="date" name="data_ocorrido" required>
                </div>

                <div>
                    <span class="form-label">Data de Solução</span>
                    <input type="date" name="data_resolvido" required>
                </div>
                <div>
                    <span class="form-label">Problema Técnico</span>
                    <input type="text" name="defeito" placeholder="Ex: Substituição de fonte" required>
                </div>
                <div>
                    <span class="form-label">Custo (R$)</span>
                    <input type="number" name="valor" placeholder="Valor total da manutenção" required step="0.01">
                </div>
                <div>
                <span class="form-label">Técnico Responsável:</span>
                <input type="text" name="responsavel" value="<?= $_SESSION['nome'] ?>" required>
            </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="fecharManutencao()">Cancelar</button>
                    <button type="reset" class="btn-clear">Limpar dados <i class='bx bxs-eraser'></i></button>
                    <button type="submit" class="btn-primary">Cadastrar Manutenção</button>
                </div>
                
            </form>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'matricula_nao_encontrada'): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0px; border-radius: 5px; border: 1px solid #f5c6cb; display:flex; align-items:center; gap:10px;">
        <i class='bx bxs-error-circle' style="font-size: 24px;"></i>
                <span><strong>Erro:</strong> A matrícula informada não foi encontrada no sistema.</span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'sucesso-manutencao'): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 15px; margin: 20px 0px; border-radius: 5px; border: 1px solid #c3e6cb;">
                <i class='bx bxs-check-circle'></i> Manutenção cadastrada com sucesso. (Verifique o relatório de patrimonio para detalhes.)
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'sucesso-exclusao'): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 15px; margin: 20px 0px; border-radius: 5px; border: 1px solid #c3e6cb;">
                <i class='bx bxs-check-circle'></i> Patrimônio excluído com sucesso.
            </div>
        <?php endif; ?>


    <div class="table-wrapper">
        <table class="patrimonio-table">
            <thead> 
                <tr>
                    <th>Matricula</th>
                    <th>Ativo</th>
                    <th>Categoria</th>
                    <th>Status</th>
                    <th>Departamento</th>
                    <th>Colaborador</th>
                    <th>Ações</th> 
                </tr>
            </thead>

            <tbody>
                <?php 
                // Verifica se tem resultados
                if ($result && $result->num_rows > 0): 
                    // Loop para cada linha do banco
                    while ($row = $result->fetch_assoc()): 
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['matricula']) ?></td>
                        <td><?= htmlspecialchars($row['ativo']) ?></td>
                        <td><?= htmlspecialchars($row['categoria']) ?></td>
                        
                        <td>
                            <span class="status-badge <?= strtolower(str_replace(' ', '-', $row['status'])) ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                        
                        <td><?= htmlspecialchars($row['departamento']) ?></td>
                        <td><?= htmlspecialchars($row['colaborador_responsavel']) ?></td>
                        
                        <td>
                            <div class="action-buttons">
                                <a href="adm/editar-patrimonio.php?id=<?= $row['id'] ?>" class="btn-icon edit">
                                    <i class='bx bx-edit'></i>
                                </a>

                                <button type="button" onclick="confirmarExclusao(<?= $row['id'] ?>)" class="btn-icon delete">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </div>
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
        // Lógica da Barra de Busca (Filtro)
    // Isso assume que existe uma tabela com a classe .usuarios-table na página
 
        const searchInput = document.querySelector('.search');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const termo = this.value.toLowerCase();
            const linhas = document.querySelectorAll('table tbody tr'); // Ajuste o seletor da sua tabela se necessário
            
            linhas.forEach(linha => {
                const texto = linha.innerText.toLowerCase();
                linha.style.display = texto.includes(termo) ? '' : 'none';
            });
        });
    }

    function confirmarExclusao(id) {
    if (confirm("Tem certeza que deseja excluir este item?")) {
        window.location.href = 'adm/excluir-patrimonio.php?id=' + id;
    }
}
</script>