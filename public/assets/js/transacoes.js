import { abrirModal, fecharModal } from "/assets/js/modais.js";

import { apiFetch, feedbackPopup } from "/assets/js/utils.js";

// preencher o formulário com a data atual
document.getElementById('data').valueAsDate = new Date();

// Buscar dados ao carregar a página
document.addEventListener('DOMContentLoaded', async function() {
    const json = await apiFetch('/transacoes/selectjson');

    if (json) {
        if (!json.resposta.sucesso) {
            feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);
            return;
        }
        
        preencherCategorias(json.categorias);
        preencherContas(json.contas);
        preencherTransacoes(json.transacoes);
    }
});

// exibir os blocos corretos de acordo com a Categoria
document.getElementById('categoria_id').addEventListener('change', async function() {
    const optionSelecionada = this.options[this.selectedIndex];
    const labelGrupo = optionSelecionada.parentElement.getAttribute('label') || '';
    const divisor = document.querySelector('.divisor-blocos')
    const botaoConfirm = document.getElementById('btnSalvarTransacao');

    let tipo = '';
         if (labelGrupo.includes('(R)')) tipo = 'R';
    else if (labelGrupo.includes('(D)')) tipo = 'D';
    else if (labelGrupo.includes('(I)')) tipo = 'I';
    else if (labelGrupo.includes('(C)')) tipo = 'C';

    // Preenche o input invisível que vai para o Controller!
    document.getElementById('tipo_transacao').value = tipo;

    divisor.style.display = 'none';
    document.querySelectorAll('.bloco')
    .forEach(bloco => bloco.style.display = 'none');

    if (tipo === 'R') {
        mudarBtnSubmit();
    }
    else if (tipo === 'D') {
        exibirBlocoEDivisor('bloco-despesa');
        mudarBtnSubmit();
    }
    else if (tipo === 'I') {
        exibirBlocoEDivisor('bloco-investimento');
        mudarBtnSubmit();
        
        const classes = await apiFetch('/classesInvestimento/selectDados');
        if (classes && classes.resposta) {
            if (!classes.resposta.sucesso) {
                feedbackPopup(classes.resposta.msgTipo, classes.resposta.mensagem);
                return;
            }

            preencherClasses(classes.classes);
        }
    }
    else if (tipo === 'C') { 
        exibirBlocoEDivisor('bloco-cofre');
        mudarBtnSubmit();
        
        const cofres = await apiFetch('/cofres/selectDados');
        if (cofres && cofres.resposta) {
            if (!cofres.resposta.sucesso) {
                feedbackPopup(cofres.resposta.msgTipo, cofres.resposta.mensagem);
                return;
            }
            
            preencherCofres(cofres.cofres);
        } 
    }

    function exibirBlocoEDivisor(blocoId) {
        divisor.style.display = 'block';
        document.getElementById(blocoId).style.display = 'grid';
    }

    function mudarBtnSubmit() {
        if (tipo === 'R') {
            botaoConfirm.innerText = 'Salvar Receita';
            botaoConfirm.classList.remove('btn-invest-submit');
        }
        else if (tipo === 'D') {
            botaoConfirm.innerText = 'Salvar Despesa';
            botaoConfirm.classList.remove('btn-invest-submit');
        }
        else if (tipo === 'I') {
            botaoConfirm.innerText = 'Salvar Investimento';
            botaoConfirm.classList.add('btn-invest-submit');
        }
        else if (tipo === 'C') {
            botaoConfirm.innerText = 'Adicionar ao Cofre';
            botaoConfirm.classList.add('btn-invest-submit');
        } 
    }
});

// habilitar/desabilitar a quantidade de parcelas
document.getElementById('parcelado').addEventListener('change', function() {
    const inputParcelas = document.getElementById('qtd_parcelas');
    inputParcelas.disabled = !this.checked;
    if (!this.checked) inputParcelas.value = 1; // Reseta se desmarcar
});

// Salvar nova categoria
document.getElementById('novaCategoriaForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const json = await apiFetch('/categoria/salvar', 'POST', copoForm);

    if (json) {
        this.reset();
        
        fecharModal('modal-nova-categoria', null);
        feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);
        
        if (!json.resposta.sucesso) return;

        const novosDados = await apiFetch('/transacoes/selectDados');
        if (novosDados && novosDados.categorias) preencherCategorias(novosDados.categorias);
    }
});

// Salvar nova forma de pagamento
document.getElementById('novoPagamentoForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const json = await apiFetch('/contaMetodo/salvar', 'POST', copoForm);

    if (json) {
        this.reset();

        fecharModal('modal-novo-pagamento');
        feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);

        if (!json.resposta.sucesso) return;

        const novosDados = await apiFetch('/transacoes/selectDados');
        if (novosDados) preencherPagamentos(novosDados.pagamentos);
    }
});

// Salvar nova classe de investimento
document.getElementById('novaClasseForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const json = await apiFetch('/classesInvestimento/salvar', 'POST', copoForm);

    if (json) {
        this.reset();

        fecharModal('modal-nova-classe');
        feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);

        if (!json.resposta.sucesso) return;

        const classes = await apiFetch('/classesInvestimento/selectDados');
        if (classes) preencherClasses(classes.classes);
    }
});

// Salvar nova transação
document.getElementById('transacaoForm').addEventListener('submit', async function(evento) {
    evento.preventDefault();

    const copoForm = new FormData(this);
    const json = await apiFetch('/transacoes/salvar', 'POST', copoForm);

    if (json) {
        this.reset();

        feedbackPopup(json.resposta.msgTipo, json.resposta.mensagem);

        if (!json.resposta.sucesso) return;
        
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

function preencherClasses(classes) {
    const select = document.getElementById('id_classes');
    if (!select) return;

    select.innerHTML = '<option value="" disabled selected>Selecione...</option>';

    classes.forEach(cofre => {
        const option = document.createElement('option');
        option.value = cofre.id;
        option.textContent = cofre.nome;
        select.appendChild(option);
    });

    const optionNovo = document.createElement('option');
    optionNovo.value = 'new';
    optionNovo.textContent = '+ Nova Classe';
    select.appendChild(optionNovo);
}

function preencherCofres(cofres) {
    const select = document.getElementById('id_cofre');
    if (!select) return;

    select.innerHTML = '<option value="" disabled selected>Selecione...</option>';

    cofres.forEach(cofre => {
        const option = document.createElement('option');
        option.value = cofre.id;
        option.textContent = cofre.nome;
        select.appendChild(option);
    });
}