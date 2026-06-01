<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciador Financeiro</title>
    <style>
        /* Estilo super básico só para você visualizar a separação */
        body { display: flex; font-family: sans-serif; margin: 0; height: 100vh; }
        aside { width: 250px; background: #333; color: white; padding: 20px; }
        aside a { color: white; text-decoration: none; display: block; margin: 10px 0; }
        main { flex: 1; padding: 20px; background: #f4f4f4; }
    </style>
</head>
<body>

    <aside>
        <h2>Financeiro</h2>
        <nav>
            <a href="/home">Dashboard</a>
            <a href="/transacoes">Lançamentos</a>
        </nav>
    </aside>

    <?php 
        if (isset($viewPath) && file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            echo "<p>Erro: View principal não encontrada.</p>";
        }
    ?>

</body>
</html>