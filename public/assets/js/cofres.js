// import { abrirModal, fecharModal } from "/assets/js/modais.js";
import * as utils from "/assets/js/utils.js";

let cofres = []; // Variável global para armazenar os cofres

document.addEventListener('DOMContentLoaded', async function() {
    try {
        const jsonCofres = await utils.apiFetch('/cofres/selectDados');
        console.log(jsonCofres);

        if (!jsonCofres?.resposta?.sucesso) {
            utils.feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);
            return;
        }

        visaoGeralCofres(jsonCofres.cofres);
        cofreEmDestaque(jsonCofres.cofres);
        listarCofres(jsonCofres.cofres);

    } catch (erro) {
        utils.feedbackPopup('error', 'Ocorreu um erro ao buscar os dados.');
        utils.feedbackPopup('error', erro);
    }

    utils.esconderLoaderBlur();
});

// Cria um novo cofre
document.getElementById('formCriarCofre').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const corpoForm = new FormData(this);
    const jsonSalvar = await utils.apiFetch('/cofres/salvarCofre', 'POST', corpoForm);
    
    if (jsonSalvar?.resposta) {
        utils.feedbackPopup(jsonSalvar.resposta.msgTipo, jsonSalvar.resposta.mensagem);
        
        if (jsonSalvar.resposta.sucesso) {
            const jsonCofres = await utils.apiFetch('/cofres/selectDados');
            listarCofres(jsonCofres.cofres);
        }
    }
});



// Função para exibir um resumo de todos os cofres e seu progresso no card lateral
function visaoGeralCofres(cofres) {
    let [totalGuardado, metaGlobal] = [0, 0];
    cofres.forEach(cofre => {
        totalGuardado += parseFloat(cofre.valor_total);
        metaGlobal += parseFloat(cofre.valor_meta);
    });

    const porcentagemGlobal = metaGlobal > 0 ? (totalGuardado / metaGlobal) * 100 : 0;
    const pctGlobalBarra = porcentagemGlobal > 100 ? 100 : porcentagemGlobal;

    document.getElementById('totalGuardado').textContent = totalGuardado.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    document.getElementById('globalMeta').textContent = metaGlobal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    document.getElementById('globalBarra').style.width = `${pctGlobalBarra}%`;
    document.getElementById('globalTexto').textContent = `${porcentagemGlobal.toFixed(1)}% do patrimônio planejado alcançado`;
}

// Função para escolher um cofre para destacar
function cofreEmDestaque(cofres) {
    const cofreDestaque = cofres.reduce((cofreDestaque = null, cofreAtual) => {
        const caFaltante = cofreAtual.valor_meta - cofreAtual.valor_total;
        const cdFaltante = cofreDestaque ? cofreDestaque.valor_meta - cofreDestaque.valor_total : null; 
        cofreDestaque = ( cofreDestaque == null || (caFaltante) < (cdFaltante) && (cdFaltante) != 0) ? cofreAtual : cofreDestaque;
        
        return cofreDestaque;
    }, null);

    // corrigir essa lógica
    let cdPercentFaltante = cofreDestaque ? ((cofreDestaque.valor_total / cofreDestaque.valor_meta) * 100) : 0;

    if (cdPercentFaltante == 0) cdPercentFaltante = 100;

    // Atualiza o conteúdo do cofre em destaque
    const badge = document.getElementById('cofreBadge');
    if (cdPercentFaltante >= 0 && cdPercentFaltante <= 60) {badge.textContent = `Falta ${cdPercentFaltante}%`; badge.className = 'badge-foco warning';}
    if (cdPercentFaltante > 60 && cdPercentFaltante <= 80) {badge.textContent = 'Quase lá!'; badge.className = 'badge-foco warning';}
    if (cdPercentFaltante > 80) {badge.textContent = 'Quase Finalizado!'; badge.className = 'badge-foco success';}

    const nome = document.getElementById('cofreNome');
    nome.textContent = cofreDestaque ? cofreDestaque.nome : 'Nenhum cofre disponível';

    const meta = document.getElementById('cofreMeta');
    meta.innerHTML = `
    Falta 
    <strong id="cofreFalta" class>${cofreDestaque ? Number(cofreDestaque.valor_meta - cofreDestaque.valor_total).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '0,00'}</strong> 
    para bater a meta de 
    <strong id="cofreMeta">${cofreDestaque ? Number(cofreDestaque.valor_meta).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '0,00'}</strong>
    `;
}

