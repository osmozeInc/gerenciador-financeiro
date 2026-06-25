import { abrirModal, fecharModal } from "/assets/js/modais.js";
import { apiFetch } from "/assets/js/utils.js";

document.getElementById('data_transacao').valueAsDate = new Date();

// Buscar dados ao carregar a página
document.addEventListener('DOMContentLoaded', async function() {
    const dados = await apiFetch('/transacoes/selectDados');

    if (dados) {
        preencherCategorias(dados.categorias);
        preencherPagamentos(dados.pagamentos);
        //preencherTransacoes(dados);
    }
});

// Salvar nova categoria
document.getElementById('novaCategoriaForm').addEventListener('submit', async function(evento) {
    evento.preventDefault(); 

    const copoForm = new FormData(this);
    const resposta = await apiFetch('/categoria/salvar', 'POST', copoForm);

    if (resposta && resposta.sucesso) {
        fecharModal('modalNovaCategoria');
        this.reset();

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
        fecharModal('modal-novo-pagamento');
        this.reset();

        const novosDados = await apiFetch('/transacoes/selectDados');
        if (novosDados) preencherPagamentos(novosDados.pagamentos);
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
        const optgroup = document.querySelector(`#categoria optgroup[label="${tipoExato}"]`);
        
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

function preencherPagamentos(pagamentos) {
    const select = document.getElementById('pagamento');
    if (!select) return;

    select.innerHTML = '<option value="" disabled selected>Selecione...</option>';

    pagamentos.forEach(pag => {
        const option = document.createElement('option');
        option.value = pag.id;
        option.textContent = pag.nome;
        select.appendChild(option);
    });

    const optionNovo = document.createElement('option');
    optionNovo.value = 'new';
    optionNovo.textContent = '+ Novo Pagamento';
    select.appendChild(optionNovo);
}
