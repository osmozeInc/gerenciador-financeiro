import { abrirModal, fecharModal } from "/assets/js/modais.js";

import { apiFetch, feedbackPopup } from "/assets/js/utils.js";

// 1. Ouvinte para exibir os blocos corretos de acordo com a Categoria
document.getElementById('categoria_id').addEventListener('change', function() {
    // Descobre a qual "optgroup" a opção selecionada pertence
    const optionSelecionada = this.options[this.selectedIndex];
    const labelGrupo = optionSelecionada.parentElement.getAttribute('label') || '';

    let tipo = '';
    if (labelGrupo.includes('(R)')) tipo = 'R';
    else if (labelGrupo.includes('(D)')) tipo = 'D';
    else if (labelGrupo.includes('(I)')) tipo = 'I';
    else if (labelGrupo.includes('(C)')) tipo = 'C';

    // Preenche o input invisível que vai para o Controller!
    document.getElementById('tipo_transacao').value = tipo;

    // Reseta a tela: Esconde todos os blocos adicionais e o divisor
    document.querySelector('.divisor-blocos').style.display = 'none';
    document.getElementById('bloco-despesa').style.display = 'none';
    document.getElementById('bloco-investimento').style.display = 'none';
    document.getElementById('bloco-cofre').style.display = 'none';

    // Exibe apenas o bloco correspondente à filha
    if (tipo === 'D') {
        document.querySelector('.divisor-blocos').style.display = 'block';
        document.getElementById('bloco-despesa').style.display = 'grid'; // ou flex
    } else if (tipo === 'I') {
        document.querySelector('.divisor-blocos').style.display = 'block';
        document.getElementById('bloco-investimento').style.display = 'grid';
    } else if (tipo === 'C') {
        document.querySelector('.divisor-blocos').style.display = 'block';
        document.getElementById('bloco-cofre').style.display = 'grid';
    }
});

// 2. Ouvinte para habilitar/desabilitar a quantidade de parcelas
document.getElementById('parcelado').addEventListener('change', function() {
    const inputParcelas = document.getElementById('qtd_parcelas');
    inputParcelas.disabled = !this.checked;
    if (!this.checked) inputParcelas.value = 1; // Reseta se desmarcar
});

document.getElementById('data').valueAsDate = new Date();

// Buscar dados ao carregar a página
document.addEventListener('DOMContentLoaded', async function() {
    const dados = await apiFetch('/transacoes/selectDados');

    console.log(dados);

    if (dados) {
        preencherCategorias(dados.categorias);
        preencherContas(dados.contas); // <-- Corrigido aqui
        preencherTransacoes(dados.transacoes);
    }
});

// Salvar nova categoria
document.getElementById('novaCategoriaForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const resposta = await apiFetch('/categoria/salvar', 'POST', copoForm);

    if (resposta && resposta.sucesso) {
        this.reset();

        fecharModal('modal-nova-categoria', null);
        feedbackPopup(resposta.msgTipo, resposta.mensagem);

        const novosDados = await apiFetch('/transacoes/selectDados');

        if (novosDados) preencherCategorias(novosDados.categorias);
    }
});

// Salvar nova forma de pagamento
document.getElementById('novoPagamentoForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const resposta = await apiFetch('/pagamento/salvar', 'POST', copoForm);

    if (resposta && resposta.sucesso) {
        this.reset();

        fecharModal('modal-novo-pagamento');

        feedbackPopup(resposta.msgTipo, resposta.mensagem);
        const novosDados = await apiFetch('/transacoes/selectDados');
        if (novosDados) preencherPagamentos(novosDados.pagamentos);
    }
});

document.getElementById('transacaoForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const resposta = await apiFetch('/transacoes/salvar', 'POST', copoForm);

    if (resposta && resposta.sucesso) {
        this.reset();

        feedbackPopup(resposta.msgTipo, resposta.mensagem);

        const novosDados = await apiFetch('/transacoes/selectDados');

        if (novosDados) preencherTransacoes(novosDados.transacoes);
    }
});

function preencherCategorias(categoriasArray) {
    const mapaGrupos = {
        'R': 'Receitas (R)',
        'D': 'Despesas (D)',
        'I': 'Investimentos (I)',
        'C': 'Cofres (C)'
    };

    for (const [tipo, tipoExato] of Object.entries(mapaGrupos)) {
        // CORRIGIDO PARA #categoria_id
        const optgroup = document.querySelector(`#categoria_id optgroup[label="${tipoExato}"]`);
        
        if (optgroup) {
            optgroup.innerHTML = ''; 
            if (categoriasArray[tipo] && categoriasArray[tipo].length > 0) {
                categoriasArray[tipo].forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.nome;
                    optgroup.appendChild(option);
                });
            }
        }
    }
}

function preencherContas(contas) {
    // CORRIGIDO PARA #conta_id
    const select = document.getElementById('conta_id');
    if (!select) return;

    select.innerHTML = '<option value="" disabled selected>Selecione...</option>';

    contas.forEach(conta => {
        const option = document.createElement('option');
        option.value = conta.id;
        option.textContent = conta.nome;
        select.appendChild(option);
    });

    const optionNovo = document.createElement('option');
    optionNovo.value = 'new';
    optionNovo.textContent = '+ Nova Conta';
    select.appendChild(optionNovo);
}

function preencherTransacoes(transacoes) {
    const tabela = document.getElementById('tabelaTransacoes');
    if (!tabela) return;

    // Limpa a tabela preservando o cabeçalho (jeito mais performático)
    tabela.innerHTML = ''; 

    transacoes = transacoes.slice(0, 10);

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
}