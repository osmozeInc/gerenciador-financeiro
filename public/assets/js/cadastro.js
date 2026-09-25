document.addEventListener('DOMContentLoaded', () => {
    const radiosTipoConta = document.querySelectorAll('input[name="tipo_conta"]');
    const labelNome = document.getElementById('labelNome');
    const labelDocumento = document.getElementById('labelDocumento');
    const inputNome = document.getElementById('nome');
    const inputDocumento = document.getElementById('documento');
    const formCadastro = document.getElementById('formCadastro');
    
    const inputSenha = document.getElementById('senha');
    const inputConfirma = document.getElementById('confirma_senha');
    const erroMsg = document.getElementById('erroSenha');

    // 1. Dinâmica do Seletor B2C / B2B
    radiosTipoConta.forEach(radio => {
        radio.addEventListener('change', (e) => {
            document.querySelectorAll('.radio-card').forEach(card => card.classList.remove('ativo'));
            e.target.closest('.radio-card').classList.add('ativo');

            if (e.target.value === 'B2B') {
                labelNome.textContent = 'Razão Social da Empresa';
                inputNome.placeholder = 'Digite o nome da empresa';
                labelDocumento.textContent = 'CNPJ';
                inputDocumento.placeholder = '00.000.000/0000-00';
            } else {
                labelNome.textContent = 'Nome Completo';
                inputNome.placeholder = 'Digite seu nome';
                labelDocumento.textContent = 'CPF';
                inputDocumento.placeholder = '000.000.000-00';
            }
            inputDocumento.value = ''; 
        });
    });

    // 2. Máscara ultra-simples para CPF/CNPJ
    inputDocumento.addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, "");
        const isB2B = document.querySelector('input[name="tipo_conta"]:checked').value === 'B2B';
        
        if (isB2B) {
            v = v.replace(/^(\d{2})(\d)/, "$1.$2");
            v = v.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
            v = v.replace(/\.(\d{3})(\d)/, ".$1/$2");
            v = v.replace(/(\d{4})(\d)/, "$1-$2");
            e.target.value = v.substring(0, 18);
        } else {
            v = v.replace(/(\d{3})(\d)/, "$1.$2");
            v = v.replace(/(\d{3})(\d)/, "$1.$2");
            v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
            e.target.value = v.substring(0, 14);
        }
    });

    // 3. Validação de Senhas em Tempo Real (Melhor UX)
    inputConfirma.addEventListener('input', () => {
        if (inputConfirma.value !== '' && inputSenha.value !== inputConfirma.value) {
            erroMsg.classList.add('visivel');
        } else {
            erroMsg.classList.remove('visivel');
        }
    });

    // Impede o envio se as senhas estiverem erradas
    formCadastro.addEventListener('submit', (e) => {
        if (inputSenha.value !== inputConfirma.value) {
            e.preventDefault(); 
            erroMsg.classList.add('visivel');
            inputConfirma.focus();
        }
    });
});