import { abrirModal, fecharModal } from "/assets/js/modais.js";
import * as utils from "/assets/js/utils.js";

let formAtualizados = {
    'R': false,
    'D': false,
    'I': false,
    'C': false,
    'Modal': false
}
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
            
        preencherTransacoes(json.transacoes, null);
    }
    catch (erro) {
        utils.feedbackPopup('error', 'Ocorreu um erro ao buscar os dados.');
        utils.feedbackPopup('error', erro);
    }

    utils.esconderLoaderTabela();
});

// exibir os blocos corretos de acordo com o botão clicado
const btnsTipoTransacao = document.querySelectorAll('.btn-tipo-transacao')
btnsTipoTransacao.forEach(btn => btn.addEventListener('click', async function() {

    // Se o botão já estiver ativo, desativa e reseta o formulário
    if (this.classList.contains('active')) {
        document.getElementById('aviso').classList.remove('hidden');

        const idForm = this.getAttribute('data-idForm');
        document.getElementById(idForm).classList.remove('visivel-block');
        document.getElementById(idForm).reset();

        this.classList.remove('active');
        btnsTipoTransacao.forEach(b => b.classList.remove('deactive'));

        return;
    };

    // Se o botão nao estiver ativo:
    btnsTipoTransacao.forEach(b => b.classList.remove('active'));
    btnsTipoTransacao.forEach(b => b.classList.add('deactive'));

    document.querySelectorAll('form.visivel-block').forEach(form => form.classList.remove('visivel-block'));
    
    this.classList.add('active');
    this.classList.remove('deactive');
    
    document.getElementById('aviso').classList.add('hidden');
    
    const idForm = this.getAttribute('data-idForm');
    document.getElementById(idForm).classList.add('visivel-block');

    await receberDadosDoBotao(this);
}));

// habilitar/desabilitar a quantidade de parcelas
const switchParcelado = document.getElementById('parcelado')
switchParcelado.addEventListener('change', function() {
    const inputParcelas = document.getElementById('input-parcelas');
    
    if (this.checked) {
        inputParcelas.classList.remove('hidden');
    } else {
        inputParcelas.classList.add('hidden');
    }
});

// recarregar tabela de transações
const btnAtualizarTabela = document.getElementById('btnRecarregarTabela');
btnAtualizarTabela.addEventListener('click', async function() {
    document.getElementById('tabelaTransacoes').innerHTML = '';
    utils.exibirLoaderTabela();

    try {
        const json = await utils.apiFetch('/transacoes/selectDados100Transacoes');
        preencherTransacoes(json.transacoes, null);
        utils.feedbackPopup('success', 'Tabela atualizada.');
    }
    catch (erro) {
        utils.feedbackPopup('error', 'Ocorreu um erro ao buscar os dados.');
    }

    utils.esconderLoaderTabela();
});

// buscar na tabela de transações
document.getElementById('inputPesquisaTabela').addEventListener('input', function() {
    const termo = this.value.trim().toLowerCase();

    if (termo === '') {
        preencherTransacoes(listaTransacoes, null);
        return;
    }

    const filtrados = listaTransacoes.filter(trans => {
        const desc = (trans.descricao || '').toLowerCase();
        const cat = (trans.categoria_nome || '').toLowerCase();
        return desc.includes(termo) || cat.includes(termo);
    });

    preencherTransacoes(filtrados, null);
});

// Salvar nova categoria
document.getElementById('novaCategoriaForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const corpoForm = new FormData(this);
    const jsonSalvar = await utils.apiFetch('/categorias/salvar', 'POST', corpoForm);
    
    if (fecharModalExibirFeedback(jsonSalvar, 'modal-nova-categoria', this)) {
        const tipo = document.getElementById('modal-nova-categoria').getAttribute('data-tipo');
        
        let jsonCategorias;
        console.log (tipo);
        if (tipo === 'R') jsonCategorias = await utils.apiFetch('/categorias/selectDadosReceita');
        if (tipo === 'D') jsonCategorias = await utils.apiFetch('/categorias/selectDadosDespesa');
        if (jsonCategorias && jsonCategorias.categorias) preencherCategorias(jsonCategorias.categorias, tipo);
    }
    
    formAtualizados['R'] = false;
    formAtualizados['D'] = false;
});

