<main class="transacoes-container">
    <section class="card">
        <h2>Nova Transação</h2>
        <form id="transacaoForm">
            <div class="form-grid">
                <div class="input-group" style="grid-column: span 2;">
                    <label for="descricao">Descrição</label>
                    <input type="text" id="descricao" name="descricao" required placeholder="Ex: Conta de Luz">
                </div>
                
                <div class="input-group">
                    <label for="categoria">Categoria</label>
                    <select class="js-abrir-modal-select" id="categoria" name="categoria" data-target="modal-nova-categoria" required>
                        <option value="" disabled selected>Selecione...</option>
                        <optgroup label="Receitas (R)"></optgroup>
                        <optgroup label="Despesas (D)"></optgroup>
                        <optgroup label="Investimentos (I)"></optgroup>
                        <optgroup label="Cofres (C)"></optgroup>

                        <optgroup label="Ações">
                            <option value="new">+ Nova Categoria</option>
                        </optgroup>
                    </select>
                </div>

                <div class="input-group">
                    <label for="valor">Valor (R$)</label>
                    <input type="number" id="valor" name="valor" step="0.01" min="0.01" required placeholder="0.00">
                </div>

                <div class="input-group">
                    <label for="data_transacao">Data</label>
                    <input type="date" id="data" name="data" required>
                </div>

                <div class="input-group">
                    <label for="pagamento">Forma de Pagamento</label>
                    <select class="js-abrir-modal-select" id="pagamento" name="pagamento" data-target="modal-novo-pagamento" required>
                        <option value="" disabled selected>Selecione...</option>
                        <option value="new">+ Novo Pagamento</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="parcelas">Parcelas</label>
                    <input type="number" id="parcelas" name="parcelas" min="1" value="1" required>
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
                    <th>Tipo</th>
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