import { abrirModal, fecharModal } from "/assets/js/modais.js";

import { apiFetch, feedbackPopup } from "/assets/js/utils.js";

// Buscar dados ao carregar a página
document.addEventListener('DOMContentLoaded', async function() {
    const json = await apiFetch('/transacoes/selectDados');

    if (json) {
        if (!json.resposta.sucesso) {
            feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);
            return;
        }
        
        preencherCategorias(json.categorias);
        preencherMetodos(json.contas);
        preencherTransacoes(json.transacoes);
    }

    atualizarDataAtual();
});

// exibir os blocos corretos de acordo com o botão clicado
const btnsTipoTransacao = document.querySelectorAll('.btn-tipo-transacao')
btnsTipoTransacao.forEach(btn => btn.addEventListener('click', async function() {

    // Se o botão já estiver ativo, desativa e reseta o formulário
    if (this.classList.contains('active')) {
        document.getElementById('aviso').classList.remove('hidden');

        const idForm = this.getAttribute('data-idForm');
        document.getElementById(idForm).classList.remove('visivel-block');
        document.getElementById(idForm).reset();

        this.classList.remove('active');
        btnsTipoTransacao.forEach(b => b.classList.remove('deactive'));

        return;
    };

    // Se o botão nao estiver ativo:
    btnsTipoTransacao.forEach(b => b.classList.remove('active'));
    btnsTipoTransacao.forEach(b => b.classList.add('deactive'));

    document.querySelectorAll('form.visivel-block').forEach(form => form.classList.remove('visivel-block'));
    
    this.classList.add('active');
    this.classList.remove('deactive');
    
    document.getElementById('aviso').classList.add('hidden');
    
    const idForm = this.getAttribute('data-idForm');
    document.getElementById(idForm).classList.add('visivel-block');
}));

// habilitar/desabilitar a quantidade de parcelas
const switchParcelado = document.getElementById('parcelado')
switchParcelado.addEventListener('change', function() {
    const inputParcelas = document.getElementById('input-parcelas');
    
    if (this.checked) {
        inputParcelas.classList.remove('hidden');
    } else {
        inputParcelas.classList.add('hidden');
    }
});

// Salvar nova categoria
document.getElementById('novaCategoriaForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const jsonSalvar = await apiFetch('/categorias/salvar', 'POST', copoForm);

    if (fecharModalExibirFeedback(jsonSalvar, 'modal-nova-categoria', this)) {
        const jsonCategorias = await apiFetch('/categorias/selectDados');
        if (jsonCategorias && jsonCategorias.categorias) preencherCategorias(jsonCategorias.categorias);
    }
});

// Salvar novo metodo de pagamento
document.getElementById('novoPagamentoForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const jsonSalvar = await apiFetch('/contaMetodo/salvar', 'POST', copoForm);

        if (fecharModalExibirFeedback(jsonSalvar, 'modal-novo-pagamento', this)) {
        const jsonMetodos = await apiFetch('/contaMetodo/selectDados');
        if (jsonMetodos) preencherMetodos(jsonMetodos.metodos);
    }
});

// Salvar nova classe de investimento
document.getElementById('novaClasseForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const json = await apiFetch('/classesInvestimento/salvar', 'POST', copoForm);

    if (fecharModalExibirFeedback(jsonSalvar, 'modal-nova-classe', this)) {
        const classes = await apiFetch('/classesInvestimento/selectDados');
        if (classes) preencherClasses(classes.classes);
    }
});

// Salvar nova transação
document.getElementById('receitaForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const jsonSalvar = await apiFetch('/transacoes/salvarReceita', 'POST', copoForm);

    if (fecharModalExibirFeedback(jsonSalvar, null, this)) {
        const novosDados = await apiFetch('/transacoes/selectTransacoes');
        if (novosDados) preencherTransacoes(novosDados.transacoes);
    }
});

