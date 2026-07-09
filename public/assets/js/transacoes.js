import { abrirModal, fecharModal } from "/assets/js/modais.js";
import { apiFetch, feedbackPopup } from "/assets/js/utils.js";

let formAtualizados = {
    'R': false,
    'D': false,
    'I': false,
    'C': false
}
let listaCompletaTransacoes = [];

// Buscar dados ao carregar a página
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const json = await apiFetch('/transacoes/selectDadosTransacoes');
        listaCompletaTransacoes = json.transacoes;

        if (!json.resposta.sucesso) {
            feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);
            return;
        }
            
        preencherTransacoes(json.transacoes, null);
    }
    catch (erro) {
        feedbackPopup('error', 'Ocorreu um erro ao buscar os dados.');
    }
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

// atualizar tabela de transações
const btnAtualizarTabela = document.getElementById('btnRecarregarTabela');
btnAtualizarTabela.addEventListener('click', async function() {
    try {
        const json = await apiFetch('/transacoes/selectDadosTransacoes');
        preencherTransacoes(json.transacoes, null);
        feedbackPopup('success', 'Tabela atualizada.');
    }
    catch (erro) {
        feedbackPopup('error', 'Ocorreu um erro ao buscar os dados.');
    }
});

// buscar na tabela de transações
document.getElementById('inputPesquisaTabela').addEventListener('input', function() {
    const termo = this.value.trim().toLowerCase();

    if (termo === '') {
        preencherTransacoes(listaCompletaTransacoes, null);
        return;
    }

    const filtrados = listaCompletaTransacoes.filter(trans => {
        const desc = (trans.descricao || '').toLowerCase();
        const cat = (trans.categoria_nome || '').toLowerCase();
        return desc.includes(termo) || cat.includes(termo);
    });

    preencherTransacoes(filtrados, null);
});

// Salvar nova categoria (atualizar funções)
document.getElementById('novaCategoriaForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    
    const copoForm = new FormData(this);
    const jsonSalvar = await apiFetch('/categorias/salvar', 'POST', copoForm);
    
    if (fecharModalExibirFeedback(jsonSalvar, 'modal-nova-categoria', this)) {
        const jsonCategorias = await apiFetch('/categorias/selectDados');
        const tipo = document.querySelector('.transacoes-container card form.visivel-block').getAttribute('data-idForm').charAt(0).toUpperCase();
        console.log(tipo);
        console.log(typeof tipo);
        if (jsonCategorias && jsonCategorias.categorias) preencherCategorias(jsonCategorias.categorias, tipo);
    }
});

// Salvar novo metodo de pagamento (atualizar funções)
document.getElementById('novoPagamentoForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const jsonSalvar = await apiFetch('/contaMetodo/salvar', 'POST', copoForm);

        if (fecharModalExibirFeedback(jsonSalvar, 'modal-novo-pagamento', this)) {
        const jsonMetodos = await apiFetch('/contaMetodo/selectDados');
        if (jsonMetodos) preencherMetodos(jsonMetodos.metodos);
    }
});

// Salvar nova classe de investimento
document.getElementById('novaClasseForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const json = await apiFetch('/classesInvestimento/salvar', 'POST', copoForm);

    if (fecharModalExibirFeedback(jsonSalvar, 'modal-nova-classe', this)) {
        const classes = await apiFetch('/classesInvestimento/selectDados');
        if (classes) preencherClasses(classes.classes, 'I');
    }
});

