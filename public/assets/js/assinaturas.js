import * as utils from "/assets/js/utils.js";
import { abrirModalPorId } from "/assets/js/modais.js";

let assinaturasAtivas = []; 

// ==========================================
// MOCK DO BANCO DE DADOS (DADOS FICTÍCIOS)
// ==========================================
const mockBancoAssinaturas = [
    { id: 1, nome: 'Netflix Premium', valor_mensal: 59.90, dia_vencimento: 10, status: 'ativa' },
    { id: 2, nome: 'Spotify Duo', valor_mensal: 27.90, dia_vencimento: 5, status: 'ativa' },
    { id: 3, nome: 'Amazon Prime', valor_mensal: 19.90, dia_vencimento: 22, status: 'ativa' },
    { id: 4, nome: 'AWS (Hospedagem API)', valor_mensal: 145.50, dia_vencimento: 15, status: 'ativa' },
    { id: 5, nome: 'Adobe Creative Cloud', valor_mensal: 275.00, dia_vencimento: 8, status: 'cancelada' },
    { id: 6, nome: 'ChatGPT Plus', valor_mensal: 110.00, dia_vencimento: 2, status: 'ativa' }
];

document.addEventListener('DOMContentLoaded', async function() {
    try {
        // COMENTADO: A requisição real para o backend
        // const jsonAssinaturas = await utils.apiFetch('/assinaturas/selectAll');
        
        // MOCK: Simulando a resposta estruturada que o backend daria
        const jsonAssinaturas = {
            resposta: { sucesso: true, msgTipo: 'success', mensagem: 'Dados mockados carregados.' },
            assinaturas: mockBancoAssinaturas
        };

        // Mantemos a busca real de métodos de pagamento pois essa tabela já existe no sistema
        const jsonMetodos = await utils.apiFetch('/contaMetodo/selectDados');
        
        if (!jsonAssinaturas?.resposta?.sucesso) {
            utils.feedbackPopup('error', jsonAssinaturas.resposta?.mensagem || 'Erro ao carregar.');
            return;
        }

        if (jsonMetodos?.metodos) preencherMetodos(jsonMetodos.metodos);

        // Filtra as ativas para o dashboard
        assinaturasAtivas = jsonAssinaturas.assinaturas.filter(a => a.status === 'ativa');

        // Popula as seções da tela
        visaoGeralAssinaturas(assinaturasAtivas);
        destaqueProximoVencimento(assinaturasAtivas);
        listarAssinaturas(jsonAssinaturas.assinaturas); // Manda todas (ativas e canceladas) para a grid

    } catch (erro) {
        utils.feedbackPopup('error', 'Ocorreu um erro ao inicializar a tela.');
    }
    utils.esconderLoaderBlur();
});


// Intercepta o envio do formulário de criação
document.getElementById('formCriarAssinatura').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // COMENTADO: O envio real dos dados para o PHP
    // const corpoForm = new FormData(this);
    // const jsonSalvar = await utils.apiFetch('/assinaturas/salvar', 'POST', corpoForm);
    
    // MOCK: Simulando sucesso no cadastro
    const jsonSalvar = { 
        resposta: { sucesso: true, msgTipo: 'success', mensagem: 'Assinatura registrada (Mock)!' } 
    };
    
    if (jsonSalvar?.resposta) {
        utils.feedbackPopup(jsonSalvar.resposta.msgTipo, jsonSalvar.resposta.mensagem);
        
        if (jsonSalvar.resposta.sucesso) {
            this.reset();
            
            // Aqui re-renderizaríamos puxando do banco novamente.
            // Como é mock, apenas recarregamos a visualização atual.
            listarAssinaturas(mockBancoAssinaturas);
            visaoGeralAssinaturas(mockBancoAssinaturas.filter(a => a.status === 'ativa'));
            destaqueProximoVencimento(mockBancoAssinaturas.filter(a => a.status === 'ativa'));
        }
    }
});

// Delegação de Eventos para os Cards do Grid
const gridAssinaturas = document.getElementById('gridAssinaturas');
gridAssinaturas.addEventListener('click', (e) => {
    const card = e.target.closest('.js-abrir-modal-passando-assinatura');
    if (card) {
        const id = card.id;
        const idModal = card.getAttribute('data-target');
        abrirModalPorId(idModal, id);
        
        // No futuro: exibirDetalhesAssinatura(id);
    }
});

function visaoGeralAssinaturas(lista) {
    // Soma os valores mensais
    const totalMensal = lista.reduce((acc, ass) => acc + parseFloat(ass.valor_mensal), 0);
    
    document.getElementById('totalMensalAssinaturas').textContent = totalMensal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    document.getElementById('totalAtivas').textContent = lista.length;
}

function destaqueProximoVencimento(lista) {
    const divConteudo = document.getElementById('focoConteudoAssinatura');
    const divVazio = document.getElementById('focoVazioAssinatura');
    
    if (lista.length === 0) {
        divConteudo.classList.add('hidden');
        divVazio.classList.remove('hidden');
        document.getElementById('assinaturaBadge').style.display = 'none';
        return;
    }

    // Calcula qual assinatura vence mais perto do dia atual
    const hoje = new Date().getDate();
    let proxima = lista.reduce((menor, atual) => {
        const diaAtual = parseInt(atual.dia_vencimento);
        // Se o vencimento é maior/igual hoje, a distância é a diferença.
        // Se já passou, calcula para o mês seguinte (assumindo mês de 30 dias para simplificar o card)
        const distAtual = diaAtual >= hoje ? diaAtual - hoje : (30 - hoje) + diaAtual;
        
        if (!menor) return { ...atual, dist: distAtual };
        return distAtual < menor.dist ? { ...atual, dist: distAtual } : menor;
    }, null);

    const badge = document.getElementById('assinaturaBadge');
    badge.textContent = `Em ${proxima.dist} dia(s)`;
    // Muda a cor do badge se o vencimento for em 3 dias ou menos
    badge.className = proxima.dist <= 3 ? 'badge-foco error' : 'badge-foco warning';

    document.getElementById('assinaturaNomeFoco').textContent = proxima.nome;
    document.getElementById('assinaturaValorFoco').textContent = parseFloat(proxima.valor_mensal).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function listarAssinaturas(lista) {
    gridAssinaturas.innerHTML = '';

    lista.forEach(ass => {
        const statusClass = ass.status === 'ativa' ? 'ativa' : 'cancelada';
        const badgeClass = ass.status === 'ativa' ? 'badge-ativa' : 'badge-inativa';
        
        const card = document.createElement('div');
        card.id = ass.id;
        card.className = `assinatura-card ${statusClass} js-abrir-modal-passando-assinatura`;
        // Deixado engatilhado para quando você criar o modal de detalhes de assinatura
        card.dataset.target = 'modal-detalhes-assinatura'; 

        card.innerHTML = `
            <div class="assinatura-header">
                <span class="assinatura-title">${ass.nome}</span>
                <span class="assinatura-status ${badgeClass}">${ass.status}</span>
            </div>
            <div class="assinatura-valores">
                <span class="valor-atual">${parseFloat(ass.valor_mensal).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</span>
                <span class="dia-vencimento"><i class="bi bi-calendar-event"></i> Dia ${ass.dia_vencimento}</span>
            </div>
        `;
        gridAssinaturas.appendChild(card);
    });
}

function preencherMetodos(metodos) {
    const select = document.getElementById('metodoContaAssinatura');
    select.innerHTML = '<option value="" disabled selected>Selecione...</option>';
    
    metodos.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m.id;
        opt.textContent = m.nome;
        select.appendChild(opt);
    });
}