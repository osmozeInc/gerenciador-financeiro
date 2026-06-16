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

        <section class="card">
            <h2>Adicionar Saldo</h2>
            <form id="formAportarCofre">
                <div class="input-group">
                    <label>Selecione o Cofre</label>
                    <select id="selectCofre" required>
                        </select>
                </div>
                <div class="input-group">
                    <label>Valor a Adicionar (R$)</label>
                    <input type="number" id="valorAporte" step="0.01" min="0.01" required placeholder="0.00">
                </div>
                <button type="submit" class="btn-submit">Guardar Dinheiro</button>
            </form>
        </section>
    </div>

    <section>
        <h2 style="margin-bottom: 1rem;">Meus Cofres</h2>
        <div class="cofres-grid" id="gridCofres">
        </div>
    </section>

</main>

<script>
    // Simulando banco de dados com alguns contextos práticos
    let cofres = [
        { id: 1, nome: 'Pneus para o Drift', meta: 2500, saldo: 1200, local: 'CDB Liquidez Diária' },
        { id: 2, nome: 'Expedição Off-Road', meta: 4000, saldo: 4000, local: 'Caixinha Nubank' },
        { id: 3, nome: 'Equipamentos de Escalada', meta: 1500, saldo: 300, local: 'Conta Corrente Inter' }
    ];

    const formCriar = document.getElementById('formCriarCofre');
    const formAportar = document.getElementById('formAportarCofre');
    const selectCofre = document.getElementById('selectCofre');
    const gridCofres = document.getElementById('gridCofres');

    // Cria um novo cofre
    formCriar.addEventListener('submit', (e) => {
        e.preventDefault();
        const novoCofre = {
            id: Date.now(),
            nome: document.getElementById('nomeCofre').value,
            meta: parseFloat(document.getElementById('metaCofre').value),
            saldo: 0, // Inicia zerado
            local: document.getElementById('localCofre').value
        };
        cofres.push(novoCofre);
        renderizarUI();
        formCriar.reset();
    });

    // Adiciona saldo a um cofre existente
    formAportar.addEventListener('submit', (e) => {
        e.preventDefault();
        const idCofre = parseInt(document.getElementById('selectCofre').value);
        const valor = parseFloat(document.getElementById('valorAporte').value);
        
        const index = cofres.findIndex(c => c.id === idCofre);
        if(index >= 0) {
            cofres[index].saldo += valor;
            // Trava o saldo máximo no valor da meta (opcional, dependendo da sua regra de negócio)
            // if(cofres[index].saldo > cofres[index].meta) cofres[index].saldo = cofres[index].meta;
        }
        
        renderizarUI();
        formAportar.reset();
    });

    function renderizarUI() {
        gridCofres.innerHTML = '';
        selectCofre.innerHTML = '<option value="" disabled selected>Escolher...</option>';

        cofres.forEach(cofre => {
            // Atualiza o Select do form de aporte (ignora os já concluídos)
            let porcentagem = (cofre.saldo / cofre.meta) * 100;
            if(porcentagem < 100) {
                const option = document.createElement('option');
                option.value = cofre.id;
                option.textContent = cofre.nome;
                selectCofre.appendChild(option);
            }

            // Cálculo seguro da barra
            let pctBarra = porcentagem > 100 ? 100 : porcentagem;
            const concluido = porcentagem >= 100 ? 'concluido' : '';

            // Formatação
            const saldoFormat = cofre.saldo.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            const metaFormat = cofre.meta.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

            // Renderiza o Card
            const card = document.createElement('div');
            card.className = `cofre-card ${concluido}`;
            card.innerHTML = `
                <div class="cofre-header">
                    <span class="cofre-title">${cofre.nome}</span>
                    <span class="cofre-local">${cofre.local}</span>
                </div>
                <div class="cofre-valores">
                    <span class="valor-atual">${saldoFormat}</span>
                    <span class="valor-meta">/ ${metaFormat}</span>
                </div>
                <div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: ${pctBarra}%"></div>
                    </div>
                    <div class="progress-text">${porcentagem.toFixed(1)}% Alcançado</div>
                </div>
            `;
            gridCofres.appendChild(card);
        });
    }

    // Render inicial
    renderizarUI();
</script>