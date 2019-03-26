<form action="<?= $this->home ?>" method="get">
Valor Minimo: <input type="range" name="vol_min" min="0" max="20" step="1" value="<?= ($this->volMin.'"> '.(500000*$this->volMin))?><br/>
Valor Maximo: <input type="range" name="vol_max" min="0" max="20" step="1" value="<?= ($this->volMax.'"> '.(500000*$this->volMax))?><br/>
    <?php if($this->orderby): ?>
	        <input type="hidden" name="orderby" value="<?= $this->orderby ?>"></input>
    <?php endif; ?>
    Segmento: <select name="segmento">
    <option value="">Nenhum</option>
    <?php while ($this->models['segmento']->fetchRow()){
        $valor = $this->models['segmento']->linhas[0];
        $selected = ($this->segmento==$valor)?' selected':'';
        echo '<option value="'.$valor.'"'.$selected.'>'.$valor.'</option>';
    } ?>
    </select><br>
    <input type="submit" ></input>
</form>
<table class="table table-striped table-bordered">
    <thead class="thead-dark">
    <tr>
    <?php 
    for ($x=0; $x < count($this->models['rank']->colunas); $x++):
    ?>
        <th scope="col">
            <?php
                $coluna = $this->models['rank']->colunas[$x];
                $segmento = ($this->segmento)?'&segmento='.$this->segmento:'';
                echo '<a href="'.$this->home.'?orderby='.$coluna.$segmento.'">'.$coluna.'</a>';
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
                $orderby = ($this->orderby)?'&orderby='.$this->orderby:'';
                if ($coluna=='Fav'){
                    $x = array_search('COD_PAPEL',  $this->models['rank']->colunas);
                    $codPapel = $this->models['rank']->linhas[$x];
                    $x = array_search('Fav',  $this->models['rank']->colunas);
                    $flFavorito = $this->models['rank']->linhas[$x];
                    
                    $value = '<a href="'.$this->home.'?fav='.$codPapel.'">'; 
                    $value .= '<i class="'.(($flFavorito)?'fas':'far').' fa-heart"></i></a>';
                } elseif ($coluna=='SEGMENTO') {
                 $value = '<a href="'.$this->home.'?segmento='.$linha.$orderby.'">'.$linha.'</a>';
                } elseif ($coluna=='COD_PAPEL') {
									         $value = '<a href="'.$this->home.'?cod_papel='.$linha.'">'.$linha.'</a>';
								      } elseif (strpos($coluna,'VAR') !== false){
                    $cor = ($linha > 0) ? 'text-success':'text-danger';
                    $value ='<span class="'.$cor.'">'.$linha.'</span>';
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