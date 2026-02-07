/* Tela de login */

function abrirSuporte(e) {
    e.preventDefault();
    document.getElementById('modalSuporte').style.display = 'flex';
}

function fecharSuporte() {
    document.getElementById('modalSuporte').style.display = 'none';
}


document.addEventListener('DOMContentLoaded', function() {
    const inputSenha = document.getElementById('inputSenha');
    const btnToggle = document.getElementById('btnToggleSenha');

    if (inputSenha && btnToggle) {

        // 1. Evento de Digitação (Monitora se tem texto ou não)
        inputSenha.addEventListener('input', function() {
            if (this.value.length > 0) {
                // Se tem texto e o ícone ainda é o cadeado, muda para Olho
                if (btnToggle.classList.contains('bxs-lock-alt')) {
                    btnToggle.classList.remove('bxs-lock-alt');
                    btnToggle.classList.add('bx-show');
                }
            } else {
                // Se limpou o campo, volta para Cadeado e esconde senha
                btnToggle.className = 'bx bxs-lock-alt'; // Reseta classes
                inputSenha.setAttribute('type', 'password'); // Garante que volte a ser password
            }
        });

        // 2. Evento de Clique (Só funciona se não for o cadeado)
        btnToggle.addEventListener('click', function() {
            // Se for cadeado, apenas foca no input e não faz nada
            if (this.classList.contains('bxs-lock-alt')) {
                inputSenha.focus();
                return;
            }

            // Lógica de alternar visibilidade
            const tipoAtual = inputSenha.getAttribute('type');
            
            if (tipoAtual === 'password') {
                inputSenha.setAttribute('type', 'text');
                this.classList.replace('bx-show', 'bx-hide'); // Olho riscado
            } else {
                inputSenha.setAttribute('type', 'password');
                this.classList.replace('bx-hide', 'bx-show'); // Olho aberto
            }
        });
    }
});

/* Botão de "minha conta"*/

document.addEventListener("DOMContentLoaded", function () {
    const btnUsuario = document.getElementById("btnUsuario");
    const dropdown = document.getElementById("userDropdown");

    // Abrir / fechar ao clicar no botão
    btnUsuario.addEventListener("click", function (e) {
        e.preventDefault();
        dropdown.classList.toggle("active");
    });

    // Fechar ao clicar fora
    document.addEventListener("click", function (e) {
        if (!e.target.closest(".user-menu")) {
            dropdown.classList.remove("active");
        }
    });
});

     /* Mudar senha "Minha conta" */
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function () {

            const input = document.getElementById(this.dataset.target);

            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                this.classList.remove('bx-show');
                this.classList.add('bx-hide');
            } else {
                input.type = 'password';
                this.classList.remove('bx-hide');
                this.classList.add('bx-show');
            }
        });
    });

});

/* Novo Ticket Upload */
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('upload');
    const listaArquivos = document.getElementById('lista-arquivos');
    const form = document.querySelector('form'); // Seleciona o formulário

    // Função que verifica e escreve na tela
    function atualizarLista() {
        // Limpa o conteúdo atual da div
        listaArquivos.innerHTML = '';

        // Verifica se o input tem arquivos E se a lista não está vazia
        if (fileInput.files && fileInput.files.length > 0) {
            
            // SE TEM ARQUIVOS: Cria a lista
            const ul = document.createElement('ul');
            ul.style.listStyle = 'none';
            ul.style.padding = '0';
            ul.style.margin = '0';
            
            for (let i = 0; i < fileInput.files.length; i++) {
                const li = document.createElement('li');
                li.innerHTML = `
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:5px;">
                        <i class='bx bx-check-circle' style='color:var(--blue)'></i> 
                        <span style="color: #35457b; font-weight:600;">${fileInput.files[i].name}</span>
                    </div>`;
                ul.appendChild(li);
            }
            listaArquivos.appendChild(ul);
        } else {
            // SE NÃO TEM ARQUIVOS (ou foi resetado): Mostra o aviso
            listaArquivos.innerHTML = `
                <div style="display:flex; align-items:center; gap:8px; color:var(--grey);">
                    <i class='bx bx-info-circle'></i>
                    <span style="font-style: italic;">Nenhum arquivo anexado ainda.</span>
                </div>`;
        }
    }

    if (fileInput && listaArquivos) {
        // 1. Chama ao carregar a página
        atualizarLista();

        // 2. Chama quando o usuário seleciona um arquivo
        fileInput.addEventListener('change', atualizarLista);

        // 3. NOVO: Chama quando o botão "Limpar dados" é clicado
        if (form) {
            form.addEventListener('reset', function() {
                // O setTimeout é necessário para esperar o navegador limpar o input nativo
                // antes de atualizarmos o visual
                setTimeout(atualizarLista, 10);
            });
        }
    }
});


