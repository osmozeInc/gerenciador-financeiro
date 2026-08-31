// import { abrirModal, fecharModal } from "/assets/js/modais.js";
import * as utils from "/assets/js/utils.js";
import { abrirModal, abrirModalPorId } from "/assets/js/modais.js";

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

const gridCofres = document.getElementById('gridCofres');

if (gridCofres) {
    gridCofres.addEventListener('click', (e) => {
        const btnAbrirModalId = e.target.closest('.js-abrir-modal-passando-cofre');
        
        if (btnAbrirModalId) {
            const id = btnAbrirModalId.id;
            const idModal = btnAbrirModalId.getAttribute('data-target');
            
            abrirModalPorId(idModal, id);
            exibirCofreCorreto(id); 
        }
    });
}

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
                <div class="progress-text">${((cofre.valor_total / cofre.valor_meta) * 100).toFixed(1)}% Alcançado</div>
            </div>
        `;
        gridCofres.appendChild(card);
    });
}

// Implementação da função que abre e popula o modal
async function exibirCofreCorreto(idCofre) {    
    try {
        const urlMetodos = '/cofres/selectCofre/' + idCofre;
        let urlCategoria;
        
        const [jsonTransacao, jsonMetodos, jsonCategoria] = await Promise.all([
            utils.buscarTransacao(idTransacao),
            utils.apiFetch(urlMetodos),
            urlCategoria ? utils.apiFetch(urlCategoria) : Promise.resolve(null)
        ]);
        
        
        if (!jsonTransacao?.resposta?.sucesso) {
            utils.feedbackPopup('error', 'Erro ao carregar dados para edição.');
            return;
        }
        if (!jsonMetodos?.resposta?.sucesso) {
            utils.feedbackPopup('error', 'Erro ao carregar formas de pagamento.');
            return;
        }
        if (urlCategoria && !jsonCategoria?.resposta?.sucesso) {
            utils.feedbackPopup('error', 'Erro ao carregar categorias.');
            return;
        }

        if (urlCategoria) {
            preencherCategoriasModal(jsonCategoria.categorias, `editCategorias${tipo}`);
        }
        preencherMetodosModal(jsonMetodos.metodos, `editMetodoConta${tipo}`);

        const t = jsonTransacao.transacao;

        const setVal = (seletor, valor) => {
            const input = formAtivo.querySelector(seletor);
            if (input && valor !== null && valor !== undefined) input.value = valor;
        };

        setVal('[name="id_transacao"]', t.id ?? t.id_transacao);
        setVal('[name="descricao"]',    t.descricao);
        setVal('[name="data"]',         t.data_transacao);
        setVal('[name="valor"]',        t.valor_total ?? t.valor);
        setVal('[name="conta_id"]',     t.metodo_nome ?? t.metodo_id);
        setVal('[name="categoria_id"]', t.categoria_nome ?? t.categoria_id);

        if (tipo === 'D') {
            const checkParcelado = formAtivo.querySelector('[name="parcelado"]');
            const divParcelas = document.getElementById('editInputParcelas');
            
            checkParcelado.checked = t.parcelado === 1 || t.parcelado === true || t.qtd_parcelas > 1;
            divParcelas.classList.toggle('hidden', !checkParcelado.checked);
            
            if (checkParcelado.checked) setVal('[name="qtd_parcelas"]', t.qtd_parcelas);
        } 
        else if (tipo === 'I') {
            const jsonClasses = await utils.apiFetch('/classesInvestimento/selectDados');
            if (!jsonClasses || !jsonClasses.resposta.sucesso) {
                utils.feedbackPopup('error', 'Erro ao carregar classes de investimento.');
                return;
            }
            preencherClassesModal(jsonClasses.classes);

            setVal('[name="ativo"]',      t.ativo);
            setVal('[name="classe"]',     t.classe);
            setVal('[name="quantidade"]', t.quantidade);
            setVal('[name="preco"]',      t.preco_unitario); // Corrigido: no seu SQL é preco_unitario
        } 
        else if (tipo === 'C') {
            const jsonCofres = await utils.apiFetch('/cofres/selectDados');
            if (!jsonCofres || !jsonCofres.resposta.sucesso) {
                utils.feedbackPopup('error', 'Erro ao carregar cofres.');
                return;
            }
            preencherCofresModal(jsonCofres.cofres);

            setVal('[name="id_cofre"]',   t.id_cofre);
        }
    } catch (error) {
        console.error(error);
        utils.feedbackPopup('error', 'Erro ao carregar dados para edição.');
    }

    abrirModal('modal-editar-transacao');
    fecharLoaderModal('modal-editar-transacao')

    formAtivo.classList.remove('hidden');
}