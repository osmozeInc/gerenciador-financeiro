// 1. Ouvinte para exibir os blocos corretos de acordo com a Categoria
document.getElementById('categoria_id').addEventListener('change', function() {
    // Descobre a qual "optgroup" a opção selecionada pertence
    const optionSelecionada = this.options[this.selectedIndex];
    const labelGrupo = optionSelecionada.parentElement.getAttribute('label') || '';

    let tipo = '';
    if (labelGrupo.includes('(R)')) tipo = 'R';
    else if (labelGrupo.includes('(D)')) tipo = 'D';
    else if (labelGrupo.includes('(I)')) tipo = 'I';
    else if (labelGrupo.includes('(C)')) tipo = 'C';

    // Preenche o input invisível que vai para o Controller!
    document.getElementById('tipo_transacao').value = tipo;

    // Reseta a tela: Esconde todos os blocos adicionais e o divisor
    document.querySelector('.divisor-blocos').style.display = 'none';
    document.getElementById('bloco-despesa').style.display = 'none';
    document.getElementById('bloco-investimento').style.display = 'none';
    document.getElementById('bloco-cofre').style.display = 'none';

    // Exibe apenas o bloco correspondente à filha
    if (tipo === 'D') {
        document.querySelector('.divisor-blocos').style.display = 'block';
        document.getElementById('bloco-despesa').style.display = 'grid'; // ou flex
    } else if (tipo === 'I') {
        document.querySelector('.divisor-blocos').style.display = 'block';
        document.getElementById('bloco-investimento').style.display = 'grid';
    } else if (tipo === 'C') {
        document.querySelector('.divisor-blocos').style.display = 'block';
        document.getElementById('bloco-cofre').style.display = 'grid';
    }
});

// 2. Ouvinte para habilitar/desabilitar a quantidade de parcelas
document.getElementById('parcelado').addEventListener('change', function() {
    const inputParcelas = document.getElementById('qtd_parcelas');
    inputParcelas.disabled = !this.checked;
    if (!this.checked) inputParcelas.value = 1; // Reseta se desmarcar
});