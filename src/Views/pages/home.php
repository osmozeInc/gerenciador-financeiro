<h1 class="page-title">Dashboard de <?php echo $nome_usuario; ?></h1>

<article class="resumo-geral">
    <div class="titles-grid">
        <h2 class="title">Resumo Mensal</h2>
        <h2 class="title">Receita Total</h2>
    </div>
    <div class="resumo-geral-grid">
        <div class="resumo-mensal">
            <div class="receita-container">
                <p class="receita-title">Receita</p>
                <p class="receita-valor"><?= $formatacao_real->format($receita_bruta); ?></p>
            </div>
            <div class="despesa-container">
                <p class="despesa-title">Despesa</p>
                <p class="despesa-valor"><?= $formatacao_real->format($despesa_bruta); ?></p>
            </div>
        </div>
        <div class="resumo-total">
            <div class="cofres-container">
                <p class="cofres-title">Cofre</p>
                <p class="cofres-valor"><?= $formatacao_real->format($cofre); ?></p>
            </div>
            <div class="investimentos-container">
                <p class="investimentos-title">Investimentos</p>
                <p class="investimentos-valor"><?= $formatacao_real->format($investidos); ?></p>
            </div>
            <div class="receita-container">
                <p class="receita-title">Receita Total</p>
                <p class="receita-valor"><?= $formatacao_real->format($receita_total); ?></p>
            </div>
        </div>
    </div>
    <div class="divider"></div>
</article>