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
    </style>
</head>
<body>
    <div class="container-fluid">
    <h1>Processos de carga</h1>
    <a href="<?= $_SERVER['PHP_SELF'] ?>?carga=bovespa">Carga Bovespa</a><br/>    
    <a href="<?= $_SERVER['PHP_SELF'] ?>?carga=investimentos">Carga Investimentos</a>
    </div>
</body>
</html>
<?php
if (isset($_GET['carga'])){
    include_once('../app/model.php');
    echo '<p style="color: green">Conexão feita com sucesso</p>';
    $filename = __DIR__.DIRECTORY_SEPARATOR.'sql'.DIRECTORY_SEPARATOR.$_GET['carga'].'.sql';
    Model::exeSqlFile($filename);
}
?>