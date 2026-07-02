// ==================== utils.js ====================

export async function apiFetch(url, metodo = 'GET', corpo = null) {
    try {
        const opcoes = { method: metodo };
        if (corpo) opcoes.body = corpo;

        const resposta = await fetch(url, opcoes);
        return await resposta.json();
    } catch (erro) {
        // console.error(`Erro crítico de API (${url}):`, erro);

        const resposta = await fetch(url, opcoes);
        return await resposta.json();
    }
}

export function feedbackPopup(tipo, mensagem) {
    const icones = {
        'success': '<i class="bi bi-check-circle"></i>',
        'error':  '<i class="bi bi-bug"></i>',
        'warning': '<i class="bi bi-exclamation-triangle"></i>',
        'info':    '<i class="bi bi-info-circle"></i>'
    };
    const icone = icones[tipo];

    const popup = document.createElement('div');
    popup.classList.add('popup-feedback', tipo);
    popup.innerHTML = `
        <span>
            ${icone} ${mensagem}
        </span>
        <i class="bi bi-x" btn-fechar-popup></i>
    `;

    const popupFeedbackContainer = document.getElementById('popup-feedback-container');
    
    console.log(tipo, mensagem);
    console.log(popupFeedbackContainer);
    console.log(typeof popupFeedbackContainer);

    popupFeedbackContainer.classList.add('active');
    popupFeedbackContainer.appendChild(popup);

    const removerPopup = () => {
        if (popup.parentElement) {
            popup.remove();
            
            if (popupFeedbackContainer.children.length === 0) {
                popupFeedbackContainer.classList.remove('active');
            }
        }
    };

    setTimeout(removerPopup, 4500);
}