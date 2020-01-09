<nav class="navbar navbar-expand-lg navbar-light bg-light">
	<img src="/img/candlestick.png" width="30" height="30" alt="">
	<a class="navbar-brand" href="#">Ranking Ações Bovespa</a>
	<button class="navbar-toggler"
			type="button"
			data-toggle="collapse"
			data-target="#navbarMenu"
			aria-controls="navbarMenu"
			aria-expanded="false"
			aria-label="Toggle navigation">
    	<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarMenu">
	<form action="<?= ($this->home()) ?>" method="get">
      <input type="hidden" name="orderby" value="<?= $this->model->orderby(''); ?>">
      <label for="segmento">Segmento: </label>
      <select name="segmento" class="form-control">
      	<option value="">Nenhum</option>
      	<?php $this->renderOption($this->model->segmentos(), $this->model->segmento); ?>
      </select>
      <div class="form-row">
      <div class="col-xs-6">
      <label for="qtd_pagina">Qtd por Pagina: </label>
      <input type="number" name="qtd_pagina" class="form-control" value="<?= $this->model->itens ?>">
      </div>
      <div class="col-xs-6">
      <label for="pagina">Pagina: </label>
      <select name="pagina" class="form-control">
     	<?php $this->renderOption($this->model->paginas, $this->model->pagina);  ?>
      </select>
      </div>
      </div>
      <div class="form-row">
      <div class="col-xs-6">
      <label for="vol_min">Volume Mínimo: </label>
      <select name="vol_min" class="form-control">
      <option value="0">Todos</option>
      <?php $this->renderOption($this->model->limitesVolume(), $this->model->volMin, true); ?>
      </select>
      </div>
      <div class="col-xs-6">
       <label for="vol_max">Volume Máximo: </label>
      <select name="vol_max" class="form-control">
      <option value="0">Todos</option>
      <?php $this->renderOption($this->model->limitesVolume(), $this->model->volMax, true); ?>
      </select>
      </div>
      </div>
      <input type="submit" >
	</form>
	</div>
</nav>

<div class="table-responsive-sm">
	<?php $this->renderAcoes($this->model->acoes()); ?>
</div>
<div>Icons made by <a href="https://www.flaticon.com/authors/freepik" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>
<script>
	/*var acoes = <?php /*$this->renderArrayJSON($this->model->acoes()); */?>;*/
</script>