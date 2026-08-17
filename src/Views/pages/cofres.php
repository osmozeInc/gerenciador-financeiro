<main class="cofres-container">

    <div class="actions-grid">
        <section class="card">
            <h2>Criar Novo Cofre</h2>
            <form id="formCriarCofre">
                <div class="input-group">
                    <label>Objetivo / Nome</label>
                    <input type="text" id="nomeCofre" required placeholder="Ex: Viagem, Câmera Nova">
                </div>
                <div class="input-group">
                    <label>Meta (R$)</label>
                    <input type="number" id="metaCofre" step="0.01" min="1" required placeholder="0.00">
                </div>
                <div class="input-group">
                    <label>Onde está guardado? (Descritivo)</label>
                    <input type="text" id="localCofre" required placeholder="Ex: Caixinha Nubank, Conta Poupança, Cripto">
                </div>
                <button type="submit" class="btn-submit outline">Criar Meta</button>
            </form>
        </section>

        <section class="card" id="cardResumoCofres">
            
            <div class="resumo-global">
                <h2>Visão Geral dos Cofres</h2>
                <div class="valores-grid">
                    <div class="valor-box">
                        <span class="label">Total Guardado</span>
                        <strong id="totalGuardado" class="text-destaque">R$ 0,00</strong>
                    </div>
                    <div class="valor-box right">
                        <span class="label">Meta Global</span>
                        <strong id="globalMeta">R$ 0,00</strong>
                    </div>
                </div>
                
                <div class="progress-container" style="margin-top: 1rem;">
                    <div class="progress-bar" id="globalBarra" style="width: 0%;"></div>
                </div>
                <div class="progress-text" id="globalTexto">0.0% do patrimônio planejado alcançado</div>
            </div>

            <div class="foco-gamificacao">
                <div class="foco-header">
                    <h2>Cofre em Destaque</h2>
                    <span class="badge-foco">Falta Pouco!</span>
                </div>
                
                <div id="focoConteudo">
                    <p class="foco-cofre" id="cofreNome"></p>

                    <p class="foco-mensagem">
                        Falta <strong id="focoFalta">R$ 0,00</strong> para bater a meta de <strong id="focoMeta">R$ 0,00</strong>
                    </p>
                </div>
                
                <div id="focoVazio" class="hidden">
                    <p class="foco-mensagem">Todos os seus cofres atuais já bateram a meta! 🎉</p>
                </div>

            </div>
            
            <a href="/transacoes" class="btn-submit">Fazer Aporte Agora</a>
        </section>
    </div>

    <section>
        <h2 style="margin-bottom: 1rem;">Meus Cofres</h2>
        <div class="cofres-grid" id="gridCofres">
        </div>
    </section>

</main>

