<?php
namespace vini\bovespa;
use vini\app\Controller;
use vini\app\Model;
use vini\bovespa\models\ranking;
use vini\bovespa\models\acao;

class Bovespa extends Controller
{
    public function __construct()
    {

	    $view = '';
        if(isset($_GET['cod_papel'])){
            $view = 'acao';
        }

        switch($view){
            case 'acao':
                $this->model = new Acao;
                $this->output .= $this->includeView('bovespa/views/acao.php');
            break;

            default:
				$this->model = new Ranking;
                $this->output .= $this->includeView('bovespa/views/default_responsive.php');
        }
    }

	protected function renderOption($iterator, $selectedValue, $number = false)
	{
		$option = '';
$limite = (\is_array($iterator))?count($iterator):$iterator;
		for ($i=1; $i<=$limite; $i++ ){
			$value = (\is_array($iterator))?$iterator[$i-1]:$i;
			$selected = ($selectedValue==$value)?' selected':'';
			$option .= '<option value="'.$value.'"'.$selected.'>';
			$option .= ($number)?number_format($value,0,',','.'):$value;
			$option .='</option>';
      	}
		echo $option;
	}

	protected function renderArrayJSON($arrayDados)
	{
		echo json_encode($arrayDados);
	}

	protected function renderAcoes($arrayDados)
	{
		foreach ($arrayDados as $linha)
		{
			echo '<div class="card">';
			echo '<div class="card-header">';
			$this->addParams('fav', $linha['COD_PAPEL']);
			echo '<a href="'.$this->home().'">';
			$this->removeParams('fav');
			$flFavorito = $linha['Fav'];
			echo '<i class="'.(($flFavorito)?'fas':'far').' fa-heart"></i></a>';
			echo  '<a href="'.$_SERVER['PHP_SELF'].'?'.'cod_papel='.$linha['COD_PAPEL'].'"><h5 class="float-left">'.$linha['COD_PAPEL'].' - '.$linha['NOM_RES'].'</h5></a> <h4 class="float-right">'.$this->formatData('PRECO',$linha['PRECO_ULT']).'</h4></div>';

			echo '<div class="card-body">';
			echo '<table class="table table-sm text-center">';
			echo '<thead><tr><th></th>';
			$this->addParams('orderby', 'VAR_DIA');
			echo '<th><a href="'.$this->home().'">D</a></th>';
			$this->removeParams('orderby');
			$this->addParams('orderby', 'VAR_MES');
			echo '<th><a href="'.$this->home().'">M</a></th>';
			$this->removeParams('orderby');
			$this->addParams('orderby', 'VAR_ANO');
			echo '<th><a href="'.$this->home().'">A</a></th>';
			$this->removeParams('orderby');
			if($this->orderby) $this->addParams('orderby', $this->orderby);
			echo '</tr></thead>';
			echo '<tbody>';
echo '<tr><td>Ultimo</td><td>'.$this->formatData('VAR', $linha['VAR_DIA']).'</td><td>'.$this->formatData('VAR',$linha['VAR_MES']).'</td><td>'.$this->formatData('VAR',$linha['VAR_ANO']).'</td></tr><tr><td>Medio</td><td>'.$this->formatData('VAR',$linha['VAR_DIA_MED']).'</td><td>'.$this->formatData('VAR',$linha['VAR_MES_MED']).'</td><td>'.$this->formatData('VAR',$linha['VAR_ANO_MED']).'</td></tr><tr><td colspan="4">Volume: '.$this->formatData('QTD',$linha['QTD_TOTAL']).'</td></tr>';
			echo '</tbody>';
			echo '</table></div></div>';
		}

	}

	protected function renderTable($arrayDados){

	$htmlColunasTitulo = '';
	$colunas = array_keys($arrayDados[0]);
	foreach ($colunas as $coluna)
	{
		$htmlColunasTitulo .= '<th scope="col" data-column-id="'.$coluna.'">';
		$this->addParams('orderby', $coluna);
		$htmlColunasTitulo .= '<a href="'.$this->home().'">'.$coluna.'</a>';
		$this->removeParams('orderby');
		if($this->orderby) $this->addParams('orderby', $this->orderby);
		$htmlColunasTitulo .= '</th>';
	}

	$htmlLinhasBody = '';
	foreach ($arrayDados as $rs)
	{
		$htmlColunasBody = '';
		$value = '';
		for ($x=0; $x < count($rs)-1; $x++)
		{
			$coluna = array_keys($rs)[$x];
			$linha = $rs[$coluna];

			switch($coluna){
			case 'Fav':
				$this->addParams('fav', $rs['COD_PAPEL']);
				$value = '<a href="'.$this->home().'">';
				$this->removeParams('fav');
				$flFavorito = $rs['Fav'];
				$value .= '<i class="'.(($flFavorito)?'fas':'far').' fa-heart"></i></a>';
			break;

			case 'SEGMENTO':
				$this->addParams('segmento', $linha);
				$value = '<a href="'.$this->home().'">'.$linha.'</a>';
				$this->removeParams('segmento');
				if ($this->model->segmento) $this->addParams('segmento', $this->model->segmento);
			break;

			case 'COD_PAPEL':
				$value = '<a href="'.$_SERVER['PHP_SELF'].'?'.'cod_papel='.$linha.'">'.$linha.'</a>';
			break;

			default:
			$htmlColunasBody .= '<td>'.$this->formatData($coluna, $linha).'</td>';
			}
    	}
        $htmlLinhasBody .= '<tr>'.$htmlColunasBody.'</tr>';
	}


	$htmlTable  ='<table id="dados" class="table table-striped table-bordered" style="font-size: 6pt">';
	$htmlTable .='<thead><tr>'.$htmlColunasTitulo.'</tr></thead>';
	$htmlTable .='<tbody>'.$htmlLinhasBody.'</tbody>';
	$htmlTable .= '</table>';
	echo $htmlTable;
	}

	private function formatData($coluna, $linha){
		$value ='';
		if (strpos($coluna,'VAR') !== false){
			$cor = ($linha > 0) ? 'text-success':'text-danger';
			$value ='<span class="'.$cor.'">'.number_format($linha,2,',','.').'%</span>';
		} elseif (strpos($coluna,'PRECO') !== false  ){
			$value = number_format($linha,2,',','.');
		} elseif (strpos($coluna,'QTD') !== false ){
			$value = number_format($linha,0,',','.');
		} else {
			$value = $linha;
		}
		return $value;
	}

}
?>