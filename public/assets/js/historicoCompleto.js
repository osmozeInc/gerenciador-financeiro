import { abrirModal, fecharModal, abrirLoaderModal, fecharLoaderModal } from "/assets/js/modais.js";
import * as utils from "/assets/js/utils.js";

let listaTransacoesCompleta;
let saldoTotal;

// Buscar dados ao carregar a página
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const [jsonTransacoes, jsonCategorias, jsonMetodos] = await Promise.all([
            utils.apiFetch('/transacoes/selectDadosTransacoes'),
            utils.apiFetch('/categorias/selectDados'),
            utils.apiFetch('/contaMetodo/selectDados')
        ])
        
        if (!jsonTransacoes.resposta.sucesso) {
            utils.feedbackPopup(jsonTransacoes.resposta.msgTipo, json.resposta.mensagem);
            return;
        }
        if (!jsonCategorias.resposta.sucesso) {
            utils.feedbackPopup(jsonCategorias.resposta.msgTipo, json.resposta.mensagem);
            return;
        }
        if (!jsonMetodos.resposta.sucesso) {
            utils.feedbackPopup(jsonMetodos.resposta.msgTipo, json.resposta.mensagem);
            return;
        }

        listaTransacoesCompleta = jsonTransacoes.transacoes;

        preencherTransacoes(jsonTransacoes.transacoes);
        preencherCategorias(jsonCategorias.categorias);
        preencherMetodos(jsonMetodos.metodos);
    }
    catch (erro) {
        utils.feedbackPopup('error', erro);
    }

    utils.esconderLoaderTabela();
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

// habilitar/desabilitar a quantidade de parcelas
const switchParcelado = document.getElementById('editParcelado')
switchParcelado.addEventListener('change', function() {
    const inputParcelas = document.getElementById('editInputParcelas');
    
    if (this.checked) {
        inputParcelas.classList.remove('hidden');
    } else {
        inputParcelas.classList.add('hidden');
    }
});

// abrir modal e exibir o form correto
document.body.addEventListener('click', (e) => {
    const btnAbrirModalTipo = e.target.closest('.js-abrir-modal-passando-tipo');
    if (btnAbrirModalTipo) {
        const value = btnAbrirModalTipo.getAttribute('value');
        const tipo = btnAbrirModalTipo.getAttribute('data-tipo');
        const idModal = btnAbrirModalTipo.getAttribute('data-target');
        
        abrirModal(idModal);
        abrirLoaderModal(idModal);
        exibirFormCorreto(tipo, value);
    }
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
    utils.feedbackPopup('info', `${filtrados.length} transações encontradas.`);
});

document.getElementById('btn-limpar-filtros')?.addEventListener('click', function() {
    document.getElementById('formFiltros').reset();
    
    preencherTransacoes(listaTransacoesCompleta);
    utils.feedbackPopup('info', 'Filtros removidos.');
});

// excluir transação
document.querySelector('#modal-excluir-transacao form').addEventListener('submit', async function(e) {
    e.preventDefault();
    fecharModal('modal-excluir-transacao');

    document.getElementById('tabelaTransacoes').innerHTML = '';
    utils.exibirLoaderTabela();

    const id = this.getAttribute('data-idTransacao');

    const jsonResposta = await utils.deletarTransacao(id);
    if (!jsonResposta.resposta.sucesso) {
        utils.feedbackPopup(jsonResposta.resposta.msgTipo, jsonResposta.resposta.mensagem);
        return;
    }
    utils.feedbackPopup(jsonResposta.resposta.msgTipo, jsonResposta.resposta.mensagem);

    const jsonTransacoes = await utils.apiFetch('/transacoes/selectDadosTransacoes');
    if (!jsonTransacoes.resposta.sucesso) {
        utils.feedbackPopup(jsonTransacoes.resposta.msgTipo, json.resposta.mensagem);
        return;
    }

    utils.esconderLoaderTabela();
    preencherTransacoes(jsonTransacoes.transacoes);
})



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
            <button value="${trans.id}" class="btn-linha edit js-abrir-modal-passando-tipo" data-tipo="${trans.categoria_tipo}" data-target="modal-editar-transacao" title="Editar"><i class="bi bi-pencil"></i></button>
            <button value="${trans.id}" class="btn-linha delete js-abrir-modal-passando-value" data-target="modal-excluir-transacao" title="Excluir"><i class="bi bi-trash3"></i></button>
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

function preencherCategoriasModal(categorias, idSelect) {
    const select = document.getElementById(idSelect);

    categorias.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.nome;
        option.textContent = cat.nome;
        option.setAttribute('data-tipo', cat.tipo);
        select.appendChild(option);
    });
}

function preencherMetodosModal(metodos, idSelect) {
    const select = document.getElementById(idSelect);
    metodos.forEach(metodo => {
        const option = document.createElement('option');
        option.value = metodo.nome;
        option.textContent = metodo.nome;
        select.appendChild(option);
    });
}

function preencherClassesModal(classes) {
    const select = document.getElementById('editClasseInvestimento');

    classes.forEach(classe => {
        const option = document.createElement('option');
        option.value = classe.id;
        option.textContent = classe.nome;
        select.appendChild(option);
    });
}

function preencherCofresModal(cofres) {
    const select = document.getElementById('editCofresCofre');

    cofres.forEach(cofre => {
        const option = document.createElement('option');
        option.value = cofre.id;
        option.textContent = cofre.nome;
        select.appendChild(option);
    });
}

async function exibirFormCorreto(tipo, idTransacao) {

    const config = {
        'R': { idForm: 'editReceitaForm',      titulo: 'Receita' },
        'D': { idForm: 'editDespesaForm',      titulo: 'Despesa' },
        'I': { idForm: 'editInvestimentoForm', titulo: 'Investimento' },
        'C': { idForm: 'editCofreForm',        titulo: 'Cofre' }
    };

    const cfg = config[tipo];
    if (!cfg) return;

    const formAtivo = document.getElementById(cfg.idForm);

    document.querySelectorAll('.form-edicao').forEach(f => {
        f.classList.add('hidden');
        f.reset();
    });

    document.getElementById('badgeTipoEdicao').textContent = cfg.titulo;
    
    try {
        const urlMetodos = '/contaMetodo/selectDados';
        let urlCategoria;
        
        if (tipo === 'R') urlCategoria = `/categorias/selectDadosReceita`;
        if (tipo === 'D') urlCategoria = `/categorias/selectDadosDespesa`;

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