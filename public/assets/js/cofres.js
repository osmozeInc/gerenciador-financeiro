// import { abrirModal, fecharModal } from "/assets/js/modais.js";
import * as utils from "/assets/js/utils.js";
import { abrirModal, fecharModal, abrirModalPorId } from "/assets/js/modais.js";

let cofres = []; // Variável global para armazenar os cofres
let cofresAtivos = []; // Variável global para armazenar os cofres

document.addEventListener('DOMContentLoaded', async function() {
    try {
        const [jsonCofres, jsonMetodos] = await Promise.all([
            utils.apiFetch('/cofres/selectAllCofres'),
            utils.apiFetch('/contaMetodo/selectDados')
        ]);
        
        cofres = jsonCofres.cofres;
        cofresAtivos = jsonCofres.cofres.filter(cofre => cofre.status !== 'cancelado');

        if (!jsonCofres?.resposta?.sucesso) {
            utils.feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);
            return;
        }

        if (jsonMetodos) preencherMetodos(jsonMetodos.metodos);

        visaoGeralCofres(cofresAtivos);
        cofreEmDestaque(cofresAtivos);
        listarCofres(cofresAtivos);

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
            const jsonCofres = await utils.apiFetch('/cofres/selectAllCofres');
            listarCofres(jsonCofres.cofres);
        }
    }
});

// exclui o cofre
document.getElementById('btnExcluirCofre').addEventListener('click', async function() {
    const idCofre = document.querySelector('#modal-detalhes-cofre').getAttribute('data-id');
    const jsonExcluir = await utils.apiFetch(`/cofres/excluirCofre/${idCofre}`, 'DELETE');

    if (jsonExcluir?.resposta) {
        utils.feedbackPopup(jsonExcluir.resposta.msgTipo, jsonExcluir.resposta.mensagem);
        
        if (jsonExcluir.resposta.sucesso) {
            const jsonCofres = await utils.apiFetch('/cofres/selectAllCofres');
            listarCofres(jsonCofres.cofres);

            fecharModal('modal-detalhes-cofre');
        }
    }
});

// resgata o saldo do cofre
document.getElementById('btnResgatarSaldo').addEventListener('click', async function() {
    const idCofre = document.querySelector('#modal-detalhes-cofre').getAttribute('data-id');
    const jsonResgatar = await utils.apiFetch(`/cofres/resgatarSaldo/${idCofre}`, 'POST');

    if (jsonResgatar?.resposta) {
        utils.feedbackPopup(jsonResgatar.resposta.msgTipo, jsonResgatar.resposta.mensagem);
        
        if (jsonResgatar.resposta.sucesso) {
            const jsonCofres = await utils.apiFetch('/cofres/selectAllCofres');
            listarCofres(jsonCofres.cofres);

            fecharModal('modal-detalhes-cofre');
        }
    }
});

// finaliza a meta do cofre
document.getElementById('btnFinalizarMeta').addEventListener('click', async function() {
    const idCofre = document.querySelector('#modal-detalhes-cofre').getAttribute('data-cofre-id');
    const jsonFinalizar = await utils.apiFetch(`/cofres/finalizarMeta/${idCofre}`, 'POST');

    if (jsonFinalizar?.resposta) {
        utils.feedbackPopup(jsonFinalizar.resposta.msgTipo, jsonFinalizar.resposta.mensagem);
        
        if (jsonFinalizar.resposta.sucesso) {
            const jsonCofres = await utils.apiFetch('/cofres/selectAllCofres');
            listarCofres(jsonCofres.cofres);

            fecharModal('modal-detalhes-cofre');
        }
    }
});

// Fechar modal de detalhes do cofre resetando informações
document.querySelectorAll('.js-fechar-modal').forEach(btnFechar => {
    btnFechar.addEventListener('click', () => {
        utils.exibirLoaderBlurModal();
        utils.exibirLoaderTabela();
        document.getElementById('detalhesCofreTransacoes').innerHTML = '';
    });
});

// escuta a abertura do modal de detalhes do cofre e exibe informações corretas
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

    let cdPorcentalConcluido = cofreDestaque ? ((cofreDestaque.valor_total / cofreDestaque.valor_meta) * 100) : 0;

    const badge = document.getElementById('cofreBadge');
    if (cdPorcentalConcluido >= 0 && cdPorcentalConcluido <= 60) {badge.textContent = `Falta ${(100 - cdPorcentalConcluido).toFixed(0)}%`; badge.className = 'badge-foco warning';}
    if (cdPorcentalConcluido > 60 && cdPorcentalConcluido <= 80) {badge.textContent = 'Quase lá!'; badge.className = 'badge-foco warning';}
    if (cdPorcentalConcluido > 80) {badge.textContent = 'Quase Finalizado!'; badge.className = 'badge-foco success';}

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
        if (cofre.status !== 'ativo') return;

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

                if (t.valor_total <= 0) {
                    tr.innerHTML = `
                    <td style="padding: 0.75rem; border-bottom: 1px solid var(--divisor-color); font-size: 0.85rem;">
                        ${formatarData(t.data_transacao)}
                    </td>
                    <td style="padding: 0.75rem; border-bottom: 1px solid var(--divisor-color); font-size: 0.85rem;">
                        ${t.descricao || 'Aporte'}
                    </td>
                    <td style="padding: 0.75rem; border-bottom: 1px solid var(--divisor-color); font-size: 0.85rem; text-align: right; color: var(--text-danger); font-weight: 600;">
                        ${formatarMoeda(t.valor_total).replace('-', '- ')}
                    </td>
                    `;
                }
                else {
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
                }
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

        // 6. Ajuste de Botões de Ação com base no status do cofre
        const btnExcluir = modal.querySelector('#btnExcluirCofre');
        const btnResgatar = modal.querySelector('#btnResgatarSaldo');

        if (c.valor_total > 0) {
            btnExcluir.classList.add('hidden');
            btnResgatar.classList.remove('hidden');
        } else {
            btnExcluir.classList.remove('hidden');
            btnResgatar.classList.add('hidden');
        }


    } catch (error) {
        console.error(error);
        utils.feedbackPopup('error', 'Erro interno ao exibir detalhes do cofre.');
    }

    utils.esconderLoaderTabela();
    utils.esconderLoaderBlurModal();
}

function preencherMetodos(metodos) {
    const select = document.getElementById('metodoContaResgate');

    select.innerHTML = '<option value="">Selecione...</option>';

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
