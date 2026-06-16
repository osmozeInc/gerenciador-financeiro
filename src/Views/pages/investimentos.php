<main class="investimentos-container">
    
    <section class="summary-grid">
        <div class="summary-box">
            <span class="summary-title">Total Investido</span>
            <span class="summary-value" id="resumoInvestido">R$ 0,00</span>
        </div>
        <div class="summary-box">
            <span class="summary-title">Saldo Atual (Estimado)</span>
            <span class="summary-value" id="resumoAtual">R$ 0,00</span>
        </div>
        <div class="summary-box">
            <span class="summary-title">Rentabilidade</span>
            <span class="summary-value positivo" id="resumoRentabilidade">0.00%</span>
        </div>
    </section>

    <section class="card">
        <h2>Registrar Aporte / Compra</h2>
        <form id="aporteForm">
            <div class="form-grid">
                <div class="input-group">
                    <label for="ativo">Ativo (Ticker)</label>
                    <input type="text" id="ativo" required placeholder="Ex: MXRF11, PETR4" style="text-transform: uppercase;">
                </div>
                
                <div class="input-group">
                    <label for="tipo">Classe</label>
                    <select id="tipo" required>
                        <option value="Ações">Ações</option>
                        <option value="FIIs">FIIs</option>
                        <option value="Renda Fixa">Renda Fixa</option>
                        <option value="Cripto">Criptomoedas</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="quantidade">Quantidade</label>
                    <input type="number" id="quantidade" step="0.01" min="0.01" required placeholder="0">
                </div>

                <div class="input-group">
                    <label for="precoCompra">Preço Unitário (R$)</label>
                    <input type="number" id="precoCompra" step="0.01" min="0.01" required placeholder="0.00">
                </div>

                <div class="input-group">
                    <label for="data_aporte">Data da Compra</label>
                    <input type="date" id="data_aporte" required>
                </div>
            </div>
            <button type="submit" class="btn-submit">Adicionar à Carteira</button>
        </form>
    </section>

    <section class="card">
        <h2>Minha Carteira</h2>
        <table>
            <thead>
                <tr>
                    <th>Ativo</th>
                    <th>Classe</th>
                    <th>Qtd.</th>
                    <th>Preço Médio</th>
                    <th>Cotação Atual</th>
                    <th>Total Investido</th>
                    <th>Rentabilidade</th>
                </tr>
            </thead>
            <tbody id="tabelaCarteira">
                </tbody>
        </table>
    </section>

</main>

<script>
    // Setar data
    document.getElementById('data_aporte').valueAsDate = new Date();

    // Estado: simulando o agrupamento do banco de dados (Posição do investidor)
    let carteira = [
        { ativo: 'MXRF11', tipo: 'FIIs', quantidade: 100, precoMedio: 10.50, cotacaoAtual: 10.75 },
        { ativo: 'PETR4', tipo: 'Ações', quantidade: 50, precoMedio: 35.00, cotacaoAtual: 33.20 }
    ];

    const form = document.getElementById('aporteForm');
    
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const ticker = document.getElementById('ativo').value.toUpperCase();
        const tipo = document.getElementById('tipo').value;
        const qtd = parseFloat(document.getElementById('quantidade').value);
        const preco = parseFloat(document.getElementById('precoCompra').value);

        // Lógica de Preço Médio
        const index = carteira.findIndex(item => item.ativo === ticker);
        if (index >= 0) {
            const ativoExistente = carteira[index];
            const totalGastoAnterior = ativoExistente.quantidade * ativoExistente.precoMedio;
            const novoGasto = qtd * preco;
            
            ativoExistente.quantidade += qtd;
            ativoExistente.precoMedio = (totalGastoAnterior + novoGasto) / ativoExistente.quantidade;
        } else {
            // Novo ativo (cotacaoAtual recebe o valor de compra provisoriamente)
            carteira.push({
                ativo: ticker, tipo: tipo, quantidade: qtd, precoMedio: preco, cotacaoAtual: preco
            });
        }

        renderizarInterface();
        form.reset();
        document.getElementById('data_aporte').valueAsDate = new Date();
    });

    function renderizarInterface() {
        const tbody = document.getElementById('tabelaCarteira');
        tbody.innerHTML = '';

        let totalInvestidoGlobal = 0;
        let totalAtualGlobal = 0;

        carteira.forEach(item => {
            const investido = item.quantidade * item.precoMedio;
            const atual = item.quantidade * item.cotacaoAtual;
            const variacaoPct = ((item.cotacaoAtual / item.precoMedio) - 1) * 100;
            
            totalInvestidoGlobal += investido;
            totalAtualGlobal += atual;

            const classeCor = variacaoPct >= 0 ? 'positivo' : 'negativo';
            const sinal = variacaoPct >= 0 ? '+' : '';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${item.ativo}</strong></td>
                <td><span class="badge">${item.tipo}</span></td>
                <td>${item.quantidade}</td>
                <td>R$ ${item.precoMedio.toFixed(2)}</td>
                <td>R$ ${item.cotacaoAtual.toFixed(2)}</td>
                <td>R$ ${investido.toFixed(2)}</td>
                <td class="${classeCor}"><strong>${sinal}${variacaoPct.toFixed(2)}%</strong></td>
            `;
            tbody.appendChild(tr);
        });

        // Atualiza Cards
        document.getElementById('resumoInvestido').innerText = `R$ ${totalInvestidoGlobal.toFixed(2)}`;
        document.getElementById('resumoAtual').innerText = `R$ ${totalAtualGlobal.toFixed(2)}`;
        
        const rentabilidadeGlobal = totalInvestidoGlobal > 0 ? ((totalAtualGlobal / totalInvestidoGlobal) - 1) * 100 : 0;
        const cardRentabilidade = document.getElementById('resumoRentabilidade');
        cardRentabilidade.innerText = `${rentabilidadeGlobal >= 0 ? '+' : ''}${rentabilidadeGlobal.toFixed(2)}%`;
        cardRentabilidade.className = `summary-value ${rentabilidadeGlobal >= 0 ? 'positivo' : 'negativo'}`;
    }

    // Render inicial
    renderizarInterface();
</script>