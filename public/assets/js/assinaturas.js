import * as utils from "/assets/js/utils.js";
import { abrirModalPorId, fecharModal } from "/assets/js/modais.js";

let assinaturasGlobais = []; 
let metodosPagamentoGlobais = []; 

document.addEventListener('DOMContentLoaded', async function() {
    try {
        // Agora busca direto do banco de dados (PHP -> PostgreSQL)
        const [jsonAssinaturas, jsonMetodos] = await Promise.all([
            utils.apiFetch('/assinaturas/selectAll'),
            utils.apiFetch('/contaMetodo/selectDados')
        ]);
        
        if (!jsonAssinaturas?.resposta?.sucesso) {
            utils.feedbackPopup('error', jsonAssinaturas.resposta?.mensagem || 'Erro ao carregar.');
            return;
        }

        if (jsonMetodos?.metodos) {
            metodosPagamentoGlobais = jsonMetodos.metodos;
            preencherMetodos(metodosPagamentoGlobais);
        }

        // Armazena no state global e atualiza a interface
        assinaturasGlobais = jsonAssinaturas.assinaturas;
        atualizarInterfaceGlobal();

    } catch (erro) {
        utils.feedbackPopup('error', 'Ocorreu um erro ao inicializar a tela.');
    }
    utils.esconderLoaderBlur();
});

// Envia os dados REAIS para o Controller PHP
document.getElementById('formCriarAssinatura').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const corpoForm = new FormData(this);
    const jsonSalvar = await utils.apiFetch('/assinaturas/salvar', 'POST', corpoForm);
    
    if (jsonSalvar?.resposta) {
        utils.feedbackPopup(jsonSalvar.resposta.msgTipo, jsonSalvar.resposta.mensagem);
        
        if (jsonSalvar.resposta.sucesso) {
            this.reset();
            recarregarDados(); // Busca a lista atualizada do banco
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
        exibirDetalhesAssinatura(id); 
    }
});

// Ação de Cancelar/Reativar Assinatura (Conexão com a API)
document.getElementById('btnAlternarStatusAssinatura').addEventListener('click', async function() {
    const modal = document.getElementById('modal-detalhes-assinatura');
    const idAssinatura = parseInt(modal.getAttribute('data-id'));
    
    const jsonAlternar = await utils.apiFetch(`/assinaturas/alternarStatus/${idAssinatura}`, 'POST');
    
    if (jsonAlternar?.resposta) {
        utils.feedbackPopup(jsonAlternar.resposta.msgTipo, jsonAlternar.resposta.mensagem);
        if (jsonAlternar.resposta.sucesso) {
            fecharModal('modal-detalhes-assinatura');
            recarregarDados(); // Busca a lista atualizada do banco
        }
    }
});

// ==========================================
// FUNÇÕES DE INTERFACE E LÓGICA
// ==========================================

// Função auxiliar para re-buscar do banco
async function recarregarDados() {
    const jsonAssinaturas = await utils.apiFetch('/assinaturas/selectAll');
    if (jsonAssinaturas?.resposta?.sucesso) {
        assinaturasGlobais = jsonAssinaturas.assinaturas;
        atualizarInterfaceGlobal();
    }
}

function atualizarInterfaceGlobal() {
    const assinaturasAtivas = assinaturasGlobais.filter(a => a.status === 'ativa');
    visaoGeralAssinaturas(assinaturasAtivas);
    destaqueProximoVencimento(assinaturasAtivas);
    listarAssinaturas(assinaturasGlobais);
}

