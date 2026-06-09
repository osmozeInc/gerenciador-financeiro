<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciador Financeiro</title>

    <link rel="stylesheet" href="/assets/css/layout.css">
</head>
<body>

    <aside class="menu-lateral">
        <?php include __DIR__ . '/partials/aside.php'; ?>
    </aside>

    <main class="conteudo-central">
        <?php 
            if (isset($viewPath) && file_exists($viewPath))
                require_once $viewPath;
        ?>
    </main>

    <footer class="rodape">
        <?php require_once __DIR__ . '/partials/footer.php'; ?>
    </footer>


</body>
</html>