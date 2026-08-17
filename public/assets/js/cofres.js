// 1. DADOS (Simulando banco de dados)
let cofres = [
    { id: 1, nome: 'Pneus para o Drift', meta: 2500, saldo: 1200, local: 'CDB Liquidez Diária' },
    { id: 2, nome: 'Expedição Off-Road', meta: 4000, saldo: 4000, local: 'Caixinha Nubank' },
    { id: 3, nome: 'Equipamentos de Escalada', meta: 1500, saldo: 300, local: 'Conta Corrente Inter' }
];


document.addEventListener('DOMContentLoaded', () => {
    // apiFetch para trazer os cofres

    listarCofres(cofres);
    VisaoGeralCofres(cofres);
    cofreEmDestaque(cofres);
});


function listarCofres(cofres) {
    const gridCofres = document.getElementById('gridCofres');
    gridCofres.innerHTML = '';

    cofres.forEach(cofre => {
        const card = document.createElement('div');
        card.className = 'cofre-card';
        card.innerHTML = `
            <div class="cofre-header">
                <span class="cofre-title">${cofre.nome}</span>
                <span class="cofre-local">${cofre.local}</span>
            </div>
            <div class="cofre-valores">
                <span class="valor-atual">${cofre.saldo.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</span>
                <span class="valor-meta">/ ${cofre.meta.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</span>
            </div>
            <div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: ${(cofre.saldo / cofre.meta) * 100}%"></div>
                </div>
                <div class="progress-text">${((cofre.saldo / cofre.meta) * 100).toFixed(1)}% Alcançado</div>
            </div>
        `;
        gridCofres.appendChild(card);
    });
}

function visaoGeralCofres(cofres) {
    const totalGuardadoHtml = document.getElementById('totalGuardado');
    const globalMetaHtml = document.getElementById('globalMeta');
    const globalBarraHtml = document 
}


// 3. EVENTO DE CRIAÇÃO DE NOVO COFRE
formCriar.addEventListener('submit', (e) => {
    e.preventDefault();
    
    const novoCofre = {
        id: Date.now(),
        nome: document.getElementById('nomeCofre').value,
        meta: parseFloat(document.getElementById('metaCofre').value),
        saldo: 0, // Todo cofre novo nasce zerado
        local: document.getElementById('localCofre').value
    };
    
    cofres.push(novoCofre);
    renderizarUI(); // Re-renderiza tudo (lista e resumo)
    formCriar.reset();
});

// 5. FUNÇÃO DE RESUMO E GAMIFICAÇÃO (Foco)
function renderizarResumoPainelDireito(listaCofres) {
    if (!listaCofres || listaCofres.length === 0) return;

    // --- CÁLCULO GLOBAL ---
    const totalGuardado = listaCofres.reduce((acc, cofre) => acc + parseFloat(cofre.saldo), 0);
    const metaGlobal = listaCofres.reduce((acc, cofre) => acc + parseFloat(cofre.meta), 0);
    const porcentagemGlobal = metaGlobal > 0 ? (totalGuardado / metaGlobal) * 100 : 0;
    let pctGlobalBarra = porcentagemGlobal > 100 ? 100 : porcentagemGlobal;

    // Atualiza DOM Global
    document.getElementById('globalGuardado').textContent = totalGuardado.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    document.getElementById('globalMeta').textContent = metaGlobal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    document.getElementById('globalBarra').style.width = `${pctGlobalBarra}%`;
    document.getElementById('globalTexto').textContent = `${porcentagemGlobal.toFixed(1)}% do patrimônio planejado alcançado`;

    // --- CÁLCULO DO FOCO (O mais próximo de bater a meta) ---
    const divConteudo = document.getElementById('focoConteudo');
    const divVazio = document.getElementById('focoVazio');
    const badgeFoco = document.querySelector('.badge-foco');

    // Filtra apenas os cofres que ainda NÃO bateram a meta
    const cofresIncompletos = listaCofres.filter(c => parseFloat(c.saldo) < parseFloat(c.meta));

    if (cofresIncompletos.length === 0) {
        // Cenário: Todos os cofres bateram a meta
        divConteudo.classList.add('hidden');
        divVazio.classList.remove('hidden');
        badgeFoco.style.display = 'none';
    } else {
        // Cenário: Existe cofre incompleto. Acha o mais próximo.
        divConteudo.classList.remove('hidden');
        divVazio.classList.add('hidden');
        badgeFoco.style.display = 'inline-block';

        // Ordena pelo percentual de conclusão (do maior para o menor)
        cofresIncompletos.sort((a, b) => {
            const pctA = a.saldo / a.meta;
            const pctB = b.saldo / b.meta;
            return pctB - pctA; 
        });

        const cofreFoco = cofresIncompletos[0]; // Pega o primeiro da lista
        const valorFaltante = parseFloat(cofreFoco.meta) - parseFloat(cofreFoco.saldo);

        // Atualiza DOM do Card de Foco
        document.getElementById('focoFalta').textContent = valorFaltante.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        document.getElementById('focoNome').textContent = `"${cofreFoco.nome}"`;
    }
}

