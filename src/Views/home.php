<?php require_once __DIR__ . '/partials/header.php'; ?>

<main class="conteudo-central">
    <h1>Painel de Controle</h1>
    <h2>Olá, <?php echo $nome_usuario; ?>!</h2>
    
    <h3>Suas Categorias (Direto da AWS):</h3>
    <ul>
        <?php foreach ($categorias as $cat): ?>
            <li>
                <?php echo $cat['nome']; ?> 
                (Tipo: <?php echo $cat['tipo']; ?>)
            </li>
        <?php endforeach; ?>
    </ul>
</main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>