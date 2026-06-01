<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador Financeiro</title>
</head>
<body>
    <div class="navbar">
        <h2>Meu Financeiro</h2>
        <p>Usuário: <?php echo $nome_usuario ?? 'Visitante'; ?></p>
    </div>

    <div class="sidebar">
        <ul>
            <li><a href="/home">Dashboard</a></li>
            <li><a href="/transacoes">Lançamentos</a></li>
        </ul>
    </div>