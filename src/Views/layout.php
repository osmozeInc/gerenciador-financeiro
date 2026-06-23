<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciador Financeiro</title>
    <link rel="stylesheet" href="/assets/css/layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <!-- adiciona o header -->
    <?php require_once __DIR__ . '/partials/header.php'; ?>

    <!-- adiciona a barra lateral -->
    <?php include __DIR__ . '/partials/aside.php'; ?>

    <div class="conteudo-central">
        <?php 
            // Adiciona o conteúdo da view (a main)
            if (isset($viewPath) && file_exists($viewPath))
                require_once $viewPath;
        ?>

        <!-- Adiciona o footer -->
        <?php require_once __DIR__ . '/partials/footer.php'; ?>
    </div>

    <!-- Adiciona os modais na página, todos com display: none -->
    <?php require_once __DIR__ . '/partials/modais.php'; ?>

    <script src="/assets/js/script.js?v=<?php echo time(); ?>" type="module"></script>
    <script type="module" src="/assets/js/<?= $view; ?>.js"></script>
</body>
</html>