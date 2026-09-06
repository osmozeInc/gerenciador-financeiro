// import { abrirModal, fecharModal } from "/assets/js/modais.js";
import * as utils from "/assets/js/utils.js";
import { abrirModal, abrirModalPorId } from "/assets/js/modais.js";

let cofres = []; // Variável global para armazenar os cofres

document.addEventListener('DOMContentLoaded', async function() {
    try {
        const jsonCofres = await utils.apiFetch('/cofres/selectDados');
        cofres = jsonCofres.cofres;

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

document.querySelectorAll('.js-fechar-modal').forEach(btnFechar => {
    btnFechar.addEventListener('click', () => {
        console.log('Fechando modal...');
        utils.exibirLoaderBlurModal();
        utils.exibirLoaderTabela();
        document.getElementById('detalhesCofreTransacoes').innerHTML = '';
    });
});

const gridCofres = document.getElementById('gridCofres');
gridCofres.addEventListener('click', (e) => {
    const btnAbrirModalId = e.target.closest('.js-abrir-modal-passando-cofre');
    
    if (btnAbrirModalId) {
        const id = btnAbrirModalId.id;
        const idModal = btnAbrirModalId.getAttribute('data-target');
        
        abrirModalPorId(idModal, id);
        exibirCofreCorreto(id); 
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
    document.getElementById('globalTexto').textContent = `${porcentagemGlobal.toFixed(0)}% do patrimônio planejado alcançado`;
}

// Função para escolher um cofre para destacar
function cofreEmDestaque(cofres) {
    const cofreDestaque = cofres.reduce((cofreDestaque = null, cofreAtual) => {
        const caFaltante = cofreAtual.valor_meta - cofreAtual.valor_total;
        const cdFaltante = cofreDestaque ? cofreDestaque.valor_meta - cofreDestaque.valor_total : null; 

        if (cdFaltante == null || cofreDestaque == null) 
            cofreDestaque = cofreAtual;
        else if ((caFaltante) < (cdFaltante) && (caFaltante) > 0)
            cofreDestaque = cofreAtual;
        
        return cofreDestaque;
    }, null);

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
        card.id = cofre.id;
        card.className = 'cofre-card js-abrir-modal-passando-cofre';
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
                <div class="progress-text">${((cofre.valor_total / cofre.valor_meta) * 100).toFixed(0)}% Alcançado</div>
            </div>
        `;
        gridCofres.appendChild(card);
    });
}

// Implementação da função que abre e popula o modal
async function exibirCofreCorreto(idCofre) {
    try {
        const jsonCofre = await utils.apiFetch(`/cofres/selectCofre/${idCofre}`);

        if (!jsonCofre || !jsonCofre.resposta || !jsonCofre.resposta.sucesso) {
            utils.feedbackPopup('error', 'Erro ao carregar os detalhes do cofre.');
            return;
        }

        const c = jsonCofre.cofre;
        const modal = document.querySelector('#modal-detalhes-cofre');

        // 1. Nova Função Helper para textos puros (usando textContent)
        const setText = (seletor, texto) => {
            const elemento = modal.querySelector(seletor);
            if (elemento && texto !== null && texto !== undefined) {
                elemento.textContent = texto;
            }
        };

        // 2. Formatadores Auxiliares
        const formatarMoeda = (valor) => parseFloat(valor || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        const formatarData = (dataString) => {
            if (!dataString) return '--/--/----';
            const [ano, mes, dia] = dataString.split('-');
            return `${dia}/${mes}/${ano}`;
        };

        // 3. Preenchimento de Textos Simples
        setText('#detalhesCofreNome', c.nome);
        setText('#detalhesCofreDescricao', c.descricao || 'Sem descrição');
        setText('#detalhesCofreLocal', c.local);
        setText('#detalhesCofreAtual', formatarMoeda(c.valor_total));
        setText('#detalhesCofreMeta', formatarMoeda(c.valor_meta));
        setText('#detalhesCofreData', `Criado em: ${formatarData(c.data_criacao)}`);

        // 4. Lógica da Barra de Progresso e Porcentagem
        const valorAtual = parseFloat(c.valor_total) || 0;
        const valorMeta = parseFloat(c.valor_meta) || 1; // Impede divisão fatal por zero se a meta for nula
        const porcentagem = (valorAtual / valorMeta) * 100;
        
        setText('#detalhesCofrePorcentagem', `${porcentagem}%`);
        // Math.min(..., 100) impede que a barra visual "vaze" do container se o valor passar da meta
        modal.querySelector('#detalhesCofreBarra').style.width = `${Math.min(porcentagem, 100)}%`; 

        // 5. Preenchimento da Tabela de Transações (Histórico)
        const tbody = modal.querySelector('#detalhesCofreTransacoes');
        tbody.innerHTML = ''; // Limpa o histórico fantasma anterior

        if (c.transacoes && c.transacoes.length > 0) {
            c.transacoes.forEach(t => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="padding: 0.75rem; border-bottom: 1px solid var(--divisor-color); font-size: 0.85rem;">
                        ${formatarData(t.data_transacao)}
                    </td>
                    <td style="padding: 0.75rem; border-bottom: 1px solid var(--divisor-color); font-size: 0.85rem;">
                        ${t.descricao || 'Aporte'}
                    </td>
                    <td style="padding: 0.75rem; border-bottom: 1px solid var(--divisor-color); font-size: 0.85rem; text-align: right; color: var(--text-especial); font-weight: 600;">
                        + ${formatarMoeda(t.valor_total)}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            // Tratamento de UI para cofres novos vazios
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" style="text-align: center; padding: 1.5rem; color: var(--text-secondary); font-size: 0.9rem;">
                        Nenhum aporte realizado neste cofre ainda.
                    </td>
                </tr>`;
        }

        document.getElementById('modal-detalhes-cofre').classList.add('active');

    } catch (error) {
        console.error(error);
        utils.feedbackPopup('error', 'Erro interno ao exibir detalhes do cofre.');
    }

    utils.esconderLoaderTabela();
    utils.esconderLoaderBlurModal();
}