// Salvar novo metodo de pagamento
document.getElementById('novoPagamentoModalForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const corpoForm = new FormData(this);
    const jsonSalvar = await utils.apiFetch('/contaMetodo/salvar', 'POST', corpoForm);

    if (fecharModalExibirFeedback(jsonSalvar, 'modal-novo-pagamento', this)) {
        const tipo = document.getElementById('modal-novo-pagamento').getAttribute('data-tipo');

        const jsonMetodos = await utils.apiFetch('/contaMetodo/selectDados');
        if (jsonMetodos) preencherMetodos(jsonMetodos.metodos, tipo);
    }

    formAtualizados['R'] = false;
    formAtualizados['D'] = false;
    formAtualizados['I'] = false;
    formAtualizados['C'] = false;
});

// Salvar nova classe de investimento
document.getElementById('novaClasseModalForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const corpoForm = new FormData(this);
    const json = await utils.apiFetch('/classesInvestimento/salvar', 'POST', corpoForm);

    if (fecharModalExibirFeedback(json, 'modal-nova-classe', this)) {
        const tipo = document.getElementById('modal-novo-pagamento').getAttribute('data-tipo');

        const classes = await utils.apiFetch('/classesInvestimento/selectDados');
        if (classes) preencherClasses(classes.classes, tipo);

        formAtualizados[tipo] = false;
    }
});

// Salvar nova transação de receita
document.getElementById('receitaForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const corpoForm = new FormData(this);
    const jsonSalvar = await utils.apiFetch('/transacoes/salvarReceita', 'POST', corpoForm);

    if (fecharModalExibirFeedback(jsonSalvar, null, this) /* && VERIFICAR SE O USUÁRIO QUER QUE ATUALIZE AUTOMATICAMENTE */) {
        // const novosDados = await utils.apiFetch('/transacoes/selectTransacoes');
        // if (novosDados) preencherTransacoes(novosDados.transacoes, 'R');
    }
    else {
        // autualizarDataAtual('R');
    }

    atualizarDataAtual('R'); /* temporário */
});

// Salvar nova transação de despesa
document.getElementById('despesaForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const corpoForm = new FormData(this);
    const jsonSalvar = await utils.apiFetch('/transacoes/salvarDespesa', 'POST', corpoForm);

    if (fecharModalExibirFeedback(jsonSalvar, null, this) /* && VERIFICAR SE O USUÁRIO QUER QUE ATUALIZE AUTOMATICAMENTE */) {
        // const novosDados = await utils.apiFetch('/transacoes/selectTransacoes');
        // if (novosDados) preencherTransacoes(novosDados.transacoes, 'D');
    }
    else {
        // autualizarDataAtual('D');
    }
    
    atualizarDataAtual('D'); /* temporário */
});

// Salvar nova transação de investimento
document.getElementById('investimentoForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const corpoForm = new FormData(this);
    const jsonSalvar = await utils.apiFetch('/transacoes/salvarInvestimento', 'POST', corpoForm);

    if (fecharModalExibirFeedback(jsonSalvar, null, this) /* && VERIFICAR SE O USUÁRIO QUER QUE ATUALIZE AUTOMATICAMENTE */) {
        // const novosDados = await utils.apiFetch('/transacoes/selectTransacoes');
        // if (novosDados) preencherTransacoes(novosDados.transacoes, 'I');
    }
    else {
        // autualizarDataAtual('I');
    }
    
    atualizarDataAtual('I'); /* temporário */
});

