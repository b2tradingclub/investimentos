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
    
    public function __construct()
    {
        $view = '';
        $this->home = $_SERVER['PHP_SELF'];
        
        Model::Connect();
        
        $this->volMin = 5;
        if (isset($_GET['vol_min']))
            $this->volMin = (int)$_GET['vol_min'];
            $this->params .= '&vol_min='.$this->volMin;
        $this->volMax = 10;
        if (isset($_GET['vol_max']))
            $this->volMax = (int)$_GET['vol_max'];
            $this->params .= '&vol_max='.$this->volMax;
            
        if (isset($_GET['segmento']))
            $this->segmento = $_GET['segmento'];
            $this->params .= '&segmento='.$this->segmento;
            
        if(isset($_GET['cod_papel'])){
            $this->codPapel = $_GET['cod_papel'];
           $this->params .= '&cod_papel='.$this->codPapel; 
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
        }
        
        switch($view){
            case 'acao':            
                $this->models = Array('acao'=>new Model);
                $this->models['acao']->query(self::SQL_ACAO.'"'.$this->codPapel.'" ORDER BY '.$this->orderby('DATA_PREGAO'));
                $this->output .= $this->includeView('bovespa/views/acao.php');
            break;
            
            default:
                $segmento = '';
                	if ($this->segmento) $segmento=' segmento="'.$this->segmento.'"';
                $this->models = Array('segmento'=>new Model, 'rank'=>new Model);
                $this->models['segmento']->query(self::SQL_SEGMENTO);
                $sqlrank = self::SQL_RANK.' WHERE '.(($segmento=='')?'QTD_TOTAL BETWEEN '.(500000*$this->volMin).' AND '.(500000*$this->volMax):$segmento).' ORDER BY '.$this->orderby('VAR_ANO');
                $this->models['rank']->query($sqlrank);
                $this->output .= $this->includeView('bovespa/views/default.php');
        }
        
        include 'app/default.template.php';
    }
    
    private function orderby($default){
        return (isset($_GET['orderby'])?$_GET['orderby']:$default);
    }
}
?>