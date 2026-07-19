<main class="historico-container">

    <section class="card header-historico">
        <div class="titulo-historico">
            <h2>Histórico Completo</h2>
            <p>Acompanhe todas as suas entradas, saídas, investimentos e cofres.</p>
        </div>
        <div class="totalizador-historico">
            <span>Soma de todas as movimentações</span>
            <strong id="valorTotal">R$ 0,00</strong>
        </div>
    </section>

    <section class="card">
        <h2>Filtros</h2>
            
        <form class="barra-filtros" id="formFiltros">
            <div class="search-group">
                <span style="position: relative; grid-column: span 2;">
                    <i class="bi bi-search"></i>
                    <input type="text" id="filtroDescricao" placeholder="Descrição...">
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
        
                <input type="number" id="filtroValorPiso" placeholder="Valor Mínimo">
            
                <input type="number" id="filtroValorTeto" placeholder="Valor Máximo">
            </div>

            <div class="filtro-acoes">
                <button type="button" class="btn-acao export" title="Exportar Extrato">
                    <i class="bi bi-download"></i> Exportar Tabela
                </button>

                <span>
                    <button type="button" class="btn-acao clean" id="btn-limpar-filtros" title="Limpar Filtros">
                        <i class="bi bi-eraser"></i> Limpar Filtros
                    </button>
                    <button type="submit" class="btn-acao filter" title="Exportar Extrato">
                        <i class="bi bi-funnel"></i> Aplicar Filtros
                    </button>
                </span>              
            </div>
        </form>
    </section>

    <section class="card tabela-wrapper">
        
        <table>
            <thead>
                <tr>
                    <th style="border-radius: 10px 0 0 0;">Data</th>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Conta</th>
                    <th class="col-valor">Valor</th>
                    <th class="col-acoes">Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaHistoricoCompleto">
            </tbody>
        </table>

        <div class="loader-table">
            <div class="loader"></div>
        </div>

        <div class="badge-info" id="badge-resultados">
            <p>Exibindo <span id="qtd-resultados">0</span> movimentações.</p>
        </div>

    </section>
</main>
