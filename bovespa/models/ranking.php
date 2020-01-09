<?php
namespace vini\bovespa\models;
use vini\app\Model;

class Ranking extends Model
{
    public $segmento;
    public $volMin;
    public $volMax;
    public $pagina;
    public $itens;
	public $paginas;
	private $_segmentos;
	private $_colunas;
	private $_acoes;

    const SQL_SEGMENTO = 'SELECT segmento, sum(1) as qtd
                                      FROM financas.vw_mais_negociadas_ultm_prgo_setor
                                      WHERE QTD_TOTAL >= 100000
                                      AND segmento IS NOT NULL
                                      GROUP BY 1
                                      HAVING sum(1)>0
                                      ORDER BY segmento';
    const QTD_PAGINAS = 'SELECT ceil(count(*)/{{itens}}) as qtd
                                      FROM financas.vw_mais_negociadas_ultm_prgo_setor';
    const SQL_RANK =  "SELECT fl_favorito as Fav
                                        ,segmento
                                        ,cod_papel
                                        ,nom_res
                                        ,preco_ult
                                        ,var_dia
                                        ,var_mes
                                        ,var_ano
                                        ,var_dia_med
                                        ,var_mes_med
                                        ,var_ano_med
                                        ,qtd_total
                               FROM financas.vw_mais_negociadas_ultm_prgo_setor";
    const SQL_ACAO =  "SELECT  data_pregao
                                         ,preco_ult
                                        , preco_med
                                         ,var_dia
                                         ,var_mes
                                         ,var_ano
                                         ,qtd_total
                               FROM financas.vw_hist_cotacao
                               WHERE cod_papel=";
    const INS_ACAO_FAV = "INSERT IGNORE INTO financas.DIM_ACAO_FAVORITOS SET COD_PAPEL=";
    const DEL_ACAO_FAV = "DELETE FROM financas.DIM_ACAO_FAVORITOS WHERE COD_PAPEL=";
    const SEL_ACAO_FAV = "SELECT COD_PAPEL FROM financas.DIM_ACAO_FAVORITOS WHERE COD_PAPEL=";


    public function __construct()
    {
		parent::__construct();
        $this->volMin   = 0;
        $this->volMax   = 0;
        $this->itens    = 25;
		$this->pagina   = 1;
		$this->segmento = '';
		$this->_segmentos = array();
		$this->_acoes = array();

		$this->updateFavorito();
		$this->carregaSegmentos();

		$where = $this->getWhere();
		$limit = $this->getLimit($where);
		$orderby = $this->getOrderBy();
		$this->carregaRankingAcoes($where, $limit, $orderby);
	}

	public function limitesVolume(){

        return Array(1000
                   , 100000
                   , 500000
                   , 1000000
                   , 2000000
                   , 3000000
                   , 4000000
                   , 5000000
                   , 10000000
                   , 20000000
                   , 30000000
                   , 40000000
                   , 50000000
                   , 60000000
                   , 70000000
                   , 80000000
                   , 90000000
                   , 100000000);
    }

	public function segmentos()
	{
		return $this->_segmentos;
	}

	public function acoes()
	{
		return $this->_acoes;
	}

	private function getWhere()
	{

        if (isset($_GET['vol_min']) && $_GET['vol_min']>0)
            $this->volMin = $_GET['vol_min'];

        if (isset($_GET['vol_max']) && $_GET['vol_max']>0)
            $this->volMax = $_GET['vol_max'];

		$qtdFilter = '';
        if ($this->volMin)
		{
			if ($this->volMax)
			{
				$qtd .= 'QTD_TOTAL between '.$this->volMin.' AND '.$this->volMax;
			} else
			{
				$qtd .= 'QTD_TOTAL >='.$this->volMin;
			}
		}

        if (isset($_GET['segmento']))
            $this->segmento = $_GET['segmento'];

		$segmentoFilter ='';
		if ($this->segmento){
           $segmentoFilter = 'SEGMENTO = "'.$segmento.'"';
           }

		$where = array();
		if($qtdFilter) array_push($where, $qtdFilter);
		if($segmentoFilter) array_push($where, $segmentoFilter);
        if (count($where)) return 'WHERE '.implode(' AND ',$where);
		return '';
	}

	private function getLimit($where)
	{
        if (isset($_GET['qtd_pagina']))
            $this->itens = (int)$_GET['qtd_pagina'];
			$this->sql = str_replace('{{itens}}', $this->itens, self::QTD_PAGINAS).$where;
            $this->query();
          	$this->fetchRow();
            $this->paginas = (int)$this->linhas[0];

        if (isset($_GET['pagina']))
            $this->pagina = (int)$_GET['pagina'];

			$limit = ' LIMIT '.(($this->itens*$this->pagina)-($this->itens)).', '.$this->itens;

		return $limit;
	}

	private function getOrderBy()
	{
		return ' ORDER BY '.$this->orderby('VAR_ANO');
	}

	private function updateFavorito()
	{
		if(isset($_GET['fav'])){
			$this->sql = self::SEL_ACAO_FAV."'".$_GET['fav']."'";
            $rs = $this->query();
            if($rs->num_rows){
				$this->sql = self::DEL_ACAO_FAV."'".$_GET['fav']."'";
                $this->query();
            }else{
				$this->sql = self::INS_ACAO_FAV."'".$_GET['fav']."'";
                $this->query();
            }
            unset($_GET['fav']);
        }
	}

	private function carregaSegmentos()
	{
		$this->sql = self::SQL_SEGMENTO;

        $this->query();
		while($this->fetchRow())
		{
			array_push($this->_segmentos, $this->linhas[0]);
		}
	}

	private function carregaRankingAcoes($where, $limit, $orderby)
	{
        $this->sql = 'SELECT * FROM ('.self::SQL_RANK.$where.$limit.') as sub_qry '.$orderby;
        $this->query();
		while($this->fetchRow(true))
		{
			array_push($this->_acoes, $this->linhas);
		}
	}
}