<main class="assinaturas-container">

    <div class="actions-grid">
        <!-- FORMULÁRIO DE NOVA ASSINATURA -->
        <section class="card">
            <h2 class="loader-blur">Nova Assinatura</h2>
            <form id="formCriarAssinatura">
                <div class="input-double-group" style="grid-column: span 2;">
                    <span>
                        <label class="loader-blur">Serviço / Plataforma</label>
                        <input type="text" id="nomeAssinatura" name="nomeAssinatura" class="loader-blur" required placeholder="Ex: Netflix, Spotify, AWS">
                    </span>

                    <span>
                        <label class="loader-blur">Valor Mensal (R$)</label>
                        <input type="number" id="valorAssinatura" name="valorAssinatura" class="loader-blur" step="0.01" min="0" required placeholder="0.00">
                    </span>
                </div>

                <div class="input-double-group">
                    <span>
                        <label class="loader-blur">Vencimento (Dia)</label>
                        <input type="number" id="diaVencimento" name="diaVencimento" class="loader-blur" min="1" max="31" required placeholder="Ex: 15">
                    </span>
                    
                    <span>
                        <label class="loader-blur">Forma de Pagamento</label>
                        <select class="js-abrir-modal-select loader-blur" id="metodoContaAssinatura" name="metodoContaAssinatura" data-target="modal-novo-pagamento" required>
                            <option value="" disabled selected>Selecione...</option>
                        </select>
                    </span>
                </div>

                <button type="submit" class="btn-submit outline loader-blur">Cadastrar Assinatura</button>
            </form>
        </section>

        <!-- DASHBOARD RESUMO -->
        <section id="cardResumoAssinaturas" class="card">
            <div class="resumo-global">
                <h2 class="loader-blur">Visão Geral de Despesas</h2>
                <div class="valores-grid">
                    <div class="valor-box">
                        <span class="label loader-blur">Custo Fixo Mensal</span>
                        <strong id="totalMensalAssinaturas" class="text-destaque loader-blur" style="color: var(--text-danger);">R$ 0,00</strong>
                    </div>
                    <div class="valor-box right">
                        <span class="label loader-blur">Assinaturas Ativas</span>
                        <strong id="totalAtivas" class="loader-blur">0</strong>
                    </div>
                </div>
                
                <div class="progress-container" style="margin-top: 1rem;">
                    <div class="progress-bar" id="barraComprometimento" style="width: 0%; background-color: var(--text-danger);"></div>
                </div>
            </div>

            <!-- GAMIFICAÇÃO / ALERTA DE FOCO -->
            <div id="assinaturaDestaque" class="foco-gamificacao" style="margin-top: 1.5rem;">
                <div class="foco-header">
                    <h2 class="loader-blur" style="margin-top: 0;">Próximo Vencimento</h2>
                    <span id="assinaturaBadge" class="badge-foco loader-blur warning">_ _ _ _ _ _ _ _</span>
                </div>
                
                <div id="focoConteudoAssinatura">
                    <p id="assinaturaNomeFoco" class="foco-cofre loader-blur">.</p>
                    <p id="assinaturaValorFoco" class="foco-mensagem loader-blur">.</p>
                </div>
                
                <div id="focoVazioAssinatura" class="hidden">
                    <p class="foco-mensagem">Nenhuma assinatura ativa no momento. 🎉</p>
                </div>
            </div>
        </section>
    </div>

    <!-- GRID DE ASSINATURAS -->
    <section>
        <h2 style="margin-bottom: 1rem;" class="loader-blur">Minhas Assinaturas</h2>
        <div class="assinaturas-grid" id="gridAssinaturas">
            <!-- Loader Skeletons (Baseado no layout de cofres) -->
            <div class="assinatura-card loader-blur">
                <div class="assinatura-header">
                    <span class="assinatura-title">.</span>
                    <span class="assinatura-status loader-blur">.</span>
                </div>
                <div class="assinatura-valores">
                    <span class="valor-atual loader-blur">.</span>
                </div>
            </div>
            <div class="assinatura-card loader-blur">
                <div class="assinatura-header">
                    <span class="assinatura-title">.</span>
                    <span class="assinatura-status loader-blur">.</span>
                </div>
                <div class="assinatura-valores">
                    <span class="valor-atual loader-blur">.</span>
                </div>
            </div>
        </div>
    </section>

</main>