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

// Ver as transações relacionadas a um cofre

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
        card.className = 'cofre-card';
        card.id = cofre.id;
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