// Salvar nova transação de receita
document.getElementById('receitaForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const jsonSalvar = await apiFetch('/transacoes/salvarReceita', 'POST', copoForm);

    if (fecharModalExibirFeedback(jsonSalvar, null, this) /* && VERIFICAR SE O USUÁRIO QUER QUE ATUALIZE AUTOMATICAMENTE */) {
        // const novosDados = await apiFetch('/transacoes/selectTransacoes');
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

    const copoForm = new FormData(this);
    const jsonSalvar = await apiFetch('/transacoes/salvarDespesa', 'POST', copoForm);

    if (fecharModalExibirFeedback(jsonSalvar, null, this) /* && VERIFICAR SE O USUÁRIO QUER QUE ATUALIZE AUTOMATICAMENTE */) {
        // const novosDados = await apiFetch('/transacoes/selectTransacoes');
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

    const copoForm = new FormData(this);
    const jsonSalvar = await apiFetch('/transacoes/salvarInvestimento', 'POST', copoForm);

    if (fecharModalExibirFeedback(jsonSalvar, null, this) /* && VERIFICAR SE O USUÁRIO QUER QUE ATUALIZE AUTOMATICAMENTE */) {
        // const novosDados = await apiFetch('/transacoes/selectTransacoes');
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

    const copoForm = new FormData(this);
    const jsonSalvar = await apiFetch('/transacoes/salvarNoCofre', 'POST', copoForm);

    if (fecharModalExibirFeedback(jsonSalvar, null, this) /* && VERIFICAR SE O USUÁRIO QUER QUE ATUALIZE AUTOMATICAMENTE */) {
        // const novosDados = await apiFetch('/transacoes/selectTransacoes');
        // if (novosDados) preencherTransacoes(novosDados.transacoes, 'C');
    }
    else {
        // autualizarDataAtual('C');
    }
    
    atualizarDataAtual('C'); /* temporário */
});



async function receberDadosDoBotao(botao) {
    const tipo = botao.getAttribute('data-idForm').charAt(0).toUpperCase();
    atualizarDataAtual(tipo);

    if (formAtualizados[tipo]) return;

    try {
        if (tipo === 'R') {
            const json = await apiFetch('/categorias/selectDadosReceita');
            preencherCategorias(json.categorias, tipo);
        } else if (tipo === 'D') {
            const json = await apiFetch('/categorias/selectDadosDespesa');
            preencherCategorias(json.categorias, tipo);
        } else if (tipo === 'I') {
            const json = await apiFetch('/classesInvestimento/selectDados');
            preencherClasses(json.classes);
        } else if (tipo === 'C') {
            const json = await apiFetch('/cofres/selectDados');
            preencherCofres(json.cofres);
        }

        const jsonMetodos = await apiFetch('/contaMetodo/selectDados');
        preencherMetodos(jsonMetodos.metodos, tipo);
        
        formAtualizados[tipo] = true;
    }
    catch (erro) {
        console.error(erro);
        feedbackPopup('error', 'erro:' + erro);
    }
}

function fecharModalExibirFeedback(json, idModal, formulario) {
    if (!json) return false;

    feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);

    if (json.resposta.sucesso && formulario) 
        formulario.reset();

    if (json.resposta.sucesso && idModal) 
        fecharModal(idModal, formulario);

    return true;
}

function preencherCategorias(jsonCategorias, tipo) {
    const select = document.getElementById('categorias' + tipo);

    jsonCategorias.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = cat.nome;
        select.appendChild(option);
    });
    
    const optionNovo = document.createElement('option');
    optionNovo.value = 'new';
    optionNovo.textContent = '+ Nova Categoria';
    select.appendChild(optionNovo);
}

