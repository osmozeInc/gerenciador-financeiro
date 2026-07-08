import { abrirModal, fecharModal } from "/assets/js/modais.js";
import { apiFetch, feedbackPopup } from "/assets/js/utils.js";

let formAtualizados = {
    'R': false,
    'D': false,
    'I': false,
    'C': false
}

// Buscar dados ao carregar a página
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const json = await apiFetch('/transacoes/selectDadosTransacoes');
        
        if (!json.resposta.sucesso) {
            feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);
            return;
        }
            
        preencherTransacoes(json.transacoes, null);
    }
    catch (erro) {
        feedbackPopup('error', 'Ocorreu um erro ao buscar os dados.');
    }
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

    await receberDadosDoBotao(this);
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

// Salvar nova transação de receita
document.getElementById('receitaForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const jsonSalvar = await apiFetch('/transacoes/salvarReceita', 'POST', copoForm);

    if (fecharModalExibirFeedback(jsonSalvar, null, this)) {
        const novosDados = await apiFetch('/transacoes/selectTransacoes');
        if (novosDados) preencherTransacoes(novosDados.transacoes, 'R');
    }
});

// Salvar nova transação de despesa
document.getElementById('despesaForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const jsonSalvar = await apiFetch('/transacoes/salvarDespesa', 'POST', copoForm);

    if (fecharModalExibirFeedback(jsonSalvar, null, this)) {
        const novosDados = await apiFetch('/transacoes/selectTransacoes');
        if (novosDados) preencherTransacoes(novosDados.transacoes, 'D');
    }
});

// Salvar nova transação de investimento
document.getElementById('investimentoForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const jsonSalvar = await apiFetch('/transacoes/salvarInvestimento', 'POST', copoForm);

    if (fecharModalExibirFeedback(jsonSalvar, null, this)) {
        const novosDados = await apiFetch('/transacoes/selectTransacoes');
        if (novosDados) preencherTransacoes(novosDados.transacoes, 'I');
    }
});

// Salvar nova transação para o cofre
document.getElementById('cofreForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const jsonSalvar = await apiFetch('/transacoes/salvarNoCofre', 'POST', copoForm);

    if (fecharModalExibirFeedback(jsonSalvar, null, this)) {
        const novosDados = await apiFetch('/transacoes/selectTransacoes');
        if (novosDados) preencherTransacoes(novosDados.transacoes, 'C');
    }
});



async function receberDadosDoBotao(botao) {
    const tipo = botao.getAttribute('data-idForm').charAt(0).toUpperCase();
    atualizarDataAtual(tipo);

    if (formAtualizados[tipo]) return;

    try {
        if (tipo === 'R') {
            const json = await apiFetch('/categorias/selectDadosReceita');
            preencherCategorias(json.categorias, tipo);
        } else if (tipo === 'D') {
            const json = await apiFetch('/categorias/selectDadosDespesa');
            preencherCategorias(json.categorias, tipo);
        } else if (tipo === 'I') {
            const json = await apiFetch('/classesInvestimento/selectDados');
            preencherClasses(json.classes);
        } else if (tipo === 'C') {
            const json = await apiFetch('/cofres/selectDados');
            preencherCofres(json.cofres);
        }

        const jsonMetodos = await apiFetch('/contaMetodo/selectDados');
        preencherMetodos(jsonMetodos.metodos, tipo);
        
        formAtualizados[tipo] = true;
    }
    catch (erro) {
        console.error(erro);
        feedbackPopup('error', 'erro:' + erro);
    }
}

function fecharModalExibirFeedback(json, idModal, formulario) {
    if (!json) return false;

    feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);

    if (json.resposta.sucesso && formulario) 
        formulario.reset();

    if (json.resposta.sucesso && idModal) 
        fecharModal(idModal, formulario);

    return true;
}

function preencherCategorias(jsonCategorias, tipo) {
    const select = document.getElementById('categorias' + tipo);

    jsonCategorias.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = cat.nome;
        select.appendChild(option);
    });
    
    const optionNovo = document.createElement('option');
    optionNovo.value = 'new';
    optionNovo.textContent = '+ Nova Categoria';
    select.appendChild(optionNovo);
}

function preencherMetodos(metodos, tipo) {
    const select = document.getElementById('metodoConta' + tipo);

    metodos.forEach(metodo => {
        const option = document.createElement('option');
        option.value = metodo.id;
        option.textContent = metodo.nome;
        select.appendChild(option);
    });

    const optionNovo = document.createElement('option');
    optionNovo.value = 'new';
    optionNovo.textContent = '+ Nova Conta';
    select.appendChild(optionNovo);
}

function preencherTransacoes(transacoes, tipo) {
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

    atualizarDataAtual(tipo);
}

function preencherClasses(classes) {
    const select = document.getElementById('classeInvestimento');

    classes.forEach(classe => {
        const option = document.createElement('option');
        option.value = classe.id;
        option.textContent = classe.nome;
        select.appendChild(option);
    });

    const optionNovo = document.createElement('option');
    optionNovo.value = 'new';
    optionNovo.textContent = '+ Nova Classe';
    select.appendChild(optionNovo);
}

function preencherCofres(cofres) {
    const select = document.getElementById('cofresCofre');

    cofres.forEach(cofre => {
        const option = document.createElement('option');
        option.value = cofre.id;
        option.textContent = cofre.nome;
        select.appendChild(option);
    });
}

function atualizarDataAtual(tipo) {
    if (!tipo) return;
    const input = document.getElementById('data' + tipo);
    input.valueAsDate = new Date();
}