document.getElementById('despesaForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const jsonSalvar = await apiFetch('/transacoes/salvarDespesa', 'POST', copoForm);

    if (fecharModalExibirFeedback(jsonSalvar, null, this)) {
        const novosDados = await apiFetch('/transacoes/selectTransacoes');
        if (novosDados) preencherTransacoes(novosDados.transacoes);
    }
});



function fecharModalExibirFeedback(json, idModal, formulario) {
    if (!json || !formulario) return false;

    feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);

    if (json.resposta.sucesso && formulario) 
        formulario.reset();

    if (json.resposta.sucesso && idModal) 
        fecharModal(idModal, formulario);

    return true;
}

function preencherCategorias(categoriasArray) {
    const mapaGrupos = {
        'R': 'Receitas (R)',
        'D': 'Despesas (D)',
        'I': 'Investimentos (I)',
        'C': 'Cofres (C)'
    };

    for (const [tipo, tipoExato] of Object.entries(mapaGrupos)) {
        // CORRIGIDO PARA #categoria_id
        const optgroup = document.querySelector(`#categoria_id optgroup[label="${tipoExato}"]`);
        
        if (optgroup) {
            optgroup.innerHTML = ''; 
            if (categoriasArray[tipo] && categoriasArray[tipo].length > 0) {
                categoriasArray[tipo].forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.nome;
                    optgroup.appendChild(option);
                });
            }
        }
    }
}

function preencherMetodos(contas) {
    // CORRIGIDO PARA #conta_id
    const select = document.getElementById('conta_id');
    if (!select) return;

    select.innerHTML = '<option value="" disabled selected>Selecione...</option>';

    contas.forEach(conta => {
        const option = document.createElement('option');
        option.value = conta.id;
        option.textContent = conta.nome;
        select.appendChild(option);
    });

    const optionNovo = document.createElement('option');
    optionNovo.value = 'new';
    optionNovo.textContent = '+ Nova Conta';
    select.appendChild(optionNovo);
}

function preencherTransacoes(transacoes) {
    const tabela = document.getElementById('tabelaTransacoes');
    if (!tabela) return;

    // Limpa a tabela preservando o cabeçalho (jeito mais performático)
    tabela.innerHTML = ''; 

    transacoes = transacoes.slice(0, 10);

    transacoes.forEach(trans => {
        // Previne erro se a data vier nula do banco
        if(!trans.data_transacao) return; 

        const [ano, mes, dia] = trans.data_transacao.split('-');
        const dataFormatada = `${dia}/${mes}/${ano}`;

        const linha = tabela.insertRow();
        linha.insertCell().textContent = dataFormatada;
        linha.insertCell().textContent = trans.descricao;
        linha.insertCell().textContent = trans.categoria_nome;
        linha.insertCell().textContent = trans.categoria_tipo;
        linha.insertCell().textContent = trans.conta_nome || 'N/A'; // Pode ser nula no cofre
        
        // Formatação profissional de moeda para os alunos verem
        const valorFormatado = parseFloat(trans.valor_total).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        linha.insertCell().textContent = valorFormatado;
    });
}

function preencherClasses(classes) {
    const select = document.getElementById('id_classes');
    if (!select) return;

    select.innerHTML = '<option value="" disabled selected>Selecione...</option>';

    classes.forEach(cofre => {
        const option = document.createElement('option');
        option.value = cofre.id;
        option.textContent = cofre.nome;
        select.appendChild(option);
    });

    const optionNovo = document.createElement('option');
    optionNovo.value = 'new';
    optionNovo.textContent = '+ Nova Classe';
    select.appendChild(optionNovo);
}

function preencherCofres(cofres) {
    const select = document.getElementById('id_cofre');
    if (!select) return;

    select.innerHTML = '<option value="" disabled selected>Selecione...</option>';

    cofres.forEach(cofre => {
        const option = document.createElement('option');
        option.value = cofre.id;
        option.textContent = cofre.nome;
        select.appendChild(option);
    });
}

function atualizarDataAtual() {
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.valueAsDate = new Date();
    });
}