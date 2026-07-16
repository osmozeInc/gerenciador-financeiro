import { abrirModal, fecharModal } from "/assets/js/modais.js";
import { apiFetch, feedbackPopup, removerPopupPeloX} from "/assets/js/utils.js";

let listaTransacoesCompleta;
let saldoTotal;

// Buscar dados ao carregar a página
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const json = await apiFetch('/transacoes/selectDadosTransacoes');
        listaTransacoesCompleta = json.transacoes;

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

function preencherTransacoes(transacoes, tipo) {
    const tabela = document.getElementById('tabelaHistoricoCompleto');
    if (!tabela) return;

    tabela.innerHTML = ''; 

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
        
        const tdAcoes = linha.insertCell();
        tdAcoes.classList.add('col-acoes');
        tdAcoes.innerHTML = `
            <button value="${trans.id_transacao}" class="btn-linha edit js-abrir-modal" data-target="modal-editar-transacao" title="Editar"><i class="bi bi-pencil"></i></button>
            <button value="${trans.id_transacao}" class="btn-linha delete js-abrir-modal" data-target="modal-excluir-transacao" title="Excluir"><i class="bi bi-trash3"></i></button>
        `;
    });

    document.getElementById('qtd-resultados').textContent = transacoes.length;
}
