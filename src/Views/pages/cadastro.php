<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Pro Gestão</title>
    
    <!-- CSS da página de autenticação compartilhado -->
    <link rel="stylesheet" href="/assets/css/login.css">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="login-page-split">
    <div class="split-layout">
        
        <!-- Lado do Formulário (Esquerda) -->
        <main class="form-side">
            <div class="login-card fade-in">
                
                <div class="login-header">
                    
                    <h1>Crie sua conta</h1>
                    <p>Já possui acesso? <a href="/auth/login">Entre aqui</a></p>
                </div>

                <form action="/auth/registrar" method="POST" class="form-extravagant" id="formCadastro">
                    
                    <div class="tipo-conta-selector">
                        <label class="radio-card ativo">
                            <input type="radio" name="tipo_conta" value="B2C" checked>
                            <i class="bi bi-person"></i> Para Mim (PF)
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="tipo_conta" value="B2B">
                            <i class="bi bi-building"></i> Empresa (PJ)
                        </label>
                    </div>

                    <div class="input-group">
                        <label id="labelNome" for="nome">Nome Completo</label>
                        <input type="text" id="nome" name="nome" required placeholder="Digite seu nome">
                    </div>

                    <div class="input-double-group cpf-email-group">
                        <div class="input-group">
                            <label id="labelDocumento" for="documento">CPF</label>
                            <input type="text" id="documento" name="documento" required placeholder="000.000.000-00" maxlength="18">
                        </div>
                        <div class="input-group">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" required autocomplete="email" placeholder="exemplo@email.com">
                        </div>
                    </div>
                    
                    <div class="input-double-group">
                        <div class="input-group">
                            <label for="senha">Senha</label>
                            <div class="input-wrapper-password">
                                <input type="password" id="senha" name="senha" required placeholder="••••••••">
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="confirma_senha">Confirmar Senha</label>
                            <div class="input-wrapper-password">
                                <input type="password" id="confirma_senha" name="confirma_senha" required placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <div id="erroSenha" class="erro-msg">
                        <i class="bi bi-exclamation-triangle-fill"></i> As senhas não coincidem.
                    </div>
                    
                    <button type="submit" class="btn-submit" id="btnRegistrar">Criar Conta Grátis</button>
                    
                    <div class="divider">
                        <span>Registro Seguro</span>
                    </div>
                </form>
            </div>
        </main>

        <!-- Lado da Marca (Direita) - Visual Minimalista e Moderno -->
        <aside class="branding-side">
            <div class="glow-orb orb-1"></div>
            <div class="glow-orb orb-2"></div>

            <div class="branding-content fade-in-delayed">
                
                <!-- Mockup de Dashboard Glassmorphism -->
                <div class="floating-dashboard">
                    <div class="dash-header">
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                    <div class="dash-body">
                        <span class="dash-label">Patrimônio Consolidado</span>
                        <h2 class="dash-value">R$ 14.590,00</h2>
                        
                        <div class="dash-chart">
                            <div class="bar bar-1"></div>
                            <div class="bar bar-2"></div>
                            <div class="bar bar-3"></div>
                            <div class="bar bar-4"></div>
                            <div class="bar bar-5"></div>
                        </div>
                    </div>
                </div>

                <div class="presentation-text">
                    <h3>O controle absoluto<br>do seu dinheiro.</h3>
                </div>
                
            </div>
        </aside>

    </div>

    <!-- Script de manipulação do DOM e validação -->
    <script src="/assets/js/cadastro.js"></script>
</body>
</html>