// lista todos os cofres e seu progresso
function listarCofres(cofres) {
    const gridCofres = document.getElementById('gridCofres');
    gridCofres.innerHTML = '';

    cofres.forEach(cofre => {
        const card = document.createElement('div');
        card.value = cofre.id;
        card.className = 'cofre-card js-abrir-modal-passando-id';
        card.dataset.target = 'modal-detalhes-cofre'

        card.innerHTML = `
            <div class="cofre-header">
                <span class="cofre-title">${cofre.nome}</span>
                <span class="cofre-local">${cofre.local}</span>
            </div>
            <div class="cofre-valores">
                <span class="valor-atual">${Number(cofre.valor_total).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</span>
                <span class="valor-meta">/ ${Number(cofre.valor_meta).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</span>
            </div>
            <div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: ${(cofre.valor_total / cofre.valor_meta) * 100}%"></div>
                </div>
                <div class="progress-text">${((cofre.valor_total / cofre.valor_meta) * 100).toFixed(1)}% Alcançado</div>
            </div>
        `;
        gridCofres.appendChild(card);
    });
}

// Implementação da função que abre e popula o modal
async function exibirCofreCompleto(idCofre) {
    // 1. Encontra os dados base do cofre na memória[cite: 4]
    const cofre = cofres.find(c => c.id === idCofre);
    if (!cofre) return;

    // 2. Popula as informações básicas no DOM do Modal
    document.getElementById('detalhesCofreNome').textContent = cofre.nome;
    document.getElementById('detalhesCofreDescricao').textContent = cofre.descricao || 'Sem descrição definida';
    document.getElementById('detalhesCofreLocal').textContent = cofre.local;
    
    const dataFormatada = cofre.data_criacao ? new Date(cofre.data_criacao).toLocaleDateString('pt-BR') : 'Não registrada';
    document.getElementById('detalhesCofreData').textContent = `Criado em: ${dataFormatada}`;

    // Cálculos de porcentagem
    const vTotal = parseFloat(cofre.valor_total || 0);
    const vMeta = parseFloat(cofre.valor_meta || 0);
    const pct = vMeta > 0 ? (vTotal / vMeta) * 100 : 0;

    document.getElementById('detalhesCofreAtual').textContent = vTotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    document.getElementById('detalhesCofreMeta').textContent = vMeta.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    document.getElementById('detalhesCofreBarra').style.width = `${pct > 100 ? 100 : pct}%`;
    document.getElementById('detalhesCofrePorcentagem').textContent = `${pct.toFixed(1)}% Alcançado`;

    const tbody = document.getElementById('detalhesCofreTransacoes');
    tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 1.5rem; color: var(--text-secondary);">Carregando histórico...</td></tr>';

    // 3. Exibe o modal na tela usando a utilidade do seu projeto[cite: 4]
    document.getElementById('modal-detalhes-cofre').classList.add('active');

    try {
        // 4. Busca o histórico de transações atreladas a este cofre específico
        // Nota: Assuma que você tenha ou criará esse endpoint na sua API
        const jsonTransacoes = await utils.apiFetch(`/transacoes/selectPorCofre?id_cofre=${idCofre}`);
        tbody.innerHTML = '';
        
        if (jsonTransacoes?.sucesso && jsonTransacoes.transacoes.length > 0) {
            jsonTransacoes.transacoes.forEach(trans => {
                const tr = document.createElement('tr');
                tr.style.transition = 'background-color 0.1s';
                
                const tdData = document.createElement('td');
                tdData.style.padding = '0.8rem 0.5rem';
                tdData.style.borderBottom = '1px solid var(--divisor-color)';
                tdData.textContent = new Date(trans.data_transacao).toLocaleDateString('pt-BR');
                
                const tdDesc = document.createElement('td');
                tdDesc.style.padding = '0.8rem 0.5rem';
                tdDesc.style.borderBottom = '1px solid var(--divisor-color)';
                tdDesc.textContent = trans.descricao;
                
                const tdValor = document.createElement('td');
                tdValor.style.padding = '0.8rem 0.5rem';
                tdValor.style.borderBottom = '1px solid var(--divisor-color)';
                tdValor.style.textAlign = 'right';
                tdValor.style.fontWeight = '600';
                tdValor.style.color = 'var(--text-especial)'; // Cor verde baseada no seu :root[cite: 2]
                tdValor.textContent = '+ ' + parseFloat(trans.valor_total).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

                tr.appendChild(tdData);
                tr.appendChild(tdDesc);
                tr.appendChild(tdValor);
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 1.5rem; color: var(--text-secondary);">Nenhuma movimentação registrada neste cofre ainda.</td></tr>';
        }
    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 1.5rem; color: var(--text-danger);">Falha ao carregar os dados.</td></tr>';
    }
}
