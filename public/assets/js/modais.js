import * as generic from "./generic.js"

/* ESCUTAS DO JS */

// abrir modais
const btnAbrirModal = document.querySelectorAll('.js-abrir-modal');
btnAbrirModal.forEach(btn => {
    btn.addEventListener('click', () => {
        const idModal = btn.getAttribute('data-target');
        abrirModal(idModal);
    });
});

// fechar modais
const btnFecharModal = document.querySelectorAll('.js-fechar-modal');
btnFecharModal.forEach(btn => {
    btn.addEventListener('click', () => {
        const idModal = btn.getAttribute('data-target');
        fecharModal(idModal);
    });
});

// trocar modais
const btnTrocarModal = document.querySelectorAll('.js-trocar-modal');
btnTrocarModal.forEach(btn => {
    btn.addEventListener('click', () => {
        const idModalFechar = btn.getAttribute('data-target-close');
        const idModalAbrir = btn.getAttribute('data-target');
        toggleModal(idModalFechar, idModalAbrir);
    });
});

// abrir modal dos selects
const categoriaSelect = document.querySelectorAll('.js-abrir-modal-select');
categoriaSelect.forEach(select => select.addEventListener('change', (e) => {
    const selectedOption = e.target.options[e.target.selectedIndex];
    
    const idModal = e.target.getAttribute('data-target');
    const tipo = selectedOption.getAttribute('data-tipo');

    if(selectedOption.value === 'new') {
        abrirModalPorOption(idModal, tipo);
        e.target.value = "";
    }
    })
);

// troca de tema
const btnTema = document.querySelector('.switch-theme input');
btnTema.addEventListener('click', () => {
    let tema = (btnTema.checked) ? "light" : "dark"
    definirTema(tema);
});

// visibilidade dos saldos
const btnVisibilidade = document.querySelectorAll('#visibilidade i');
btnVisibilidade.forEach(btn => {
    btn.addEventListener('click', () => {
        const visibilidade = (btn.getAttribute('data-visibilidade') === 'true') ? true : false;
        definirVisibilidadeDeValores(visibilidade);
    });
});


/* FUNÇÕES DE ABRIR, FECHAR E TROCAR MODAL */

export function abrirModal(idModal) {
    document.getElementById(idModal).classList.add('active');

    travarRolagemDaPagina();
}

export function fecharModal(idModal, idFormulario) {
    document.getElementById(idModal).classList.remove('active');

    if (idFormulario) document.getElementById(idFormulario).reset();

    destravarRolagemDaPagina();
}

function abrirModalPorOption(idModal, tipo) {
    document.getElementById(idModal).classList.add('active');
    document.getElementById(idModal).setAttribute('data-tipo', tipo);
    
    if (tipo === 'R' || tipo === 'D') {
        const select = document.getElementById('tipoCategoriaModal');
        const option = document.createElement('option');
        option.value = tipo;
        option.textContent = tipo;
        option.name = 'tipo';
        option.selected = true;

        select.appendChild(option);
        select.classList.add('bloqueado');
    }
}

function toggleModal(idModalFechar, idModalAbrir) {
    document.getElementById(idModalFechar).classList.remove('active');
    document.getElementById(idModalAbrir).classList.add('active');
}


/* FUNÇÕES DE TRAVA E DESTRAVA ROLAGEM DA PAGINA */

function travarRolagemDaPagina() {
    document.body.classList.add('travar-scroll');
}

function destravarRolagemDaPagina() {
    document.body.classList.remove('travar-scroll');
}


/* FUNÇÕES DE TROCA DE TEMA */

export function definirTema(tema) {
    const htmlElement = document.documentElement;

    htmlElement.setAttribute('data-theme', tema);
    localStorage.setItem('tema', tema);

    if (tema === 'light') document.querySelector('.switch-theme input').checked = true;
    else document.querySelector('.switch-theme input').checked = false;

    //trocar logo da página
}


/* FUNÇÕES DE VISIBILIDADE DOS SALDOS */

export function definirVisibilidadeDeValores(visibilidade) {
    generic.toggleClassByQuery('.bi-eye', 'hidden');
    generic.toggleClassByQuery('.bi-eye-slash', 'hidden');

    if (visibilidade) {
        // esconder os saldos
    } 
    else {
        // mostrar os saldos
    }
}