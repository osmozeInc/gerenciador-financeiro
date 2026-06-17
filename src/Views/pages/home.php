
<main class="dashboard-container">
    <section class="kpi-grid">
        <div class="kpi-card">
            <span class="kpi-title">Saldo Total</span>
            <span class="kpi-value" id="kpiSaldo">R$ 0,00</span>
            <span class="kpi-trend trend-up">+8.4% vs mês anterior</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-title">Receitas (Mês)</span>
            <span class="kpi-value" id="kpiReceitas" style="color: var(--text-especial);">R$ 0,00</span>
            <span class="kpi-trend trend-up">+2.1%</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-title">Despesas (Mês)</span>
            <span class="kpi-value" id="kpiDespesas">R$ 0,00</span>
            <span class="kpi-trend trend-down">-5.3%</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-title">Faturas Abertas</span>
            <span class="kpi-value" id="kpiCartoes" style="color: var(--text-danger);">R$ 0,00</span>
            <span class="kpi-trend trend-down">2 Vencimentos Próximos</span>
        </div>
    </section>

    <div class="main-grid">
        
        <div class="col-main">
            
            <div class="card-section">
                <div class="section-header">
                    <h2>Fluxo de Caixa (Últimos 6 Meses)</h2>
                </div>

                <div class="chart-container" id="chartFluxo">
                    <!-- Gerado via JS -->
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem; font-size: 0.8rem; justify-content: center;">
                    <span style="display: flex; align-items: center; gap: 4px;"><div style="width: 10px; height: 10px; background: var(--text-especial); border-radius: 2px;"></div> Receitas</span>
                    <span style="display: flex; align-items: center; gap: 4px;"><div style="width: 10px; height: 10px; background: var(--text-danger); border-radius: 2px;"></div> Despesas</span>
                </div>
            </div>

            <div class="card-section">
                <div class="section-header">
                    <h2>Transações Recentes</h2>
                    <a href="#" class="link-view-all">Ver todas</a>
                </div>
                <div id="listaTransacoes">
                    <!-- Gerado via JS -->
                </div>
            </div>
        </div>

        <div class="col-side">
            
            <div class="card-section">
                <div class="section-header">
                    <h2>Minhas Contas</h2>
                </div>
                <div id="listaContas">
                    <!-- Gerado via JS -->
                </div>
            </div>

            <div class="card-section">
                <div class="section-header">
                    <h2>Orçamento do Mês</h2>
                </div>
                <div id="listaOrcamentos">
                    <!-- Gerado via JS -->
                </div>
            </div>

        </div>

    </div>
</main>

<script>
    // ESTADO GLOBAL (Mockado para simular o backend)
    const appState = {
        kpis: { saldo: 34580.00, receitas: 14100.00, despesas: 7200.00, cartoes: 2607.15 },
        grafico: [
            { mes: 'Jul', rec: 60, des: 40 }, { mes: 'Ago', rec: 70, des: 55 },
            { mes: 'Set', rec: 85, des: 60 }, { mes: 'Out', rec: 65, des: 80 },
            { mes: 'Nov', rec: 90, des: 50 }, { mes: 'Dez', rec: 100, des: 45 } // % relativa para altura CSS
        ],
        transacoes: [
            { id: 1, desc: 'Agência (Cliente X)', cat: 'Receita', data: 'Hoje', valor: 8500.00, tipo: 'R' },
            { id: 2, desc: 'Aluguel Escritório', cat: 'Moradia', data: 'Ontem', valor: 2200.00, tipo: 'D' },
            { id: 3, desc: 'Supermercado', cat: 'Alimentação', data: '08 Dez', valor: 487.50, tipo: 'D' }
        ],
        contas: [
            { id: 1, nome: 'Conta Nubank', tipo: 'Corrente', saldo: 14945.61 },
            { id: 2, nome: 'Conta Inter', tipo: 'PJ', saldo: 19634.39 }
        ],
        orcamentos: [
            { cat: 'Moradia', gasto: 2200, limite: 3000 },
            { cat: 'Alimentação', gasto: 1640, limite: 2000 },
            { cat: 'Software/SaaS', gasto: 890, limite: 800 } // Estourado
        ]
    };

    // FUNÇÕES DE RENDERIZAÇÃO (Padrão Componentizado)
    
    function formatarBRL(valor) {
        return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function renderKPIs() {
        document.getElementById('kpiSaldo').textContent = formatarBRL(appState.kpis.saldo);
        document.getElementById('kpiReceitas').textContent = formatarBRL(appState.kpis.receitas);
        document.getElementById('kpiDespesas').textContent = formatarBRL(appState.kpis.despesas);
        document.getElementById('kpiCartoes').textContent = formatarBRL(appState.kpis.cartoes);
    }

    function renderGrafico() {
        const chartDiv = document.getElementById('chartFluxo');
        chartDiv.innerHTML = appState.grafico.map(item => `
            <div class="bar-group">
                <div class="bar receita" style="height: ${item.rec}%;"></div>
                <div class="bar despesa" style="height: ${item.des}%;"></div>
                <span class="bar-label">${item.mes}</span>
            </div>
        `).join('');
    }

    function renderTransacoes() {
        const listDiv = document.getElementById('listaTransacoes');
        listDiv.innerHTML = appState.transacoes.map(t => `
            <div class="list-item">
                <div class="item-info">
                    <div class="item-icon" style="color: ${t.tipo === 'R' ? 'var(--text-especial)' : 'var(--text-danger)'}">
                        ${t.tipo === 'R' ? '↓' : '↑'}
                    </div>
                    <div class="item-details">
                        <h4>${t.desc}</h4>
                        <p>${t.cat} • ${t.data}</p>
                    </div>
                </div>
                <div class="item-value ${t.tipo === 'R' ? 'positivo' : ''}">
                    ${t.tipo === 'R' ? '+' : '-'} ${formatarBRL(t.valor)}
                </div>
            </div>
        `).join('');
    }

    function renderContas() {
        const listDiv = document.getElementById('listaContas');
        listDiv.innerHTML = appState.contas.map(c => `
            <div class="list-item">
                <div class="item-info">
                    <div class="item-details">
                        <h4>${c.nome}</h4>
                        <p>${c.tipo}</p>
                    </div>
                </div>
                <div class="item-value positivo">${formatarBRL(c.saldo)}</div>
            </div>
        `).join('');
    }

    function renderOrcamentos() {
        const listDiv = document.getElementById('listaOrcamentos');
        listDiv.innerHTML = appState.orcamentos.map(o => {
            const pct = (o.gasto / o.limite) * 100;
            let cor = 'var(--text-primary)';
            if (pct > 80 && pct <= 100) cor = '#f59e0b'; // Amarelo alerta
            if (pct > 100) cor = 'var(--text-danger)'; // Vermelho estourado

            return `
            <div class="budget-row">
                <div class="budget-info">
                    <span>${o.cat}</span>
                    <span>${formatarBRL(o.gasto)} / ${formatarBRL(o.limite)}</span>
                </div>
                <div class="progress-bg">
                    <div class="progress-fill" style="width: ${pct > 100 ? 100 : pct}%; background-color: ${cor};"></div>
                </div>
            </div>
        `}).join('');
    }

    // INIT
    function inicializarDashboard() {
        renderKPIs();
        renderGrafico();
        renderTransacoes();
        renderContas();
        renderOrcamentos();
    }

    document.addEventListener('DOMContentLoaded', inicializarDashboard);

</script>