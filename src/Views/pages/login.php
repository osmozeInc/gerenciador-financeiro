<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pro Gestão</title>
    
    <!-- CSS da página de autenticação -->
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
                    
                    <h1>Acesse sua conta</h1>
                    <p>Não tem uma conta? <a href="/auth/cadastro">Cadastre-se</a></p>
                </div>

                <?php if (isset($_SESSION['erro_login'])): ?>
                    <div class="erro-msg visivel">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <?= htmlspecialchars($_SESSION['erro_login']); ?>
                    </div>
                    <?php unset($_SESSION['erro_login']); ?>
                <?php endif; ?>

                <form action="/auth/processar" method="POST" class="form-extravagant">
                    
                    <div class="input-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" required autocomplete="email" placeholder="exemplo@email.com">
                    </div>
                    
                    <div class="input-group">
                        <label for="senha">Senha</label>
                        <div class="input-wrapper-password">
                            <input type="password" id="senha" name="senha" required placeholder="••••••••">
                            <i class="bi bi-eye-slash btn-toggle-password" title="Mostrar senha"></i>
                        </div>
                    </div>

                    <div class="options-row">
                        <div class="remember-me">
                            <input type="checkbox" id="lembrar" name="lembrar">
                            <label for="lembrar">Lembrar acesso</label>
                        </div>
                        <a href="/recuperar-senha" class="link-esqueceu" tabindex="-1">Esqueceu a senha?</a>
                    </div>
                    
                    <button type="submit" class="btn-submit">Entrar no Sistema</button>
                    
                    <div class="divider">
                        <span>OU</span>
                    </div>

                    <div class="social-login-column">
                        <button type="button" class="btn-social">
                            <svg version="1.1" width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path fill="#FBBB00" d="M113.47,309.408L95.648,375.94l-65.139,1.378C11.042,341.211,0,299.9,0,256c0-42.451,10.324-82.483,28.624-117.732h0.014l57.992,10.632l25.404,57.644c-5.317,15.501-8.215,32.141-8.215,49.456C103.821,274.792,107.225,292.797,113.47,309.408z"></path>
                                <path fill="#518EF8" d="M507.527,208.176C510.467,223.662,512,239.655,512,256c0,18.328-1.927,36.206-5.598,53.451c-12.462,58.683-45.025,109.925-90.134,146.187l-0.014-0.014l-73.044-3.727l-10.338-64.535c29.932-17.554,53.324-45.025,65.646-77.911h-136.89V208.176h138.887L507.527,208.176L507.527,208.176z"></path>
                                <path fill="#28B446" d="M416.253,455.624l0.014,0.014C372.396,490.901,316.666,512,256,512c-97.491,0-182.252-54.491-225.491-134.681l82.961-67.91c21.619,57.698,77.278,98.771,142.53,98.771c28.047,0,54.323-7.582,76.87-20.818L416.253,455.624z"></path>
                                <path fill="#F14336" d="M419.404,58.936l-82.933,67.896c-23.335-14.586-50.919-23.012-80.471-23.012c-66.729,0-123.429,42.957-143.965,102.724l-83.397-68.276h-0.014C71.23,56.123,157.06,0,256,0C318.115,0,375.068,22.126,419.404,58.936z"></path>
                            </svg>
                            Continuar com o Google
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <!-- Lado da Marca e Benefícios (Direita) com Glassmorphism -->
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
                        <span class="dash-label">Rendimento Global</span>
                        <h2 class="dash-value">+ 14.8%</h2>
                        
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
                    <h3>Bem-vindo de volta<br>ao controle real.</h3>
                </div>
                
            </div>
        </aside>

    </div>
    
</body>
</html>