// Salvar nova transação para o cofre
document.getElementById('cofreForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const corpoForm = new FormData(this);
    const jsonSalvar = await utils.apiFetch('/transacoes/salvarNoCofre', 'POST', corpoForm);

    if (fecharModalExibirFeedback(jsonSalvar, null, this) /* && VERIFICAR SE O USUÁRIO QUER QUE ATUALIZE AUTOMATICAMENTE */) {
        // const novosDados = await utils.apiFetch('/transacoes/selectTransacoes');
        // if (novosDados) preencherTransacoes(novosDados.transacoes, 'C');
    }
    else {
        // autualizarDataAtual('C');
    }
    
    atualizarDataAtual('C'); /* temporário */
});

// abre modal de filtro
document.getElementById('btnFiltrarTabela').addEventListener('click', async function() {
    if (formAtualizados['Modal']) return;

    try {
        const [jsonCat, jsonMet] = await Promise.all([
            utils.apiFetch('/categorias/selectDados'),
            utils.apiFetch('/contaMetodo/selectDados')
        ]);

        if(jsonCat && jsonCat.resposta.sucesso) {

            const selectCat = document.getElementById('filtroCategoriaModal');
            selectCat.innerHTML = '<option value="">Todas as Categorias</option>';

            const arrayJson = [jsonCat.categorias['R'], jsonCat.categorias['D'], jsonCat.categorias['I'], jsonCat.categorias['C']];

            arrayJson.forEach(arrayCat => {
                arrayCat.forEach(objCat => {
                    const option = document.createElement('option');
                    option.value = objCat.nome;
                    option.textContent = objCat.nome;
                    option.setAttribute('data-tipo', objCat.tipo);
                    selectCat.appendChild(option);
                })
            });
        }

        if (jsonMet && jsonMet.resposta.sucesso) {
            const selectMet = document.getElementById('filtroContaModal');
            selectMet.innerHTML = '<option value="">Todas as Contas</option>';
            
            jsonMet.metodos.forEach(met => {
                const option = document.createElement('option');
                option.value = met.nome;
                option.textContent = met.nome;
                selectMet.appendChild(option);
            });
        }

        formAtualizados['Modal'] = true;
    } catch (erro) {
        console.error("Erro ao popular modal de filtro:", erro);
    }
});

// aplica filtros
document.getElementById('formFiltroTransacoes').addEventListener('submit', function(e) {
    e.preventDefault();

    const tipo = document.getElementById('filtroTipoModal').value;
    const catId = document.getElementById('filtroCategoriaModal').value;
    const contaId = document.getElementById('filtroContaModal').value;
    const dataInicio = document.getElementById('filtroDataInicioModal').value;
    const dataFim = document.getElementById('filtroDataFimModal').value;

    const filtrados = listaTransacoes.filter(trans => {
        if (tipo && trans.categoria_tipo !== tipo) return false;
        if (catId && String(trans.categoria_nome) !== String(catId)) return false;
        if (contaId && String(trans.conta_nome) !== String(contaId)) return false;
        if (dataInicio && trans.data_transacao < dataInicio) return false;
        if (dataFim && trans.data_transacao > dataFim) return false;
        
        return true;
    });

    preencherTransacoes(filtrados, null);

    const jsonArtificial = { resposta: { sucesso: true, msgTipo: 'info', mensagem: 'Filtros aplicados.' } };
    fecharModalExibirFeedback(jsonArtificial, 'modal-filtro-transacoes', null);
});

