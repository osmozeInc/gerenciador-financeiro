// Setar data atual no carregamento
document.getElementById('data_transacao').valueAsDate = new Date();

document.getElementById('novaCategoriaForm').addEventListener('submit', function(evento) {
    // 1. Impede que o formulário recarregue a página inteira
    evento.preventDefault(); 

    // 2. Coleta todos os dados preenchidos no form automaticamente
    const dadosFormulario = new FormData(this);

    // 3. O Fetch entra em ação: envia os dados para o Controller
    fetch('/categoria/salvar', {
        method: 'POST',
        body: dadosFormulario
    })
    .then(resposta => resposta.json()) // Transforma a resposta do PHP em um objeto JS
    .then(dados => {
        if (dados.sucesso) {
            alert('Categoria salva com sucesso!');
            fecharModal('modal-nova-categoria'); // Usa a sua função utilitária
            this.reset(); // Limpa os inputs
            
            // Aqui, no futuro, faremos o select de categorias da tela atualizar sozinho
        } else {
            alert('Erro ao salvar: ' + dados.mensagem);
        }
    })
    .catch(erro => {
        console.error('Erro de comunicação:', erro);
        alert('Falha crítica ao conectar com o servidor.');
    });
});