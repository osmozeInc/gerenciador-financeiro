<main class="historico-container">

    <section class="card header-historico">
        <div class="titulo-historico">
            <h2>Histórico Completo</h2>
            <p>Acompanhe todas as suas entradas, saídas, investimentos e cofres.</p>
        </div>
        <div class="totalizador-historico">
            <span>Soma de todas as movimentações</span>
            <strong id="valor-total-filtro">R$ 0,00</strong>
        </div>
    </section>

    <section class="card">
        <h2>Filtros</h2>
            
        <form class="barra-filtros">
            <div class="search-group">
                <span style="position: relative; grid-column: span 2;">
                    <i class="bi bi-search"></i>
                    <input type="text" id="filtro-busca" placeholder="Descrição...">
                </span>
                
                <select id="filtroTipo">
                    <option value="">Todos os Tipos</option>
                    <option value="R">Receitas (R)</option>
                    <option value="D">Despesas (D)</option>
                    <option value="I">Investimentos (I)</option>
                    <option value="C">Cofres (C)</option>
                </select>

                <select id="filtroCategoria">
                    <option value="">Todas as Categorias</option>
                </select>             

                <select id="filtroConta">
                    <option value="">Todas as Contas</option>
                </select>
        
                <input type="date" id="filtroDataInicio">
            
                <input type="date" id="filtroDataFim">
        
                <input type="number" id="filtroValorPiso" placeholder="Valor Piso">
            
                <input type="number" id="filtroValorTeto" placeholder="Valor Teto">
            </div>

            <div class="filtro-acoes">

                <button type="button" class="btn-acao export" title="Exportar Extrato">
                    <i class="bi bi-download"></i> Exportar Tabela
                </button>

                <span>
                    <button type="button" class="btn-acao clean" id="btn-limpar-filtros" title="Limpar Filtros">
                        <i class="bi bi-eraser"></i> Limpar Filtros
                    </button>
                    <button type="button" class="btn-acao filter" title="Exportar Extrato">
                        <i class="bi bi-funnel"></i> Aplicar Filtros
                    </button>
                </span>
                
            </div>
        </form>
    </section>

    <!-- Tabela Infinita -->
    <section class="card tabela-wrapper">
        <div class="tabela-scroll">
            <table class="tabela-historico">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Categoria / Ativo</th>
                        <th>Conta</th>
                        <th class="col-valor">Valor</th>
                        <th class="col-acoes">Ações</th>
                    </tr>
                </thead>
                <tbody id="tbody-historico">
                    <!-- Linhas geradas via JavaScript -->
                </tbody>
            </table>
        </div>
        <div class="badge-info" id="badge-resultados">
            <p>Exibindo <span id="qtd-resultados">0</span> movimentações.</p>
        </div>
    </section>
</main>

<script>
    // Mock de dados para simular o banco (unificando todos os tipos)
    const historicoDados = [
        { id: 1, data: '2026-07-14', desc: 'Salário Mensal', catAtivo: 'Renda Principal', conta: 'Nubank', valor: 8500.00, tipo: 'R' },
        { id: 2, data: '2026-07-12', desc: 'Conta de Luz', catAtivo: 'Moradia', conta: 'Inter', valor: 250.40, tipo: 'D' },
        { id: 3, data: '2026-07-10', desc: 'Compra MXRF11', catAtivo: 'FIIs', conta: 'Rico Corretora', valor: 1050.00, tipo: 'I' },
        { id: 4, data: '2026-07-08', desc: 'Reserva Viagem', catAtivo: 'Cofre Férias', conta: 'Caixinha Nubank', valor: 500.00, tipo: 'C' },
        { id: 5, data: '2026-07-05', desc: 'Supermercado', catAtivo: 'Alimentação', conta: 'Cartão Inter', valor: 650.00, tipo: 'D' },
        { id: 6, data: '2026-06-28', desc: 'Freela Design', catAtivo: 'Renda Extra', conta: 'Nubank', valor: 1200.00, tipo: 'R' }
    ];

    document.addEventListener('DOMContentLoaded', () => {
        const tbody = document.getElementById('tbody-historico');
        const valorTotalFiltro = document.getElementById('valor-total-filtro');
        const boxTotalizador = valorTotalFiltro.parentElement;
        const qtdResultados = document.getElementById('qtd-resultados');

        // Elementos de Filtro
        const inputBusca = document.getElementById('filtro-busca');
        const selectMes = document.getElementById('filtro-mes');
        const selectTipo = document.getElementById('filtro-tipo');
        const btnLimpar = document.getElementById('btn-limpar-filtros');

        // Função de formatação
        const formatarData = (dataIso) => dataIso.split('-').reverse().join('/');
        const formatarMoeda = (valor) => valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

        // Função principal de renderização e cálculo
        function renderizarTabela() {
            const termo = inputBusca.value.toLowerCase();
            const mes = selectMes.value;
            const tipo = selectTipo.value;

            tbody.innerHTML = '';
            let saldoTotal = 0;
            let linhasRenderizadas = 0;

            historicoDados.forEach(item => {
                // Lógica de Filtros
                const matchBusca = item.desc.toLowerCase().includes(termo) || item.catAtivo.toLowerCase().includes(termo);
                const matchTipo = tipo === 'todos' || item.tipo === tipo;
                
                // Pega MM-YYYY da data do item (YYYY-MM-DD)
                const itemMesAno = `${item.data.substring(5, 7)}-${item.data.substring(0, 4)}`;
                const matchMes = mes === 'todos' || itemMesAno === mes;

                if (matchBusca && matchTipo && matchMes) {
                    linhasRenderizadas++;

                    // Cálculo do Saldo (Receitas somam, Despesas subtraem, Investimentos/Cofres consideramos como saída do caixa livre)
                    if (item.tipo === 'R') {
                        saldoTotal += item.valor;
                    } else {
                        saldoTotal -= item.valor;
                    }

                    // Criação da linha no DOM
                    const tr = document.createElement('tr');
                    
                    // Define sinal e cor baseada no tipo
                    let sinal = item.tipo === 'R' ? '+' : '-';
                    
                    tr.innerHTML = `
                        <td>${formatarData(item.data)}</td>
                        <td><strong>${item.desc}</strong></td>
                        <td>${item.catAtivo}</td>
                        <td>${item.conta}</td>
                        <td class="col-valor tipo-${item.tipo}">${sinal} ${formatarMoeda(item.valor)}</td>
                        <td class="col-acoes">
                            <button class="btn-linha" title="Editar"><i class="bi bi-pencil"></i></button>
                            <button class="btn-linha delete" title="Excluir"><i class="bi bi-trash3"></i></button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                }
            });

            // Atualizações de UI Pós-Renderização
            qtdResultados.textContent = linhasRenderizadas;
            valorTotalFiltro.textContent = formatarMoeda(saldoTotal);

            // Ajusta cor do totalizador
            boxTotalizador.classList.remove('positivo', 'negativo');
            if (saldoTotal > 0) boxTotalizador.classList.add('positivo');
            else if (saldoTotal < 0) boxTotalizador.classList.add('negativo');
        }

        // Listeners dos Filtros
        inputBusca.addEventListener('input', renderizarTabela);
        selectMes.addEventListener('change', renderizarTabela);
        selectTipo.addEventListener('change', renderizarTabela);

        // Botão de Limpar
        btnLimpar.addEventListener('click', () => {
            inputBusca.value = '';
            selectMes.value = 'todos';
            selectTipo.value = 'todos';
            renderizarTabela();
        });

        // Chamada inicial
        renderizarTabela();
    });
</script>