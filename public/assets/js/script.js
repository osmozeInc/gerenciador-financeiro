
/* FUNÇÕES DE ABRIR E FECHAR MODAL */

function abrirModal(idModal) {
    document.getElementById(idModal).classList.add('active');
}

function fecharModal(idModal, idFormulario) {
    document.getElementById(idModal).classList.remove('active');

    if (idFormulario) document.getElementById(idFormulario).reset();
}