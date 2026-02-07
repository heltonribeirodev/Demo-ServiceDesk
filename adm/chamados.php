<?php
// 1. Iniciar Sessão e Conexão
if (!isset($_SESSION)) {
    session_start();
}
include_once __DIR__ . '/../config/conexao.php';

$perfilUsuario = $_SESSION['perfil'] ?? ''; 

// 2. Captura o filtro da URL (Se não tiver nada, fica vazio)
$filtroStatus = isset($_GET['filtro']) ? $_GET['filtro'] : '';

// --- MONTAGEM DINÂMICA DO SQL ---

// Começamos a query básica
$sql = "SELECT c.*, u.nome AS nome_usuario 
        FROM chamados c 
        LEFT JOIN usuarios u ON c.usuario_id = u.id ";

// Array para guardar as regras do WHERE
$condicoes = [];

// A. REGRA DE PERFIL (Segurança)
// Se for Manutenção, só vê manutenção. Se for TI, só vê TI.
if ($perfilUsuario == 'Manutenção' || $perfilUsuario == 'Manutencao') {
    $condicoes[] = "c.tipo = 'Manutenção'";
} elseif ($perfilUsuario == 'Suporte' || $perfilUsuario == 'TI') {
    $condicoes[] = "c.tipo = 'Tickets TI'";
}

// B. REGRA DO FILTRO (O que você selecionou no dropdown)
if ($filtroStatus == 'pendentes') {
    // Pendentes = Tudo que NÃO está 'Concluido' (Aberto, Em Andamento, Resolvido)
    $condicoes[] = "c.status != 'Concluido'";
} elseif ($filtroStatus == 'concluidos') {
    // Apenas os fechados
    $condicoes[] = "c.status = 'Concluido'";
}

// C. A MÁGICA: Junta tudo com "AND"
if (count($condicoes) > 0) {
    // Transforma o array em: "WHERE c.tipo = 'Manutenção' AND c.status != 'Concluido'"
    $sql .= " WHERE " . implode(" AND ", $condicoes);
}

// 4. Ordenação Final
$sql .= " ORDER BY c.data_abertura DESC";

// Executa a busca
$result = $conn->query($sql);
?>

    <script src="../assets/js/script.js"></script>

<div class="usuarios-container">
    <div class="usuarios-header">
        <h2>Gerenciar Chamados</h2>
        <div class="retorn">
           <button type="button" onclick="window.location.href='adm-painel.php'"><i class='bx bx-x'></i></button>
        </div>
    </div>

<div class="filtros-container">
    <input type="text" id="searchTickets" class="search" placeholder="Buscar neste filtro (Assunto, ID, Nome)...">

    <select class="select-filtro" 
        onchange="window.location.href='?page=chamados&filtro=' + this.value">
        <option value="">Todos os Tickets</option>
        
        <option value="pendentes" <?= $filtroStatus == 'pendentes' ? 'selected' : '' ?>>
            Pendentes (Abertos/Resolvidos)
        </option>
        
        <option value="concluidos" <?= $filtroStatus == 'concluidos' ? 'selected' : '' ?>>
            Concluídos (Histórico)
        </option>
    </select>
</div>





            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'erro_permissao'): ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0px; border-radius: 5px; border: 1px solid #f5c6cb; display:flex; align-items:center; gap:10px;">
        <i class='bx bxs-error-circle' style="font-size: 24px;"></i>
        <span><strong>Acesso Negado:</strong> Apenas Administradores podem excluir chamados.</span>
    </div>
<?php endif; ?>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'excluido'): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 15px; margin: 20px 0px; border-radius: 5px; border: 1px solid #c3e6cb;">
        <i class='bx bxs-check-circle'></i> Chamado excluído com sucesso.
    </div>
<?php endif; ?>




    <div class="table-responsive">
        <table class="usuarios-table" id="tabelaChamados">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Solicitante</th>
                    <th>Tipo</th>
                    <th>Patrimônio</th>
                    <th>Assunto</th>
                    <th>Prioridade</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Concluido</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($ticket = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?= $ticket['id'] ?></strong></td>
                            
                            <td class="quebra-texto">
                                <?= $ticket['nome_usuario'] ? htmlspecialchars($ticket['nome_usuario']) : '<span style="color:red">Usuário Excluído</span>' ?>
                            </td>
                            <td><?= htmlspecialchars($ticket['tipo']) ?></td>

                            <td><?= htmlspecialchars($ticket['patrimonio']) ?></td>

                            <td class="quebra-texto"><?= htmlspecialchars($ticket['assunto']) ?></td>

                            <td>
                                <span class="priority <?= strtolower($ticket['prioridade']) ?>">
                                    <?= htmlspecialchars($ticket['prioridade']) ?>
                                </span>
                            </td>

                            <td>
                                <?php 
                                    $cssStatus = strtolower(str_replace(' ', '-', $ticket['status'])); 
                                ?>
                                <span class="status-badge <?= $cssStatus ?>">
                                    <?= htmlspecialchars($ticket['status']) ?>
                                </span>
                            </td>

                            <td><?= date('d/m/y H:i', strtotime($ticket['data_abertura'])) ?></td>
                            
                            <td  class="<?= empty($ticket['data_conclusao']) ? 'align-center' : '' ?>">
                                <?= !empty($ticket['data_conclusao'])
                                    ? date('d/m/y H:i', strtotime($ticket['data_conclusao']))
                                    : '-' ?>
                            </td>
                            
                            <td>
                                <div class="actions">
                                    <button class="btn-icon edit" title="Ver/Responder"
                                        onclick="window.location.href='adm/ver-chamado.php?id=<?= $ticket['id'] ?>'">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    
                                    <button class="btn-icon delete" title="Excluir Chamado" 
                                        onclick="confirmarExclusaoChamado(<?= $ticket['id'] ?>)">
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
        // Filtro da barra de pesquisa
    document.getElementById('searchTickets').addEventListener('keyup', function() {
        const termo = this.value.toLowerCase();
        const linhas = document.querySelectorAll('#tabelaChamados tbody tr');

        linhas.forEach(linha => {
            const texto = linha.innerText.toLowerCase();
            linha.style.display = texto.includes(termo) ? '' : 'none';
        });
    });
</script>