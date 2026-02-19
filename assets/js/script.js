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

//   --- TIPO DE TICKET (Novo Ticket) ---
document.addEventListener("DOMContentLoaded", function () {
    const selectTipo = document.querySelector('select[name="tipo"]');
    const selectCategoria = document.querySelector('select[name="categoria"]');
    const divPatrimonio = document.querySelector('.patrimonio');

    // 1. Listas de Opções
    const categoriasTI = [
        { valor: 'lorem', texto: 'lorem' }
    ];

    const categoriasManutencao = [
        { valor: 'lorem', texto: 'lorem' }
    ];

    // 2. Função para atualizar categorias
    function atualizarInterface() {
        const tipoSelecionado = selectTipo.value;
        let opcoes = [];
        if (tipoSelecionado === 'Tickets TI') {
            opcoes = categoriasTI;
        } else if (tipoSelecionado === 'Manutenção') {
            opcoes = categoriasManutencao;
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