/* Tela de Relatorios (views) */
    const cards = document.querySelectorAll('.card-item');
    const views = document.querySelectorAll('.view');

    cards.forEach(card => {
        card.addEventListener('click', () => {
            const view = card.dataset.view;

            views.forEach(v => v.classList.add('hidden'));

            document
                .getElementById(`view-${view}`)
                .classList.remove('hidden');
        });
    });

/* Menu hamburguer */

document.addEventListener("DOMContentLoaded", () => {
    const menuToggle = document.getElementById("menuToggle");
    const navigation = document.getElementById("navigation");

    if (!menuToggle || !navigation) return;

    menuToggle.addEventListener("click", () => {
        navigation.classList.toggle("active");

        const icon = menuToggle.querySelector("i");
        icon.classList.toggle("bx-menu");
        icon.classList.toggle("bx-x");
    });
});
document.querySelectorAll('.navigation a').forEach(link => {
    link.addEventListener('click', () => {
        navigation.classList.remove('active');

        const icon = menuToggle.querySelector("i");
        icon.classList.add("bx-menu");
        icon.classList.remove("bx-x");
    });
});


/* Chamdados */

    // Função de Excluir 
function confirmarExclusaoChamado(id) {
    // Pergunta se tem certeza
    if (confirm("Tem certeza que deseja excluir este chamado permanentemente?\nIsso apagará também todo o histórico de conversa.")) {

    window.location.href = 'adm/excluir-chamado.php?id=' + id;
    }
}

    function confirmarExclusaoChamado(id) {
    if (confirm("Tem certeza que deseja excluir este chamado permanentemente?")) {
         window.location.href = 'adm/excluir-chamado.php?id=' + id;
    }
}


/* Patrimonio */

function abrirNovoPatrimonio() {
        const boxPatrimonio = document.getElementById('novoPatrimonioBox');
        const boxManutencao = document.getElementById('novaManutencaoBox');

        // 1. FECHA A OUTRA CAIXA (O Segredo está aqui)
        if (boxManutencao) boxManutencao.style.display = 'none';

        // 2. Mostra a caixa de Patrimônio (Toggle: se estiver aberta, fecha; se fechada, abre)
        if (boxPatrimonio.style.display === 'none' || boxPatrimonio.style.display === '') {
            boxPatrimonio.style.display = 'block';
            // Opcional: Rolar a página até o formulário
            boxPatrimonio.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            boxPatrimonio.style.display = 'none';
        }
    }

    function abrirManutencao() {
        const boxPatrimonio = document.getElementById('novoPatrimonioBox');
        const boxManutencao = document.getElementById('novaManutencaoBox');

        // 1. FECHA A OUTRA CAIXA
        if (boxPatrimonio) boxPatrimonio.style.display = 'none';

        // 2. Mostra a caixa de Manutenção
        if (boxManutencao.style.display === 'none' || boxManutencao.style.display === '') {
            boxManutencao.style.display = 'block';
            boxManutencao.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            boxManutencao.style.display = 'none';
        }
    }

    // --- FUNÇÕES DE FECHAMENTO (Botões Cancelar) ---

    function fecharNovoPatrimonio() {
        document.getElementById('novoPatrimonioBox').style.display = 'none';
    }

    function fecharManutencao() {
        document.getElementById('novaManutencaoBox').style.display = 'none';
    }

 /* Usuário */
 
    // Funções do Modal Novo Usuário (que você já tem)
    function abrirNovoUsuario() {
        document.getElementById('novoUsuarioBox').style.display = 'block';
    }

    function fecharNovoUsuario() {
        document.getElementById('novoUsuarioBox').style.display = 'none';
    }

    // --- NOVA FUNÇÃO (Adicione esta parte) ---
    function confirmarExclusao(id) {
        if (confirm("Tem certeza que deseja excluir este usuário?")) {
            // Se clicar em OK, ele vai para o arquivo de exclusão
            window.location.href = 'adm/excluir-usuario.php?id=' + id;
        }
    }

