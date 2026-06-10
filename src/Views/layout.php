<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciador Financeiro</title>
    <link rel="stylesheet" href="/assets/css/layout.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>

    <?php include __DIR__ . '/partials/aside.php'; ?>

    <main class="conteudo-central">
        <?php 
            if (isset($viewPath) && file_exists($viewPath))
                require_once $viewPath;
        ?>
    </main>

    <?php require_once __DIR__ . '/partials/footer.php'; ?>

</body>
</html>