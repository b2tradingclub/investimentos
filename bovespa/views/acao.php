<h1>Histórico <?= $this->model->codPapel() ?></h1>
<a href="<?= $this->home ?>">Voltar</a> |
<a target="_blank" href="https://br.tradingview.com/symbols/BMFBOVESPA-<?= $this->model->codPapel() ?>/">Gráfico TradingView</a> |
<a target="_blank" href="https://br.advfn.com/bolsa-de-valores/bovespa/<?= $this->model->codPapel() ?>/cotacao">Gráfico ADVFN</a>

<?php $this->renderTable($this->model->acao()); ?>