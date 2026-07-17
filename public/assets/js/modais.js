/* ESCUTAS DO JS */

// abrir modais
document.body.addEventListener('click', (e) => {
    const btnAbrirModal = e.target.closest('.js-abrir-modal');
    if (btnAbrirModal) {
        const idModal = btnAbrirModal.getAttribute('data-target');
        abrirModal(idModal);
        return;
    }

    // fechar modais
    const btnFecharModal = e.target.closest('.js-fechar-modal');
    if (btnFecharModal) {
        const idModal = btnFecharModal.getAttribute('data-target');
        fecharModal(idModal);
        return;
    }

    // trocar modais
    const btnTrocarModal = e.target.closest('.js-trocar-modal');
    if (btnTrocarModal) {
        const idModalFechar = btnTrocarModal.getAttribute('data-target-close');
        const idModalAbrir = btnTrocarModal.getAttribute('data-target');
        toggleModal(idModalFechar, idModalAbrir);
        return;
    }

    const btnAbrirModalValue = e.target.closest('.js-abrir-modal-passando-value');
    if (btnAbrirModalValue) {
        const value = btnAbrirModalValue.getAttribute('value');
        const idModal = btnAbrirModalValue.getAttribute('data-target');
        abrirModalPorValue(idModal, value);
        return;
    }
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

export function abrirModalPorValue(idModal, value) {
    abrirModal(idModal);
    document.querySelector(`#${idModal} form`).setAttribute('data-idTransacao', value);
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

export function toggleClassById(idElemento, classCss) {
    document.getElementById(idElemento).classList.toggle(classCss);
}

export function toggleClassByQuery(classElement, classCss) {
    document.querySelector(classElement).classList.toggle(classCss);
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
    toggleClassByQuery('.bi-eye', 'hidden');
    toggleClassByQuery('.bi-eye-slash', 'hidden');

    if (visibilidade) {
        // esconder os saldos
    } 
    else {
        // mostrar os saldos
    }
}