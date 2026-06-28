import { abrirModal, fecharModal } from "/assets/js/modais.js";
import { apiFetch, feedbackPopup } from "/assets/js/utils.js";

document.getElementById('data').valueAsDate = new Date();

// Buscar dados ao carregar a página
document.addEventListener('DOMContentLoaded', async function() {
    const dados = await apiFetch('/transacoes/selectDados');

    if (dados) {
        preencherCategorias(dados.categorias);
        preencherPagamentos(dados.pagamentos);
        preencherTransacoes(dados.transacoes);
    }
});

// Salvar nova categoria
document.getElementById('novaCategoriaForm').addEventListener('submit', async function(evento) {
    evento.preventDefault(); 

    const copoForm = new FormData(this);
    const resposta = await apiFetch('/categoria/salvar', 'POST', copoForm);

    if (resposta && resposta.sucesso) {
        fecharModal('modal-nova-categoria', null);
        this.reset();

        feedbackPopup(resposta.msgTipo, resposta.mensagem);

        const novosDados = await apiFetch('/transacoes/selectDados');
        if (novosDados) preencherCategorias(novosDados.categorias);
    }
});

// Salvar nova forma de pagamento
document.getElementById('novoPagamentoForm').addEventListener('submit', async function(evento) {
    evento.preventDefault(); 

    const copoForm = new FormData(this);
    const resposta = await apiFetch('/pagamento/salvar', 'POST', copoForm);

    if (resposta && resposta.sucesso) {
        fecharModal('modal-novo-pagamento');
        this.reset();

        feedbackPopup(resposta.msgTipo, resposta.mensagem);

        const novosDados = await apiFetch('/transacoes/selectDados');
        if (novosDados) preencherPagamentos(novosDados.pagamentos);
    }

});

// Salvar nova transação
document.getElementById('transacaoForm').addEventListener('submit', async function(evento) {
    evento.preventDefault(); 

    const copoForm = new FormData(this);
    const resposta = await apiFetch('/transacoes/salvar', 'POST', copoForm);

    if (resposta && resposta.sucesso) {
        this.reset();

        feedbackPopup(resposta.msgTipo, resposta.mensagem);

        const novosDados = await apiFetch('/transacoes/selectDados');
        if (novosDados) preencherTransacoes(novosDados.transacoes);

    }
});


function preencherCategorias(categoriasArray) {
    const mapaGrupos = {
        'R': 'Receitas (R)',
        'D': 'Despesas (D)',
        'I': 'Investimentos (I)',
        'C': 'Cofres (C)'
    };

    for (const [tipo, tipoExato] of Object.entries(mapaGrupos)) {
        const optgroup = document.querySelector(`#categoria optgroup[label="${tipoExato}"]`);
        
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

function preencherPagamentos(pagamentos) {
    const select = document.getElementById('pagamento');
    if (!select) return;

    select.innerHTML = '<option value="" disabled selected>Selecione...</option>';

    pagamentos.forEach(pag => {
        const option = document.createElement('option');
        option.value = pag.id;
        option.textContent = pag.nome;
        select.appendChild(option);
    });

    const optionNovo = document.createElement('option');
    optionNovo.value = 'new';
    optionNovo.textContent = '+ Novo Pagamento';
    select.appendChild(optionNovo);
}

function preencherTransacoes(transacoes) {
    
    const tabela = document.getElementById('tabelaTransacoes');
    if (!tabela) return;

    if (tabela.rows.length > 1) {
        while (tabela.rows.length > 1) {
            tabela.deleteRow(1);
        }
    }

    transacoes = transacoes.slice(0, 10);

    transacoes.forEach(trans => {
        const [ano, mes, dia] = trans.data_transacao.split('-');
        const dataFormatada = `${dia}/${mes}/${ano}`;

        const linha = tabela.insertRow();
        linha.insertCell().textContent = dataFormatada;
        linha.insertCell().textContent = trans.descricao;
        linha.insertCell().textContent = trans.categoria_nome;
        linha.insertCell().textContent = trans.categoria_tipo;
        linha.insertCell().textContent = trans.pagamento_nome;
        linha.insertCell().textContent = trans.parcelas;
        linha.insertCell().textContent = trans.valor;
    });
}