// limpa filtros
document.getElementById('btnLimparFiltrosModal').addEventListener('click', function() {
    document.getElementById('formFiltroTransacoes').reset();
    
    preencherTransacoes(listaTransacoes, null);
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

// altera a categoria de acordo com o tipo
const selectTipo = document.getElementById('filtroTipoModal');
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
const selectCat = document.getElementById('filtroCategoriaModal');
selectCat.addEventListener('change', function() {
    const opcaoSelecionada = this.options[this.selectedIndex];
    const tipoDaCategoria = opcaoSelecionada.getAttribute('data-tipo');
    
    if (tipoDaCategoria) selectTipo.value = tipoDaCategoria;
});


async function receberDadosDoBotao(botao) {
    const tipo = botao.getAttribute('data-idForm').charAt(0).toUpperCase();
    atualizarDataAtual(tipo);

    if (formAtualizados[tipo]) return;

    try {
        // 1. Mapeamos qual é o endpoint do primeiro select de acordo com a aba
        const rotasEspecificas = {
            'R': '/categorias/selectDadosReceita',
            'D': '/categorias/selectDadosDespesa',
            'I': '/classesInvestimento/selectDados',
            'C': '/cofres/selectDados'
        };

        const rotaAlvo = rotasEspecificas[tipo];

        // 2. DISPARO EM PARALELO: Busca o select específico E as contas em tempo simultâneo!
        const [jsonEspecifico, jsonMetodos] = await Promise.all([
            rotaAlvo ? utils.apiFetch(rotaAlvo) : Promise.resolve(null),
            utils.apiFetch('/contaMetodo/selectDados')
        ]);

        // 3. Preenchemos os selects com os dados que chegaram juntos
        if (jsonEspecifico) {
            if (tipo === 'R' || tipo === 'D') preencherCategorias(jsonEspecifico.categorias, tipo);
            else if (tipo === 'I') preencherClasses(jsonEspecifico.classes);
            else if (tipo === 'C') preencherCofres(jsonEspecifico.cofres);
        }

        if (jsonMetodos) {
            preencherMetodos(jsonMetodos.metodos, tipo);
        }
        
        formAtualizados[tipo] = true;
    }
    catch (erro) {
        console.error(erro);
        utils.feedbackPopup('error', 'erro:' + erro);
    }
}

function fecharModalExibirFeedback(json, idModal, formulario) {
    if (!json) return false;
    
    utils.feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);

    if (json.resposta.sucesso) {   
        
        if (formulario)
            formulario.reset();
        
        if (json.resposta.sucesso && idModal) 
            fecharModal(idModal, null);
        
        return true;
    }

    return false;
}

function preencherCategorias(jsonCategorias, tipo) {
    const select = document.getElementById('categorias' + tipo);

    select.innerHTML = '<option value="">Selecione...</option>';

    jsonCategorias.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = cat.nome;
        select.appendChild(option);
    });
    
    const optionNovo = document.createElement('option');
    optionNovo.value = 'new';
    optionNovo.textContent = '+ Nova Categoria';
    optionNovo.setAttribute('data-tipo', tipo);
    select.appendChild(optionNovo);
}

function preencherMetodos(metodos, tipo) {
    const select = document.getElementById('metodoConta' + tipo);

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
    optionNovo.setAttribute('data-tipo', tipo);
    select.appendChild(optionNovo);
}

function preencherTransacoes(transacoes, tipo) {
    const tabela = document.getElementById('tabelaTransacoes');
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
            <button value="${trans.id}" class="btn-linha edit js-abrir-modal-passando-tipo" data-tipo="${trans.categoria_tipo}" data-target="modal-editar-transacao" title="Editar"><i class="bi bi-pencil"></i></button>
            <button value="${trans.id}" class="btn-linha delete js-abrir-modal-passando-value" data-target="modal-excluir-transacao" title="Excluir"><i class="bi bi-trash3"></i></button>
        `;
    });

    atualizarDataAtual(tipo);
}

function preencherClasses(classes) {
    const select = document.getElementById('classeInvestimento');

    classes.forEach(classe => {
        const option = document.createElement('option');
        option.value = classe.id;
        option.textContent = classe.nome;
        select.appendChild(option);
    });

    const optionNovo = document.createElement('option');
    optionNovo.value = 'new';
    optionNovo.textContent = '+ Nova Classe';
    optionNovo.setAttribute('data-tipo', 'I');
    select.appendChild(optionNovo);
}

function preencherCofres(cofres) {
    const select = document.getElementById('cofresCofre');

    cofres.forEach(cofre => {
        const option = document.createElement('option');
        option.value = cofre.id;
        option.textContent = cofre.nome;
        select.appendChild(option);
    });
}

function atualizarDataAtual(tipo) {
    if (!tipo) return;
    const input = document.getElementById('data' + tipo);
    input.valueAsDate = new Date();
}