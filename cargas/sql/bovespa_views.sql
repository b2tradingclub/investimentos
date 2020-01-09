
CREATE OR REPLACE FUNCTION var_preco(atual FLOAT,  anterior FLOAT)
RETURNS DECIMAL(10,2)
RETURN round(if(COALESCE(anterior,0) = 0,0, COALESCE(atual, 0) / anterior - 1) * 100,2);

CREATE OR REPLACE VIEW vw_mais_negociadas_ultm_prgo AS
select
ch.DATA_PREGAO
,left(ch.COD_PAPEL,4) AS COD_EMPR
,ch.COD_PAPEL
,ch.NOM_RES
,ch.PRECO_FECH_ANTERIOR
,ch.PRECO_FECH_MES_ANTERIOR
,ch.PRECO_ULT
,ch.PRECO_INI_ANO
,ch.PRECO_FECH_ANTERIOR_MEDIO
,ch.PRECO_FECH_MES_ANTERIOR_MEDIO
,ch.PRECO_MED
,ch.PRECO_INI_ANO_MEDIO
,ch.QTD_TOTAL
,var_preco(ch.PRECO_ULT, ch.PRECO_FECH_ANTERIOR) AS VAR_DIA
,var_preco(ch.PRECO_ULT, ch.PRECO_FECH_MES_ANTERIOR) AS VAR_MES
,var_preco(ch.PRECO_ULT, ch.PRECO_INI_ANO) as VAR_ANO
,var_preco(ch.PRECO_MED, ch.PRECO_FECH_ANTERIOR) AS VAR_DIA_MED
,var_preco(ch.PRECO_MED, ch.PRECO_FECH_MES_ANTERIOR_MEDIO) AS VAR_MES_MED
,var_preco(ch.PRECO_MED, ch.PRECO_INI_ANO_MEDIO) as VAR_ANO_MED
 from tb_cotacao_historica  ch
where ch.DATA_PREGAO = (select ULTM_FOTO_PRGO from vw_ultimo_foto)
and ch.COD_BDI = 2;


CREATE OR REPLACE VIEW vw_mais_negociadas_ultm_prgo_setor as
select se.SETOR_ECONOMICO
,se.SUBSETOR
,se.SEGMENTO
,up.*
,if(fav.COD_PAPEL is null,0,1) AS FL_FAVORITO
from vw_mais_negociadas_ultm_prgo up
left join dim_acao_favoritos fav on up.COD_PAPEL = fav.COD_PAPEL
left join dim_setor_economico se on up.COD_EMPR = se.CODIGO
order by up.QTD_TOTAL desc;