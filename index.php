<style>
    .btn_enviar{
        background-color: #57c029;
        display: block;
        margin-top: 20px;
        width: 200px;
        height: 45px;
        border: 0;
        border-radius: 5px;
    }
</style>
<form method="post" action="gera_tabela.php" enctype="multipart/form-data">
    <input type="file" name="arquivo">  
    <input class="btn_enviar" value="Carregar..." name="retorno" type="submit">
</form>

<?php