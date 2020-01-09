	<h1>Ranking Ações Bovespa</h1>
	<form action="<?= ($this->home.'?'.$this->params()) ?>" method="get">
      <input type="hidden" name="orderby" value="<?= $this->orderby(''); ?>">
      <label for="segmento">Segmento: </label>
      <select name="segmento" class="form-control">
      <option value="">Nenhum</option>
      <?php while ($this->models['segmento']->fetchRow()){
          $valor = $this->models['segmento']->linhas[0];
          $selected = ($this->segmento==$valor)?' selected':'';
          echo '<option value="'.$valor.'"'.$selected.'>'.$valor.'</option>';
      } ?>
      </select>
      <div class="form-row">
      <div class="col-xs-6">
      <label for="qtd_pagina">Qtd por Pagina: </label>
      <input type="number" name="qtd_pagina" class="form-control" value="<?= $this->qtdPagina ?>">
      </div>
      <div class="col-xs-6">
      <label for="pagina">Pagina: </label>
      <select name="pagina" class="form-control">
     <?php
          $this->models['qtd_pag']->fetchRow();
          $qtd_pag = (int)$this->models['qtd_pag']->linhas[0];
          for ($i=1; $i<=$qtd_pag; $i++ ){
              $selected = ($this->pagina==$i)?' selected':'';
              echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
      } ?>
      </select>
      </div>
      </div>
      <div class="form-row">
      <div class="col-xs-6">
      <label for="vol_min">Volume Mínimo: </label>
      <select name="vol_min" class="form-control">
      <option value="0">Todos</option>
      <?php foreach($this->limitesVolume as $value ){
          $selected = ($this->volMin==$value)?' selected':'';
          echo '<option value="'.$value.'"'.$selected.'>'.number_format($value,0,',','.').'</option>';
      }
      ?>
      </select>
      </div>
      <div class="col-xs-6">
       <label for="vol_max">Volume Máximo: </label>
      <select name="vol_max" class="form-control">
      <option value="0">Todos</option>
      <?php foreach($this->limitesVolume as $value ){
          $selected = ($this->volMax==$value)?' selected':'';
          echo '<option value="'.$value.'"'.$selected.'>'.number_format($value,0,',','.').'</option>';
      }
      ?>
      </select>
      </div>
      </div>
      <input type="submit" >
	</form>

<div role="main" class="ui-content">
<div style="overflow-x:auto">
<table class="table table-striped table-bordered">
    <thead class="thead-dark">
    <tr>
    <?php
    for ($x=0; $x < count($this->models['rank']->colunas); $x++):
    ?>
        <th scope="col">
            <?php
                $coluna = $this->models['rank']->colunas[$x];
                $this->addParams('orderby', $coluna);
                echo '<a href="'.($this->home.'?'.$this->params()).'">'.$coluna.'</a>';
                $this->removeParams('orderby');
                if($this->orderby) $this->addParams('orderby', $this->orderby);
            ?>
        </th>
    <?php endfor; ?>
    </tr>
    </thead>
    <tbody>
    <?php while ($this->models['rank']->fetchRow()): ?>
        <tr>
            <?php for ($x=0; $x < count($this->models['rank']->colunas); $x++){
                echo '<td>';
                $linha = $this->models['rank']->linhas[$x];
                $coluna = $this->models['rank']->colunas[$x];
                if ($coluna=='Fav'){
                    $x = array_search('COD_PAPEL',  $this->models['rank']->colunas);
                    $this->addParams('fav', $this->models['rank']->linhas[$x]);
                    $value = '<a href="'.($this->home.'?'.$this->params()).'">';
                    $this->removeParams('fav');

                    $x = array_search('Fav',  $this->models['rank']->colunas);
                    $flFavorito = $this->models['rank']->linhas[$x];
                    $value .= '<i class="'.(($flFavorito)?'fas':'far').' fa-heart"></i></a>';

                } elseif ($coluna=='SEGMENTO') {
                    $this->addParams('segmento', $linha);
                    $value = '<a href="'.($this->home.'?'.$this->params()).'">'.$linha.'</a>';
                    $this->removeParams('segmento');
                    if ($this->segmento) $this->addParams('segmento', $this->segmento);

                } elseif ($coluna=='COD_PAPEL') {
									         $value = '<a href="'.$this->home.'?cod_papel='.$linha.'">'.$linha.'</a>';

								      } elseif (strpos($coluna,'VAR') !== false){
                    $cor = ($linha > 0) ? 'text-success':'text-danger';
                    $value ='<span class="'.$cor.'">'.number_format($linha,2,',','.').'%</span>';
                } elseif (strpos($coluna,'PRECO') !== false  ){
                    $value = number_format($linha,2,',','.');

                 } elseif (strpos($coluna,'QTD') !== false ){
                    $value = number_format($linha,0,',','.');

                } else {
                    $value = $linha;
                }
                echo $value;
                echo '</td>';
                }
                ?>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>
</div>