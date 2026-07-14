<main class="transacoes-container">
    <section class="card">
        <h2>Nova Movimentação</h2>

        <div class="tipo-transacao" id="tipo-transacao">
            <button type="button" class="btn-tipo-transacao" data-idForm="receitaForm">Receita</button>
            <button type="button" class="btn-tipo-transacao" data-idForm="despesaForm">Despesa</button>
            <button type="button" class="btn-tipo-transacao" data-idForm="investimentoForm">Investimento</button>
            <button type="button" class="btn-tipo-transacao" data-idForm="cofreForm">Cofre</button>
        </div>

        <div class="badge-info" id="aviso">
            <p>Selecione o tipo de movimentação</p>
        </div>
        
        <form id="receitaForm">
            <div class="form-grid" id="bloco-base">
                <div class="input-group" style="grid-column: span 2;">
                    <label for="descricao">Descrição</label>
                    <input type="text" name="descricao" required placeholder="Ex: Salário, Bolsa, Venda...">
                </div>
                
                <div class="input-group">
                    <label for="categoria_id">Categoria</label>
                    <select class="js-abrir-modal-select" id="categoriasR" name="categoria_id" data-target="modal-nova-categoria" required>
                        <option value="" disabled selected>Selecione...</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="conta_id">Conta de Origem/Destino</label>
                    <select class="js-abrir-modal-select" id="metodoContaR" name="conta_id" data-target="modal-novo-pagamento" required>
                        <option value="" disabled selected>Selecione...</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="valor">Valor Total (R$)</label>
                    <input type="number" name="valor" step="0.01" min="0.01" required placeholder="0.00">
                </div>

                <div class="input-group">
                    <label for="data">Data</label>
                    <input type="date" id="dataR" name="data" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">Salvar Receita</button>
        </form>
        
        <form id="despesaForm">
            <div class="form-grid" id="bloco-base">
                <div class="input-group" style="grid-column: span 2;">
                    <label for="descricao">Descrição</label>
                    <input type="text" name="descricao" required placeholder="Ex: Conta de Luz, Passeio em Família">
                </div>
                
                <div class="input-group">
                    <label for="categoria_id">Categoria</label>
                    <select class="js-abrir-modal-select" id="categoriasD" name="categoria_id" data-target="modal-nova-categoria" required>
                        <option value="" disabled selected>Selecione...</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="conta_id">Conta de Origem/Destino</label>
                    <select class="js-abrir-modal-select" id="metodoContaD" name="conta_id" data-target="modal-novo-pagamento" required>
                        <option value="" disabled selected>Selecione...</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="valor">Valor Total (R$)</label>
                    <input type="number" name="valor" step="0.01" min="0.01" required placeholder="0.00">
                </div>

                <div class="input-group">
                    <label for="data">Data</label>
                    <input type="date" id="dataD" name="data" required>
                </div>
                
                <div class="input-group checkbox-group">
                    <label>Compra Parcelada?</label>
                    <label class="switch">
                        <input type="checkbox" id="parcelado" name="parcelado">
                        <div class="slider"> <div class="circle"> <svg class="cross" xml:space="preserve" style="enable-background:new 0 0 512 512" viewBox="0 0 365.696 365.696" y="0" x="0" height="6" width="6" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" xmlns="http://www.w3.org/2000/svg"> <g> <path data-original="#000000" fill="currentColor" d="M243.188 182.86 356.32 69.726c12.5-12.5 12.5-32.766 0-45.247L341.238 9.398c-12.504-12.503-32.77-12.503-45.25 0L182.86 122.528 69.727 9.374c-12.5-12.5-32.766-12.5-45.247 0L9.375 24.457c-12.5 12.504-12.5 32.77 0 45.25l113.152 113.152L9.398 295.99c-12.503 12.503-12.503 32.769 0 45.25L24.48 356.32c12.5 12.5 32.766 12.5 45.247 0l113.132-113.132L295.99 356.32c12.503 12.5 32.769 12.5 45.25 0l15.081-15.082c12.5-12.504 12.5-32.77 0-45.25zm0 0"></path> </g> </svg> <svg class="checkmark" xml:space="preserve" style="enable-background:new 0 0 512 512" viewBox="0 0 24 24" y="0" x="0" height="10" width="10" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" xmlns="http://www.w3.org/2000/svg"> <g> <path class="" data-original="#000000" fill="currentColor" d="M9.707 19.121a.997.997 0 0 1-1.414 0l-5.646-5.647a1.5 1.5 0 0 1 0-2.121l.707-.707a1.5 1.5 0 0 1 2.121 0L9 14.171l9.525-9.525a1.5 1.5 0 0 1 2.121 0l.707.707a1.5 1.5 0 0 1 0 2.121z"></path> </g> </svg> </div> </div>
                    </label>
                </div>
                
                <div class="input-group hidden" id="input-parcelas">
                    <label for="qtd_parcelas">Quantidade de Parcelas</label>
                    <input type="number" id="qtd_parcelas" name="qtd_parcelas" min="1" value="1">
                </div>
            </div>

            <button type="submit" class="btn-submit">Salvar Despesa</button>
        </form>
        
        <form id="investimentoForm">
            <div class="form-grid" id="bloco-base">
                <div class="input-group" style="grid-column: span 2;">
                    <label for="descricao">Descrição</label>
                    <input type="text" name="descricao" required placeholder="Ex: 10% do Salário, Dinheiro Sobrando">
                </div>
                
                <div class="input-group">
                    <label for="conta_id">Conta de Origem/Destino</label>
                    <select class="js-abrir-modal-select" id="metodoContaI" name="conta_id" data-target="modal-novo-pagamento" required>
                        <option value="" disabled selected>Selecione...</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="data">Data</label>
                    <input type="date" id="dataI" name="data" required>
                </div>

                <div class="input-group">
                    <label for="ativo">Ativo (Ticker)</label>
                    <input type="text" id="ativo" name="ativo" placeholder="Ex: MXRF11" style="text-transform: uppercase;">
                </div>
                
                <div class="input-group">
                    <label for="classe">Classe</label>
                    <select class="js-abrir-modal-select" id="classeInvestimento" name="classe" data-target="modal-nova-classe">
                        <option value="" disabled selected>Selecione...</option>
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

            <button type="submit" class="btn-submit btn-invest">Salvar Investimento</button>
        </form>
        
        <form id="cofreForm">
            <div class="form-grid" id="bloco-base">
                <div class="input-group" style="grid-column: span 2;">
                    <label for="descricao">Descrição</label>
                    <input type="text" name="descricao" required placeholder="Ex: 10% do Salário, Dinheiro Sobrando">
                </div>
                
                <div class="input-group">
                    <label for="conta_id">Conta de Origem/Destino</label>
                    <select class="js-abrir-modal-select" id="metodoContaC" name="conta_id" data-target="modal-novo-pagamento" required>
                        <option value="" disabled selected>Selecione...</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="data">Data</label>
                    <input type="date" id="dataC" name="data" required>
                </div>

                <div class="input-group" style="grid-column: span 2;">
                    <label for="id_cofre">Selecione o Cofre Destino</label>
                    <select id="cofresCofre" name="id_cofre">
                        <option value="" disabled selected>Selecione...</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="valor">Valor Total (R$)</label>
                    <input type="number" name="valor" step="0.01" min="0.01" required placeholder="0.00">
                </div>
            </div>

            <button type="submit" class="btn-submit btn-cofre">Salvar no Cofre</button>
        </form>
    </section>
    
    <section class="card">
        <div class="card-header">
            <h2 style="margin: 0;">Transações Recentes</h2>
            
            <div class="tools-container">
                <span style="position: relative;">
                    <input type="text" id="inputPesquisaTabela" placeholder="Pesquisar...">
                    <i class="bi bi-search" style="position: absolute; top: 50%; right: 12px; transform: translateY(-50%);"></i>
                </span>
                
                <button type="button" id="btnRecarregarTabela" title="Recarregar">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
                
                <button type="button" id="btnFiltrarTabela" class="js-abrir-modal" data-target="modal-filtro-transacoes" title="Filtrar">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>
        </div>

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

        <div class="badge-info">
            <p> Exibimos suas últimas 100 movimentações. Se não achou o que procura, <a href="/historicoCompleto">busque no histórico completo</a>.</p>
        </div>
    </section>
</main>