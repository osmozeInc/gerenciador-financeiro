<main class="contas-container">
    
    <section>
        <div class="header-section">
            <h2>Minhas Contas</h2>
            <div class="saldo-total">
                <span>Saldo Total Disponível</span>
                <strong id="saldoGlobal">R$ 0,00</strong>
            </div>
        </div>
        
        <div class="cards-grid" id="gridContas">
            <!-- Gerado via JS -->
        </div>
    </section>

    <section>
        <div class="header-section">
            <h2>Cartões de Crédito</h2>
        </div>

        <div class="cards-grid" id="gridCartoes">
            <!-- Gerado via JS -->
        </div>
    </section>

</main>

<script>
    // ESTADO DA APLICAÇÃO (Dados Simulados)
    const estado = {
        contas: [
            { id: 1, banco: 'Nubank', tipo: 'Conta Corrente', saldo: 4500.50 },
            { id: 2, banco: 'Inter', tipo: 'Conta PJ', saldo: 12400.00 },
            { id: 3, banco: 'Itaú', tipo: 'Poupança', saldo: 150.00 }
        ],
        cartoes: [
            { 
                id: 1, nome: 'Nubank Ultravioleta', 
                limiteTotal: 8000, limiteUsado: 3250.80, 
                diaFechamento: 25, diaVencimento: 5, 
                status: 'Aberta' // Aberta, Fechada, Paga
            },
            { 
                id: 2, nome: 'Inter Black', 
                limiteTotal: 15000, limiteUsado: 14500.00, 
                diaFechamento: 10, diaVencimento: 20, 
                status: 'Fechada' 
            }
        ]
    };

    // RENDERIZAÇÃO
    function renderizarContas() {
        const grid = document.getElementById('gridContas');
        grid.innerHTML = '';
        let saldoGlobal = 0;

        estado.contas.forEach(conta => {
            saldoGlobal += conta.saldo;
            const saldoFormatado = conta.saldo.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            
            const card = document.createElement('div');
            card.className = 'box-card';
            card.innerHTML = `
                <div class="box-header">
                    <span class="box-title">${conta.banco}</span>
                    <span class="badge bg-gray">${conta.tipo}</span>
                </div>
                <div>
                    <div class="valor-sub">Saldo Disponível</div>
                    <div class="valor-principal ${conta.saldo < 0 ? 'bg-red' : ''}" style="${conta.saldo < 0 ? 'color: var(--text-danger);' : ''}">${saldoFormatado}</div>
                </div>
                <button class="btn-action" onclick="alert('Abre modal para ajuste de saldo')">Ajustar Saldo</button>
            `;
            grid.appendChild(card);
        });

        document.getElementById('saldoGlobal').innerText = saldoGlobal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function renderizarCartoes() {
        const grid = document.getElementById('gridCartoes');
        grid.innerHTML = '';

        estado.cartoes.forEach(cartao => {
            const limiteDisponivel = cartao.limiteTotal - cartao.limiteUsado;
            const pctUsado = (cartao.limiteUsado / cartao.limiteTotal) * 100;
            
            const usadoFormatado = cartao.limiteUsado.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            const disponivelFormatado = limiteDisponivel.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

            // Lógica visual de status
            let badgeClass = 'bg-yellow';
            if(cartao.status === 'Fechada') badgeClass = 'bg-red';
            if(cartao.status === 'Paga') badgeClass = 'bg-green';

            const card = document.createElement('div');
            card.className = 'box-card';
            card.innerHTML = `
                <div class="box-header">
                    <span class="box-title">${cartao.nome}</span>
                    <span class="badge ${badgeClass}">${cartao.status}</span>
                </div>
                
                <div>
                    <div class="valor-sub">Fatura Atual</div>
                    <div class="valor-principal">${usadoFormatado}</div>
                    
                    <div class="progress-container">
                        <div class="progress-bar" style="width: ${pctUsado > 100 ? 100 : pctUsado}%"></div>
                    </div>
                    <div class="valor-sub" style="text-align: right; margin-top: 0.25rem;">Limite Disp: ${disponivelFormatado}</div>
                </div>

                <div class="card-dates">
                    <div>Fechamento <strong>Dia ${cartao.diaFechamento}</strong></div>
                    <div style="text-align: right;">Vencimento <strong>Dia ${cartao.diaVencimento}</strong></div>
                </div>

                ${cartao.status !== 'Paga' 
                    ? `<button class="btn-action primary" onclick="pagarFatura(${cartao.id})">Marcar Fatura como Paga</button>`
                    : `<button class="btn-action">Fatura Paga</button>`
                }
            `;
            grid.appendChild(card);
        });
    }

    // AÇÃO SIMULADA DE NEGÓCIO
    window.pagarFatura = function(id) {
        const cartao = estado.cartoes.find(c => c.id === id);
        if(cartao) {
            cartao.status = 'Paga';
            cartao.limiteUsado = 0; // Libera o limite
            renderizarCartoes();
        }
    };

    // INICIALIZAÇÃO
    renderizarContas();
    renderizarCartoes();
</script>
