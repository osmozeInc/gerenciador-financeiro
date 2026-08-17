<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gerenciador Financeiro</title>
    
    <!-- Puxa o seu CSS global para herdar as variáveis de cor -->
    <link rel="stylesheet" href="/assets/css/style.css"> 
    
    <link rel="stylesheet" href="/assets/css/login.css">

</head>
<body class="login-page">
    <main class="login-wrapper">
        <div class="login-card">
            
            <!-- Cabeçalho de Identidade -->
            <div class="login-header">
                <div class="logo-icon"><i class="bi bi-wallet2"></i></div>
                <h1>Acesso ao Sistema</h1>
                <p>Gerencie suas finanças com inteligência.</p>
            </div>

            <!-- O PHP verifica se o Controller mandou alguma mensagem de erro -->
            <?php if (isset($_SESSION['erro_login'])): ?>
                <div class="erro-msg">
                    <i class="bi bi-exclamation-triangle"></i>
                    <?= htmlspecialchars($_SESSION['erro_login']); ?>
                </div>
                <!-- Regra de Ouro: Apaga o erro da sessão para ele não aparecer no próximo F5 -->
                <?php unset($_SESSION['erro_login']); ?>
            <?php endif; ?>

            <!-- Formulário raiz: POST direto para o roteador -->
            <form action="/auth/processar" method="POST" class="form-simple">
                <div class="input-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required autocomplete="email" placeholder="admin@teste.com">
                </div>
                
                <div class="input-group">
                    <div class="label-row">
                        <label for="senha">Senha</label>
                        <!-- Padrão obrigatório de UX -->
                        <a href="/recuperar-senha" class="link-esqueceu">Esqueceu a senha?</a>
                    </div>
                    <input type="password" id="senha" name="senha" required placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn-submit">Entrar</button>
            </form>
            
        </div>
    </main>
</body>
</html>