import { abrirModal, fecharModal } from "/assets/js/modais.js";
import * as utils from "/assets/js/utils.js";

let listaTransacoes = [];

// Buscar dados ao carregar a página
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const json = await utils.apiFetch('/transacoes/selectDados100Transacoes');
        listaTransacoes = json.transacoes;

        if (!json.resposta.sucesso) {
            utils.feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);
            return;
        }
            
        renderizarResumoInvestimentos(json.transacoes, null);
    }
    catch (erro) {
        utils.feedbackPopup('error', 'Ocorreu um erro ao buscar os dados.');
        utils.feedbackPopup('error', erro);
    }

    utils.esconderLoaderTabela();
});


function renderizarResumoInvestimentos(transacoes) {
    const listaContainer = document.getElementById('listaAlocacaoClasses');
    const totalDisplay = document.getElementById('totalInvestidoDisplay');
    if (!listaContainer || !totalDisplay) return;

    // 1. Filtra o array global pegando apenas o que for Investimento ('I')
    const investimentos = transacoes.filter(t => t.categoria_tipo === 'I');

    // 2. Calcula o valor total investido na carteira
    const totalGeral = investimentos.reduce((acc, curr) => acc + parseFloat(curr.valor_total || 0), 0);
    totalDisplay.textContent = totalGeral.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

    if (totalGeral === 0) {
        listaContainer.innerHTML = '<p style="color: var(--text-secondary); font-size: 0.9rem;">Nenhum investimento registrado.</p>';
        return;
    }

    // 3. Agrupa e soma os valores por Classe usando reduce (Criando um Dicionário/Objeto)
    const totaisPorClasse = investimentos.reduce((acc, curr) => {
        const classe = curr.classe_nome || 'Outros';
        acc[classe] = (acc[classe] || 0) + parseFloat(curr.valor_total || 0);
        return acc;
    }, {});

    // 4. Limpa o container e renderiza calculando as porcentagens
    listaContainer.innerHTML = '';
    
    Object.entries(totaisPorClasse).forEach(([classe, valor]) => {
        const porcentagem = ((valor / totalGeral) * 100).toFixed(1);
        const valorFormatado = valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

        const itemHtml = `
            <div class="alocacao-item">
                <div class="alocacao-info">
                    <span class="classe-nome">${classe}</span>
                    <span class="classe-valores">${valorFormatado} (<strong>${porcentagem}%</strong>)</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: ${porcentagem}%;"></div>
                </div>
            </div>
        `;
        listaContainer.insertAdjacentHTML('beforeend', itemHtml);
    });
}