function preencherMetodos(metodos, tipo) {
    const select = document.getElementById('metodoConta' + tipo);

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

function preencherTransacoes(transacoes, tipo) {
    const tabela = document.getElementById('tabelaTransacoes');
    if (!tabela) return;

    tabela.innerHTML = ''; 

    transacoes.forEach(trans => {
        // Previne erro se a data vier nula do banco
        if(!trans.data_transacao) return; 

        const [ano, mes, dia] = trans.data_transacao.split('-');
        const dataFormatada = `${dia}/${mes}/${ano}`;

        const linha = tabela.insertRow();
        linha.insertCell().textContent = dataFormatada;
        linha.insertCell().textContent = trans.descricao;
        linha.insertCell().textContent = trans.categoria_nome;
        linha.insertCell().textContent = trans.categoria_tipo;
        linha.insertCell().textContent = trans.conta_nome || 'N/A'; // Pode ser nula no cofre
        
        // Formatação profissional de moeda para os alunos verem
        const valorFormatado = parseFloat(trans.valor_total).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        linha.insertCell().textContent = valorFormatado;
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



// let selectsFiltroCarregados = false;

// document.getElementById('btnFiltrarTabela')?.addEventListener('click', async function() {
//     // Evita fazer requisições repetidas ao banco se o usuário abrir o filtro várias vezes
//     if (selectsFiltroCarregados) return;

//     try {
//         // Busca todas as categorias e contas usando suas rotas de API
//         const jsonCat = await apiFetch('/categorias/selectDados'); // Ou sua rota equivalente
//         const jsonMet = await apiFetch('/contaMetodo/selectDados');

//         // Preenche Categoria de forma isolada
//         if (jsonCat && jsonCat.categorias) {
//             const selectCat = document.getElementById('filtroCategoria');
//             selectCat.innerHTML = '<option value="">Todas as Categorias</option>';
            
//             // Garante leitura de array plano mesmo se o PHP mandar categorias agrupadas
//             const arrayCat = Array.isArray(jsonCat.categorias) 
//                 ? jsonCat.categorias 
//                 : Object.values(jsonCat.categorias).flat();

//             arrayCat.forEach(cat => {
//                 const option = document.createElement('option');
//                 option.value = cat.id;
//                 option.textContent = cat.nome;
//                 selectCat.appendChild(option);
//             });
//         }

//         // Preenche Contas de forma isolada
//         if (jsonMet && jsonMet.metodos) {
//             const selectMet = document.getElementById('filtroConta');
//             selectMet.innerHTML = '<option value="">Todas as Contas</option>';
            
//             jsonMet.metodos.forEach(met => {
//                 const option = document.createElement('option');
//                 option.value = met.id;
//                 option.textContent = met.nome;
//                 selectMet.appendChild(option);
//             });
//         }

//         selectsFiltroCarregados = true;
//     } catch (erro) {
//         console.error("Erro ao popular modal de filtro:", erro);
//     }
// });

// // =========================================================
// // 2. APLICAR FILTRO (Em memória, super rápido)
// // =========================================================
// document.getElementById('formFiltroTransacoes')?.addEventListener('submit', function(e) {
//     e.preventDefault();

//     const tipo = document.getElementById('filtroTipo').value;
//     const catId = document.getElementById('filtroCategoria').value;
//     const contaId = document.getElementById('filtroConta').value;
//     const dataInicio = document.getElementById('filtroDataInicio').value;
//     const dataFim = document.getElementById('filtroDataFim').value;

//     // Filtra o array global que guarda as 100 transações originais
//     const filtrados = listaCompletaTransacoes.filter(trans => {
//         if (tipo && trans.categoria_tipo !== tipo) return false;
//         if (catId && String(trans.categoria_id) !== String(catId)) return false;
//         if (contaId && String(trans.conta_id) !== String(contaId)) return false;
//         if (dataInicio && trans.data_transacao < dataInicio) return false;
//         if (dataFim && trans.data_transacao > dataFim) return false;
        
//         return true;
//     });

//     // Desenha a tabela com o resultado
//     preencherTransacoes(filtrados, null);
    
//     // Fecha o modal via JS
//     const modal = document.getElementById('modal-filtro-transacoes');
//     if (modal) modal.style.display = 'none';
// });

// // =========================================================
// // 3. LIMPAR FILTROS (Botão Outline)
// // =========================================================
// document.getElementById('btnLimparFiltros')?.addEventListener('click', function() {
//     // Reseta todos os selects e inputs de data do modal
//     document.getElementById('formFiltroTransacoes').reset();
    
//     // Devolve as 100 transações originais para a tela
//     preencherTransacoes(listaCompletaTransacoes, null);
    
//     // Fecha o modal
//     const modal = document.getElementById('modal-filtro-transacoes');
//     if (modal) modal.style.display = 'none';
// });