<?php use vini\app\Model;?>
<DOCTYPE! html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"></meta>
    <title>Processos de Carga</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
    .thead-dark a {
        color: white;
        }
    code {
        color:black;
    }
    </style>
</head>
<body>
    <div class="container-fluid">
    <h1>Processos de carga</h1>
    <a href="<?= $_SERVER['PHP_SELF'] ?>?carga=bovespa">Carga Bovespa</a><br/>    
    <a href="<?= $_SERVER['PHP_SELF'] ?>?carga=bovespa_atualiza_precos">Atualiza Cortes de Preços Bovespa</a><br/>    
    <a href="<?= $_SERVER['PHP_SELF'] ?>?carga=investimentos">Carga Investimentos</a>
     <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">   
     <!--<input type="hidden" name="carga" value="bovespa"></input>-->
    <input type="hidden" name="MAX_FILE_SIZE" value=52428800" />
    <label for="arquivo">Arquivo(Max 15MB): </label>
    <input type="file" class="form-control" name="arquivo" id="arquivo"></input>
    
    <input type="submit" class="form-control" value="enviar"></input>
    </form>
    </div>
    <?php 
    print_r($_FILES);
    if (isset($_FILES['arquivo'])){
    if($_FILES['arquivo']['type']=='application/zip'){
            echo 'Descompactando arquivo....';
            $zip = new ZipArchive;
            $res = $zip->open($_FILES["arquivo"]["tmp_name"]);
            if ($res === TRUE) {
                $zip->extractTo('txt');
                $name = $zip->getNameIndex(0);
                if (file_exists('txt/COTAHIST.TXT')) unlink('txt/COTAHIST.TXT');
                rename('txt/'.$name, 'txt/COTAHIST.TXT');
                $zip->close();
                echo '<span style="color: green">ok</span><br><br>';
            } else {
                echo '<span style="color: red">Ocorreu um erro</span>';
            }
            
        }
}
    
    
    ?>
</body>
</html>
<?php

if ( (isset($_GET['carga']) or isset($_POST['carga'])) ){
    $script = ($_GET['carga'] ?:  $_POST['carga']);
    include_once('../app/model.php');
    echo '<p style="color: green">Conexão feita com sucesso</p>';
    $filename = __DIR__.DIRECTORY_SEPARATOR.'sql'.DIRECTORY_SEPARATOR.$script.'.sql';
    Model::exeSqlFile($filename);
}
?>