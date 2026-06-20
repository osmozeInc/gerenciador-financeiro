<?php
$urlAtual = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>

<aside class="aside-container">
    <div class="logo-container">
        <img src="/images/marca/logo_escura.png" alt="Logo do Gerenciador Financeiro">
        <h2>Pro Gestão</h2>
    </div>
    
    <div class="menus-container">
        <div class="menu-container">
            <hr class="logo-divider">
            <h2>Menu</h2>
            <a href="/home" class="menu-item <?= $urlAtual === '/home' ? 'active' : ''; ?> <?= $urlAtual === '/' ? 'active' : ''; ?>">
                <i class="bi bi-clipboard"></i>
                <p class="menu-item-text">Visão Geral</p>
                <i class="bi bi-chevron-right"></i>
            </a>
            <a href="/transacoes" class="menu-item <?= $urlAtual === '/transacoes' ? 'active' : ''; ?>">
                <i class="bi bi-wallet"></i>
                <p class="menu-item-text">Transações</p>
                <i class="bi bi-chevron-right"></i>
            </a>
            <a href="/investimentos" class="menu-item <?= $urlAtual === '/investimentos' ? 'active' : ''; ?>">
                <i class="bi bi-graph-up"></i>
                <p class="menu-item-text">Investimentos</p>
                <i class="bi bi-chevron-right"></i>
            </a>
            <a href="/cofres" class="menu-item <?= $urlAtual === '/cofres' ? 'active' : ''; ?>">
                <i class="bi bi-piggy-bank"></i>
                <p class="menu-item-text">Cofres</p>
                <i class="bi bi-chevron-right"></i>
            </a>
            <a href="/contas" class="menu-item <?= $urlAtual === '/contas' ? 'active' : ''; ?>">
                <i class="bi bi-credit-card"></i>
                <p class="menu-item-text">Contas e Cartões</p>
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    
        <div class="submenu-container">
            <hr class="divider">
            <button href="" class="submenu-item" onclick="abrirModal('modal-notificacao')">
                <i class="bi bi-bell"></i>
                <p class="submenu-item-text">Notificações</p>
            </button>
            <button href="" class="submenu-item" onclick="abrirModal('modal-configuracoes')">
                <i class="bi bi-gear"></i>
                <p class="submenu-item-text">Configurações</p>
            </button>
            <button class="user-button">
                <i class="bi bi-person-fill"></i>
                <div class="user-text-container">
                    <p class="user-name">Caio Monte</p>
                    <p class="user-email">caio.monte@example.com</p>
                </div>
            </button>
        </div>
    </div>
</aside>