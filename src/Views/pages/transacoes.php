<main class="transacoes-container">
    <section class="card">
        <h2>Nova Movimentação</h2>
        
        <form id="transacaoForm">
            <input type="hidden" name="tipo" id="tipo_transacao" value="">

            <div class="form-grid" id="bloco-base">
                <div class="input-group" style="grid-column: span 2;">
                    <label for="descricao">Descrição</label>
                    <input type="text" id="descricao" name="descricao" required placeholder="Ex: Conta de Luz, Compra PETR4, Depósito Viagem">
                </div>
                
                <div class="input-group">
                    <label for="categoria_id">Categoria</label>
                    <select class="js-abrir-modal-select" id="categoria_id" name="categoria_id" data-target="modal-nova-categoria" required>
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
                    <label for="conta_id">Conta de Origem/Destino</label>
                    <select class="js-abrir-modal-select" id="conta_id" name="conta_id" data-target="modal-novo-pagamento" required>
                        <option value="" disabled selected>Selecione...</option>
                        <option value="new">+ Nova Conta</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="valor">Valor Total (R$)</label>
                    <input type="number" id="valor" name="valor" step="0.01" min="0.01" required placeholder="0.00">
                </div>

                <div class="input-group">
                    <label for="data">Data</label>
                    <input type="date" id="data" name="data" required>
                </div>
            </div>

            <hr class="divisor-blocos" style="margin: 20px 0; display: none;">

            <div class="form-grid" id="bloco-despesa" style="display: none;">
                <div class="input-group checkbox-group">
                    <label>
                        <input type="checkbox" id="parcelado" name="parcelado" value="1">
                        Compra Parcelada?
                    </label>
                </div>
                <div class="input-group">
                    <label for="qtd_parcelas">Quantidade de Parcelas</label>
                    <input type="number" id="qtd_parcelas" name="qtd_parcelas" min="1" value="1" disabled>
                </div>
            </div>

            <div class="form-grid" id="bloco-investimento" style="display: none;">
                <div class="input-group">
                    <label for="ativo">Ativo (Ticker)</label>
                    <input type="text" id="ativo" name="ativo" placeholder="Ex: MXRF11" style="text-transform: uppercase;">
                </div>
                
                <div class="input-group">
                    <label for="classe">Classe</label>
                    <select class="js-abrir-modal-select" id="classe" name="classe" data-target="modal-nova-classe">
                        <option value="" disabled selected>Selecione...</option>
                        <option value="Ações">Ações</option>
                        <option value="FIIs">FIIs</option>
                        <option value="Renda Fixa">Renda Fixa</option>
                        <option value="Cripto">Criptomoedas</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="quantidade">Quantidade</label>
                    <input type="number" id="quantidade" name="quantidade" step="0.01" min="0.01" placeholder="0">
                </div>

                <div class="input-group">
                    <label for="preco">Preço Unitário (R$)</label>
                    <input type="number" id="preco" name="preco" step="0.01" min="0.01" placeholder="0.00">
                </div>
            </div>

            <div class="form-grid" id="bloco-cofre" style="display: none;">
                <div class="input-group">
                    <label for="id_cofre">Selecione o Cofre Destino</label>
                    <select id="id_cofre" name="id_cofre">
                        <option value="" disabled selected>Selecione...</option>
                        </select>
                </div>
            </div>

            <button type="submit" class="btn-submit" style="margin-top: 20px;">Salvar Registro</button>
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
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody id="tabelaTransacoes">
            </tbody>
        </table>
</section>
</main>