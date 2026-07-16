import { abrirModal, fecharModal } from "/assets/js/modais.js";
import { apiFetch, feedbackPopup, removerPopupPeloX} from "/assets/js/utils.js";

let listaTransacoesCompleta;
let saldoTotal;

// Buscar dados ao carregar a página
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const jsonTransacoes = await apiFetch('/transacoes/selectDadosTransacoes');
        const jsonCategorias = await apiFetch('/categorias/selectDados');
        const jsonMetodos = await apiFetch('/contaMetodo/selectDados');
        
        if (!jsonTransacoes.resposta.sucesso) {
            feedbackPopup(jsonTransacoes.resposta.msgTipo, json.resposta.mensagem);
            return;
        }
        
        if (!jsonCategorias.resposta.sucesso) {
            feedbackPopup(jsonCategorias.resposta.msgTipo, json.resposta.mensagem);
            return;
        }
        
        if (!jsonMetodos.resposta.sucesso) {
            feedbackPopup(jsonMetodos.resposta.msgTipo, json.resposta.mensagem);
            return;
        }

        listaTransacoesCompleta = jsonTransacoes.transacoes;
        preencherTransacoes(jsonTransacoes.transacoes);
        preencherCategorias(jsonCategorias.categorias);
        preencherMetodos(jsonMetodos.metodos);
    }
    catch (erro) {
        feedbackPopup('error', erro);
    }
});

// altera a categoria de acordo com o tipo
const selectTipo = document.getElementById('filtroTipo');
selectTipo.addEventListener('change', function() {
    const tipoSelecionado = this.value;
    
    Array.from(selectCat.options).forEach(option => {
        if (option.value === "") return; 
        
        const tipoDaOpcao = option.getAttribute('data-tipo');
        
        if (tipoSelecionado === "" || tipoDaOpcao === tipoSelecionado) {
            option.style.display = ''; 
        } else {
            option.style.display = 'none'; 
        }
    });

    selectCat.value = ""; 
});

//altera o tipo de acordo com a categoria
const selectCat = document.getElementById('filtroCategoria');
selectCat.addEventListener('change', function() {
    const opcaoSelecionada = this.options[this.selectedIndex];
    const tipoDaCategoria = opcaoSelecionada.getAttribute('data-tipo');
    
    if (tipoDaCategoria) selectTipo.value = tipoDaCategoria;
});

document.getElementById('formFiltros')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const descricao  = document.getElementById('filtroDescricao').value.trim().toLowerCase();
    const tipo       = document.getElementById('filtroTipo').value;
    const catVal     = document.getElementById('filtroCategoria').value;
    const contaVal   = document.getElementById('filtroConta').value;
    const dataInicio = document.getElementById('filtroDataInicio').value;
    const dataFim    = document.getElementById('filtroDataFim').value;
    
    const valorPiso  = document.getElementById('filtroValorPiso').value !== '' ? parseFloat(document.getElementById('filtroValorPiso').value) : null;
    const valorTeto  = document.getElementById('filtroValorTeto').value !== '' ? parseFloat(document.getElementById('filtroValorTeto').value) : null;

    const filtrados = listaTransacoesCompleta.filter(trans => {
        if (descricao && !trans.descricao.toLowerCase().includes(descricao)) return false;
        if (tipo && trans.categoria_tipo !== tipo) return false;
        if (catVal && String(trans.categoria_id ?? trans.categoria_nome) !== String(catVal)) return false;
        if (contaVal && String(trans.conta_id ?? trans.conta_nome) !== String(contaVal)) return false;
        if (dataInicio && trans.data_transacao < dataInicio) return false;
        if (dataFim && trans.data_transacao > dataFim) return false;

        const valorTransacao = parseFloat(trans.valor_total ?? trans.valor);
        if (valorPiso !== null && valorTransacao < valorPiso) return false;
        if (valorTeto !== null && valorTransacao > valorTeto) return false;
        
        return true;
    });

    preencherTransacoes(filtrados);
    feedbackPopup('info', `${filtrados.length} transações encontradas.`);
});

// BÔNUS DIDÁTICO: Ação do botão "Limpar Filtros" que estava órfão no seu HTML
document.getElementById('btn-limpar-filtros')?.addEventListener('click', function() {
    // Reseta o formulário fisicamente na tela
    document.getElementById('formFiltros').reset();
    
    // Devolve a lista master original sem filtros para a tabela
    preencherTransacoes(listaTransacoesCompleta);
    feedbackPopup('info', 'Filtros removidos.');
});


function preencherTransacoes(transacoes) {
    const tabela = document.getElementById('tabelaHistoricoCompleto');
    if (!tabela) return;

    tabela.innerHTML = ''; 
    saldoTotal = 0;

    transacoes.forEach(trans => {
        if(!trans.data_transacao) return; 

        const [ano, mes, dia] = trans.data_transacao.split('-');
        const dataFormatada = `${dia}/${mes}/${ano}`;
        
        const valorFormatado = parseFloat(trans.valor_total).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

        const linha = tabela.insertRow();
        linha.insertCell().textContent = dataFormatada;
        linha.insertCell().textContent = trans.descricao;
        linha.insertCell().textContent = trans.categoria_nome;
        linha.insertCell().textContent = trans.conta_nome;
        
        const tdValor = linha.insertCell();
        tdValor.classList.add('col-valor', `tipo-${trans.categoria_tipo}`);
        tdValor.textContent = `${trans.categoria_tipo === 'R' ? '+' : '-'} ${valorFormatado}`;
        saldoTotal += trans.categoria_tipo === 'R' ? parseFloat(trans.valor_total) : -parseFloat(trans.valor_total);
        
        const tdAcoes = linha.insertCell();
        tdAcoes.classList.add('col-acoes');
        tdAcoes.innerHTML = `
            <button value="${trans.id_transacao}" class="btn-linha edit js-abrir-modal" data-target="modal-editar-transacao" title="Editar"><i class="bi bi-pencil"></i></button>
            <button value="${trans.id_transacao}" class="btn-linha delete js-abrir-modal" data-target="modal-excluir-transacao" title="Excluir"><i class="bi bi-trash3"></i></button>
        `;
    });

    document.getElementById('valorTotal').textContent = saldoTotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    document.getElementById('qtd-resultados').textContent = transacoes.length;
}

function preencherCategorias(jsonCategorias) {
    const select = document.getElementById('filtroCategoria');
    const arrayCategorias = [
        jsonCategorias['R'],
        jsonCategorias['D'],
        jsonCategorias['I'],
        jsonCategorias['C']
    ]
    
    arrayCategorias.forEach(categorias => {
        categorias.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.nome;
            option.textContent = cat.nome;
            option.setAttribute('data-tipo', cat.tipo);
            select.appendChild(option);
        });
    });
}

function preencherMetodos(metodos) {
    const select = document.getElementById('filtroConta');
    metodos.forEach(metodo => {
        const option = document.createElement('option');
        option.value = metodo.nome;
        option.textContent = metodo.nome;
        select.appendChild(option);
    });
}