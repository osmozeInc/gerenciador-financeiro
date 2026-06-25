// ==================== utils.js ====================

export async function apiFetch(url, metodo = 'GET', corpo = null) {
    try {
        const opcoes = { method: metodo };
        if (corpo) opcoes.body = corpo;

        const resposta = await fetch(url, opcoes);
        return await resposta.json();
    } catch (erro) {
        console.error(`Erro crítico de API (${url}):`, erro);
        alert('Falha de comunicação com o servidor.');
        return null;
    }
}