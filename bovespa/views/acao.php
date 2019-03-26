<h1>Histórico <?= $this->codPapel ?></h1>
<a href="<?= $this->home ?>">Voltar</a> |
<a target="_blank" href="https://br.tradingview.com/symbols/BMFBOVESPA-<?= $this->codPapel ?>/">Gráfico TradingView</a> |
<a target="_blank" href="https://br.advfn.com/bolsa-de-valores/bovespa/<?= $this->codPapel ?>/cotacao">Gráfico ADVFN</a>

<table class="table table-striped table-bordered">
    <thead class="thead-dark">
    <tr>
    <?php for ($x=0; $x < count($this->models['acao']->colunas); $x++): ?>
        <th scope="col">
           <?php
            $coluna = $this->models['acao']->colunas[$x];
            echo '<a href="'.$this->home.'?orderby='.$coluna.'&cod_papel='.$this->codPapel.'">'.$coluna.'</a>';
            ?>
        </th>
    <?php endfor; ?>
    </tr>
    </thead>
    <tbody>
    <?php while ($this->models['acao']->fetchRow()): ?>
        <tr>
            <?php for ($x=0; $x < count($this->models['acao']->colunas); $x++): ?>
                <td>
                <?php
                $linha = $this->models['acao']->linhas[$x];
                $coluna = $this->models['acao']->colunas[$x];
                if (strpos($coluna,'VAR') !== false){
                    $cor = ($linha > 0) ? 'text-success':'text-danger';
                    $value ='<span class="'.$cor.'">'.$linha.'</span>';
                } else {
                    $value = $linha;
                }
                echo $value;
                ?>
                </td>
            <?php endfor; ?>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>