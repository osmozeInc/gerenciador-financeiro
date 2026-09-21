<main class="cofres-container">

    <div class="actions-grid">
        <section class="card">
            <h2 class="loader-blur">Criar Novo Cofre</h2>
            <form id="formCriarCofre">
                <div class="input-double-group" style="grid-column: span 2;">
                    <span>
                        <label class="loader-blur">Objetivo / Nome</label>
                        <input type="text" id="nomeCofre" name="nomeCofre" class="loader-blur" required placeholder="Ex: Viagem fim de ano">
                    </span>

                    <span>
                        <label class="loader-blur">Meta (R$)</label>
                        <input type="number" id="metaCofre" name="metaCofre" class="loader-blur" step="0.01" min="1" required placeholder="0.00">
                    </span>
                </div>

                <div class="input-group">
                    <label class="loader-blur">Descrição</label>
                    <input type="text" id="descricaoCofre" name="descricaoCofre" class="loader-blur" required placeholder="Ex: Guardar dinheiro para viajar">
                </div>

                <div class="input-double-group">
                    <span>
                        <label class="loader-blur">Onde está guardado? (Descritivo)</label>
                        <input type="text" id="localCofre" name="localCofre" class="loader-blur" required placeholder="Ex: Caixinha Nubank, Conta Poupança, Cripto">
                    </span>
                    
                    <span>
                        <label class="loader-blur">Conta de resgate</label>
                        <select class="js-abrir-modal-select loader-blur" id="metodoContaResgate" name="metodoContaResgate" data-target="modal-novo-pagamento" required>
                            <option value="" disabled selected>Selecione...</option>
                        </select>
                    </span>
                </div>

                <button type="submit" class="btn-submit outline loader-blur">Criar Cofre</button>
            </form>
        </section>

        <section id="cardResumoCofres" class="card">
            
            <div class="resumo-global">
                <h2 class="loader-blur">Visão Geral dos Cofres</h2>
                <div class="valores-grid">
                    <div class="valor-box">
                        <span class="label loader-blur">Total Guardado</span>
                        <strong id="totalGuardado" class="text-destaque loader-blur">R$ 0,00</strong>
                    </div>
                    <div class="valor-box right">
                        <span class="label loader-blur">Meta Global</span>
                        <strong id="globalMeta" class="loader-blur">R$ 0,00</strong>
                    </div>
                </div>
                
                <div class="progress-container" style="margin-top: 1rem;">
                    <div class="progress-bar" id="globalBarra" style="width: 0%;"></div>
                </div>
                <div class="progress-text loader-blur" id="globalTexto">0.0% do patrimônio planejado alcançado</div>
            </div>

            <div id="cofreDestaque" class="foco-gamificacao">
                <div class="foco-header">
                    <h2 class="loader-blur" style="margin-top: 0;">Cofre em Destaque</h2>
                    <span id="cofreBadge" class="badge-foco loader-blur">_ _ _ _ _ _ _ _ _ _ _ _ _</span>
                </div>
                
                <div id="focoConteudo">
                    <p id="cofreNome" class="foco-cofre loader-blur">.</p>

                    <p id="cofreMeta" class="foco-mensagem loader-blur">.</p>
                </div>
                
                <div id="focoVazio" class="hidden">
                    <p class="foco-mensagem">Todos os seus cofres atuais já bateram a meta! 🎉</p>
                </div>

            </div>
            
            <a href="/transacoes" class="btn-submit loader-blur">Fazer Aporte Agora</a>
        </section>
    </div>

    <section>
        <h2 style="margin-bottom: 1rem;" class="loader-blur">Meus Cofres</h2>
        <div class="cofres-grid" id="gridCofresAtivos">

            <div class="cofre-card loader-blur">
                <div class="cofre-header ">
                    <span class="cofre-title">.</span>
                    <span class="cofre-local loader-blur">.</span>
                </div>
                <div class="cofre-valores">
                    <span class="valor-atual loader-blur">.</span>
                    <span class="valor-meta loader-blur">.</span>
                </div>
                <div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                    <div class="progress-text loader-blur">.</div>
                </div>
            </div>

            <div class="cofre-card loader-blur">
                <div class="cofre-header ">
                    <span class="cofre-title">.</span>
                    <span class="cofre-local loader-blur">.</span>
                </div>
                <div class="cofre-valores">
                    <span class="valor-atual loader-blur">.</span>
                    <span class="valor-meta loader-blur">.</span>
                </div>
                <div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                    <div class="progress-text loader-blur">.</div>
                </div>
            </div>

        </div>
    </section>

    <section class="hidden" id="sectionCofresConcluidos">
        <h2 style="margin-bottom: 1rem;" class="loader-blur">Cofres Concluidos</h2>
        <div class="cofres-grid" id="gridCofresConcluidos">

            <div class="cofre-card loader-blur">
                <div class="cofre-header ">
                    <span class="cofre-title">.</span>
                    <span class="cofre-local loader-blur">.</span>
                </div>
                <div class="cofre-valores">
                    <span class="valor-atual loader-blur">.</span>
                    <span class="valor-meta loader-blur">.</span>
                </div>
                <div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                    <div class="progress-text loader-blur">.</div>
                </div>
            </div>

            <div class="cofre-card loader-blur">
                <div class="cofre-header ">
                    <span class="cofre-title">.</span>
                    <span class="cofre-local loader-blur">.</span>
                </div>
                <div class="cofre-valores">
                    <span class="valor-atual loader-blur">.</span>
                    <span class="valor-meta loader-blur">.</span>
                </div>
                <div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                    <div class="progress-text loader-blur">.</div>
                </div>
            </div>

        </div>
    </section>

</main>

