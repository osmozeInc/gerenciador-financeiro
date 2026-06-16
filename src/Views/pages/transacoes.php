<main class="transacoes-container">
    <section class="card">
        <h2>Nova Transação</h2>
        <form id="transacaoForm">
            <div class="form-grid">
                <div class="input-group" style="grid-column: span 2;">
                    <label for="descricao">Descrição</label>
                    <input type="text" id="descricao" required placeholder="Ex: Conta de Luz">
                </div>
                
                <div class="input-group">
                    <label for="categoria_id">Categoria</label>
                    <select id="categoria_id" required>
                        <option value="" disabled selected>Selecione...</option>
                        <optgroup label="Receitas (R)">
                            <option value="1" data-tipo="R">Salário</option>
                            <option value="2" data-tipo="R">Freela</option>
                        </optgroup>
                        <optgroup label="Despesas (D)">
                            <option value="3" data-tipo="D">Moradia</option>
                            <option value="4" data-tipo="D">Alimentação</option>
                        </optgroup>
                        <optgroup label="Ações">
                            <option value="new">+ Nova Categoria</option>
                        </optgroup>
                    </select>
                </div>

                <div class="input-group">
                    <label for="valor">Valor (R$)</label>
                    <input type="number" id="valor" step="0.01" min="0.01" required placeholder="0.00">
                </div>

                <div class="input-group">
                    <label for="data_transacao">Data</label>
                    <input type="date" id="data_transacao" required>
                </div>

                <div class="input-group">
                    <label for="pagamento_id">Forma de Pagamento</label>
                    <select id="pagamento_id" required>
                        <option value="" disabled selected>Selecione...</option>
                        <option value="1">PIX</option>
                        <option value="2">Cartão de Crédito</option>
                        <option value="3">Dinheiro</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="parcelas">Parcelas</label>
                    <input type="number" id="parcelas" min="1" value="1" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">Salvar Registro</button>
        </form>
    </section>

    <section class="card">
        <h2>Transações Recentes</h2>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Pagamento</th>
                    <th>Parc.</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody id="tabelaTransacoes">
            </tbody>
        </table>
    </section>
</main>

<script>
    // Setar data atual no carregamento
    document.getElementById('data_transacao').valueAsDate = new Date();

    const form = document.getElementById('transacaoForm');
    const categoriaSelect = document.getElementById('categoria_id');
    const valorInput = document.getElementById('valor');
    const tabelaBody = document.getElementById('tabelaTransacoes');

    // Lógica de Feedback Visual do Input de Valor
    categoriaSelect.addEventListener('change', (e) => {
        const selectedOption = e.target.options[e.target.selectedIndex];
        
        // Verifica se clicou em "+ Nova Categoria"
        if(selectedOption.value === 'new') {
            alert('Aqui abriria um modal para criar uma nova categoria.');
            e.target.value = ""; // Reseta o select
            valorInput.className = '';
            return;
        }

        const tipo = selectedOption.getAttribute('data-tipo');
        
        // Limpa classes anteriores e aplica a cor correspondente
        valorInput.className = '';
        if(tipo) valorInput.classList.add(`tipo-${tipo}`);
    });

    // Submissão e renderização na tabela
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const selectedOption = categoriaSelect.options[categoriaSelect.selectedIndex];
        const tipo = selectedOption.getAttribute('data-tipo');
        const nomeCategoria = selectedOption.text;
        const nomePagamento = document.getElementById('pagamento_id').options[document.getElementById('pagamento_id').selectedIndex].text;
        
        const transacao = {
            id: Date.now(),
            descricao: document.getElementById('descricao').value,
            categoria: nomeCategoria,
            tipo: tipo,
            pagamento: nomePagamento,
            parcelas: document.getElementById('parcelas').value,
            valor: parseFloat(document.getElementById('valor').value),
            data: document.getElementById('data_transacao').value
        };

        adicionarLinhaTabela(transacao);
        form.reset();
        document.getElementById('data_transacao').valueAsDate = new Date();
        valorInput.className = ''; // Reseta as cores
    });

    function adicionarLinhaTabela(transacao) {
        const dataFormatada = transacao.data.split('-').reverse().join('/');
        const valorFormatado = transacao.valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${dataFormatada}</td>
            <td>${transacao.descricao}</td>
            <td><span class="badge ${transacao.tipo}">${transacao.categoria}</span></td>
            <td>${transacao.pagamento}</td>
            <td>${transacao.parcelas}x</td>
            <td class="tipo-${transacao.tipo}">${transacao.tipo === 'D' ? '- ' : '+ '}${valorFormatado}</td>
        `;
        
        // Adiciona no topo da tabela
        tabelaBody.insertBefore(tr, tabelaBody.firstChild);
    }
</script>