async function exibirDetalhesAssinatura(id) {
    utils.exibirLoaderBlurModal();
    await new Promise(r => setTimeout(r, 100)); // Delay para transição visual

    const assinatura = assinaturasGlobais.find(a => parseInt(a.id) === parseInt(id));
    if (!assinatura) return;

    // Busca o nome do método de pagamento
    const nomeConta = assinatura.conta_nome || 'Conta Padrão';

    document.getElementById('detalhesAssNome').textContent = assinatura.nome;
    document.getElementById('detalhesAssVencimento').innerHTML = `<i class="bi bi-calendar-event"></i> Vence todo dia ${assinatura.dia_vencimento}`;
    document.getElementById('detalhesAssValor').textContent = parseFloat(assinatura.valor_mensal).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    document.getElementById('detalhesAssConta').textContent = nomeConta;

    const spanStatus = document.getElementById('detalhesAssStatus');
    const btnAcao = document.getElementById('btnAlternarStatusAssinatura');

    if (assinatura.status === 'ativa') {
        spanStatus.textContent = 'Ativa';
        spanStatus.className = 'badge-foco success'; 
        spanStatus.style.backgroundColor = 'var(--bg-badge-success)';
        spanStatus.style.color = 'var(--text-especial)';
        
        btnAcao.textContent = 'Cancelar Assinatura';
        btnAcao.style.color = 'var(--text-danger)';
        btnAcao.style.borderColor = 'var(--text-danger)';
    } else {
        spanStatus.textContent = 'Cancelada';
        spanStatus.className = 'badge-foco';
        spanStatus.style.backgroundColor = 'var(--bg-badge)';
        spanStatus.style.color = 'var(--text-secondary)';

        btnAcao.textContent = 'Reativar Assinatura';
        btnAcao.style.color = 'var(--text-especial)';
        btnAcao.style.borderColor = 'var(--text-especial)';
    }

    utils.esconderLoaderBlurModal();
}

function visaoGeralAssinaturas(lista) {
    const totalMensal = lista.reduce((acc, ass) => acc + parseFloat(ass.valor_mensal), 0);
    document.getElementById('totalMensalAssinaturas').textContent = totalMensal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    document.getElementById('totalAtivas').textContent = lista.length;
}

function destaqueProximoVencimento(lista) {
    const divConteudo = document.getElementById('focoConteudoAssinatura');
    const divVazio = document.getElementById('focoVazioAssinatura');
    const badge = document.getElementById('assinaturaBadge');
    
    if (lista.length === 0) {
        divConteudo.classList.add('hidden');
        divVazio.classList.remove('hidden');
        badge.style.display = 'none';
        return;
    }

    divConteudo.classList.remove('hidden');
    divVazio.classList.add('hidden');

    const hoje = new Date().getDate();
    let proxima = lista.reduce((menor, atual) => {
        const diaAtual = parseInt(atual.dia_vencimento);
        const distAtual = diaAtual >= hoje ? diaAtual - hoje : (30 - hoje) + diaAtual;
        
        if (!menor) return { ...atual, dist: distAtual };
        return distAtual < menor.dist ? { ...atual, dist: distAtual } : menor;
    }, null);

    badge.style.display = 'inline-block';
    badge.textContent = `Em ${proxima.dist} dia(s)`;
    
    if(proxima.dist <= 3) {
        badge.className = 'badge-foco error';
        badge.style.backgroundColor = 'var(--bg-badge-danger)';
        badge.style.color = 'var(--text-danger)';
    } else {
        badge.className = 'badge-foco warning';
        badge.style.backgroundColor = 'var(--bg-badge-warning)';
        badge.style.color = 'var(--text-warning)';
    }

    document.getElementById('assinaturaNomeFoco').textContent = proxima.nome;
    document.getElementById('assinaturaValorFoco').textContent = parseFloat(proxima.valor_mensal).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function listarAssinaturas(lista) {
    const gridAssinaturas = document.getElementById('gridAssinaturas');
    gridAssinaturas.innerHTML = '';

    const listaOrdenada = [...lista].sort((a, b) => {
        if (a.status === 'ativa' && b.status !== 'ativa') return -1;
        if (a.status !== 'ativa' && b.status === 'ativa') return 1;
        return a.nome.localeCompare(b.nome);
    });

    listaOrdenada.forEach(ass => {
        const statusClass = ass.status === 'ativa' ? 'ativa' : 'cancelada';
        const badgeClass = ass.status === 'ativa' ? 'badge-ativa' : 'badge-inativa';
        
        const card = document.createElement('div');
        card.id = ass.id;
        card.className = `assinatura-card ${statusClass} js-abrir-modal-passando-assinatura`;
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