//   --- TIPO DE TICKET (Novo Ticket) ---
document.addEventListener("DOMContentLoaded", function() {
    const selectTipo = document.querySelector('select[name="tipo"]');
    const selectCategoria = document.querySelector('select[name="categoria"]');
    const divPatrimonio = document.querySelector('.patrimonio'); // Seleciona a div pai para esconder tudo

    // 1. Listas de Opções
    const categoriasTI = [
        { valor: '3CX', texto: '3CX (Ramal mudo, falhas, etc...)' },
        { valor: 'Cigam', texto: 'Cigam ERP (Erros de acesso, Relatórios, etc...)' },
        { valor: 'Hardware', texto: 'Hardware (Falha em computadores, Perifeticos, etc...)' },
        { valor: 'Impressora', texto: 'Impressora (Cartuchos, Papel Travado, etc...)' },
        { valor: 'Infraestrutura', texto: 'Infraestrutura (Cabeamento, Organização, etc...)' },
        { valor: 'Rede', texto: 'Rede (Instabilidade, VPN, etc...)' },
        { valor: 'Outros', texto: 'Outros (Dúvidas, instalações, etc...)' }
    ];

    const categoriasManutencao = [
        { valor: 'ArCondicionado', texto: 'Ar Condicionado' },
        { valor: 'Civil', texto: 'Civil (Paredes/Pisos)' },
        { valor: 'Eletrica', texto: 'Elétrica' },
        { valor: 'Jardinagem', texto: 'Jardinagem' },
        { valor: 'Limpeza', texto: 'Limpeza' },
        { valor: 'Maquina', texto: 'Máquinas' },
        { valor: 'Moveis', texto: 'Móveis/Mobília' },
        { valor: 'Pneumatica', texto: 'Pneumática' },
        { valor: 'Outros', texto: 'Outros (Escreva no Assunto)' }
    ];

    // 2. Função para atualizar categorias
    function atualizarInterface() {
        const tipoSelecionado = selectTipo.value;
        let opcoes = [];

        // No seu HTML o valor é "Tickets TI" e "Manutenção"
        if (tipoSelecionado === 'Tickets TI') {
            opcoes = categoriasTI;
            divPatrimonio.style.display = 'block'; // TI geralmente precisa de patrimônio
        } else if (tipoSelecionado === 'Manutenção') {
            opcoes = categoriasManutencao;
            // Exemplo: Se for manutenção predial (limpeza/jardinagem), talvez não precise de patrimônio
            divPatrimonio.style.display = 'block'; 
        }

        // Limpa e preenche o select de categorias
        selectCategoria.innerHTML = '<option value="" disabled selected>Selecione uma categoria...</option>';
        
        opcoes.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.valor;
            option.textContent = cat.texto;
            selectCategoria.appendChild(option);
        });
    }

    // 3. Event Listener para mudanças
    selectTipo.addEventListener('change', atualizarInterface);
});