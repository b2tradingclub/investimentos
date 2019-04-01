<?php
namespace vini\bovespa;
use vini\app\Controller;
use vini\app\Model;

class bovespa extends Controller
{
    const SQL_SEGMENTO = 'SELECT segmento, sum(1) as qtd
                                      FROM financas.vw_mais_negociadas_ultm_prgo_setor 
                                      WHERE QTD_TOTAL >= 100000
                                      AND segmento IS NOT NULL
                                      GROUP BY 1
                                      HAVING sum(1)>0
                                      ORDER BY segmento';
    const QTD_PAGINAS = 'SELECT ceil(count(*)/{{qtdPagina}}) as qtd
                                      FROM financas.vw_mais_negociadas_ultm_prgo_setor';
    const SQL_RANK =  "SELECT fl_favorito as Fav
                                        ,segmento
                                        ,cod_papel
                                        ,nom_res
                                        ,preco_ult
                                        ,var_dia
                                        ,var_mes
                                        ,var_ano
                                        ,qtd_total
                               FROM financas.vw_mais_negociadas_ultm_prgo_setor";
    const SQL_ACAO =  "SELECT  data_pregao
                                         ,preco_ult
                                         ,var_dia
                                         ,var_mes
                                         ,var_ano
                                         ,qtd_total
                               FROM financas.vw_hist_cotacao
                               WHERE cod_papel=";
    const INS_ACAO_FAV = "INSERT IGNORE INTO financas.DIM_ACAO_FAVORITOS SET COD_PAPEL=";
    const DEL_ACAO_FAV = "DELETE FROM financas.DIM_ACAO_FAVORITOS WHERE COD_PAPEL=";
    const SEL_ACAO_FAV = "SELECT COD_PAPEL FROM financas.DIM_ACAO_FAVORITOS WHERE COD_PAPEL=";
    
    protected $codPapel;
    protected $segmento;
    protected $volMin;
    protected $volMax;
    protected $pagina;
    protected $limitesVolume;
    protected $qtdPagina;
    
    public function __construct()
    {
        $this->limitesVolume = Array(   1000
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
        $view = '';
        $this->home = $_SERVER['PHP_SELF'];
        Model::Connect();
        
        $this->qtdPagina = 25;
        if (isset($_GET['qtd_pagina']))
            $this->qtdPagina = (int)$_GET['qtd_pagina'];
        
        $this->pagina = 1;        
        if (isset($_GET['pagina']))
            $this->pagina = (int)$_GET['pagina'];

        if (isset($_GET['segmento']))
            $this->segmento = $_GET['segmento'];
            
        if (isset($_GET['orderby']))
            $this->orderby = $_GET['orderby'];

        $this->volMin = 0;            
        if (isset($_GET['vol_min']) && $_GET['vol_min']>0)
            $this->volMin = $_GET['vol_min'];
        
        $this->volMax = 0;   
        if (isset($_GET['vol_max']) && $_GET['vol_max']>0)
            $this->volMax = $_GET['vol_max'];
          
        if(isset($_GET['cod_papel'])){
            $this->codPapel = $_GET['cod_papel'];
            $view = 'acao';
        }
        
        if(isset($_GET['fav'])){
            $this->models = Array('favoritos'=> new Model);
            $rs = $this->models['favoritos']->query(self::SEL_ACAO_FAV."'".$_GET['fav']."'");
            if($rs->num_rows){
                 $rs = $this->models['favoritos']->query(self::DEL_ACAO_FAV."'".$_GET['fav']."'");
            }else{
                 $rs = $this->models['favoritos']->query(self::INS_ACAO_FAV."'".$_GET['fav']."'");
            }
            unset($_GET['fav']);
        }
        
        switch($view){
            case 'acao':
                $this->models = Array('acao'=>new Model);
                $this->models['acao']->query(self::SQL_ACAO.'"'.$this->codPapel.'" ORDER BY '.$this->orderby('DATA_PREGAO'));
                $this->output .= $this->includeView('bovespa/views/acao.php');
            break;
            
            default:
                $this->models = Array('segmento'=>new Model, 'rank'=>new Model, 'qtd_pag'=>new Model);
                
                $this->models['segmento']->query(self::SQL_SEGMENTO);
                
                $where = '';
                if ($this->volMin)
                    $where .= 'QTD_TOTAL '.(($this->volMax)?'between '.$this->volMin.' AND '.$this->volMax:'>='.$this->volMin);
                if ($this->segmento){
                    if ($where != '') $where .=' AND ';
                    $where .= 'SEGMENTO = "'.$this->segmento.'"';
                }
                if($where) $where = ' WHERE '.$where;
                
                $limit = ' LIMIT '.(($this->qtdPagina*$this->pagina)-($this->qtdPagina)).', '.$this->qtdPagina;
                
                $orderby = ' ORDER BY '.$this->orderby('VAR_ANO'); 
                
                $sqlQtd = str_replace('{{qtdPagina}}', $this->qtdPagina, self::QTD_PAGINAS).$where;
                $this->models['qtd_pag']->query($sqlQtd);
                
                $sqlrank = 'SELECT * FROM ('.self::SQL_RANK.$where.$limit.') as sub_qry '.$orderby;
                $this->models['rank']->query($sqlrank);
                $this->output .= $this->includeView('bovespa/views/default.php');
        }
        
        include 'app/default.template.php';
    }
    
    protected function addParams($key, $value){
        $_GET[$key] = $value;
    }
    
    protected function removeParams($key){
        unset($_GET[$key]);
    }
    
    protected function params (){
        return parent::params();     
    }
    
    protected function orderby($default){
        return (isset($_GET['orderby'])?$_GET['orderby']:$default);
    }
}
?>