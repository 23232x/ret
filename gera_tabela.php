<?php

$_UP['pasta'] = 'upload/';

$_UP['tamanho'] = 1024 * 1024 * 2; // 2Mb

$_UP['extensoes'] = array('ret', 'txt');

$_UP['renomeia'] = false;




$_UP['erros'][0] = 'Não houve erro';
$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
$_UP['erros'][4] = 'Não foi feito o upload do arquivo';

if ($_FILES['arquivo']['error'] != 0) {
    die("Não foi possível fazer o upload, erro:" . $_UP['erros'][$_FILES['arquivo']['error']]);
    exit;
}

$extensao = strtolower(end(explode('.', $_FILES['arquivo']['name'])));
if (array_search($extensao, $_UP['extensoes']) === false) {
    echo "Por favor, envie arquivos com as seguintes extensões: ret";
    exit;   
}


if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
    echo "O arquivo enviado é muito grande, envie arquivos de até 2Mb.";
    exit;
}

if ($_UP['renomeia'] == true) {
    
} else {

    $nome_final = $_FILES['arquivo']['name'];
}


if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {
//
    $arquivo = 'planilha.xlsx';
//
    $html = '';
    $html .= '<table>';
    $html .= '<tr>';
    $html .= '<td colspan="8">EMPR&Eacute;STIMO BANCO DO BRASIL</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<td><b>NOME</b></td>';
    $html .= '<td><b>ID</b></td>';
    $html .= '<td><b>CPF</b></td>';
    $html .= '<td><b>CONTRATO</b></td>';
    $html .= '<td><b>DATA DE VENC.</b></td>';
    $html .= '<td><b>PRE.</b></td>';
    $html .= '<td><b>PRA.</b></td>';
    $html .= '<td><b>VALOR PREST.</b></td>';
    $html .= '</tr>';

    $ponteiro = fopen("upload/$nome_final", "r");
    $cont = 2;
    $total = 0;
    $flag = TRUE;
    while (!feof($ponteiro)) {
        $linha = fgets($ponteiro, 4096);
        $ini = strpos($linha, "H0");
        if ($ini) {
            $html .= '<tr>';
            $nome = substr($linha, 15, 36);
            $cpf = substr($linha, 51, 11);
            $id = substr($linha, 62, 11);
            $contr = substr($linha, 161, 9);
            $data = substr($linha, 97, 8);
            $data = substr($data, 0, 2) . "/" . substr($data, 2, 2) . "/" . substr($data, 4, 4);
            $pre = substr($linha, 105, 2);
            $pra = substr($linha, 107, 2);
            $valor = substr($linha, 144, 8);
            $total += $valor;
            $valor = substr($valor, 0, 6) . "," . substr($valor, 6, 2);

            $html .= "<td>" . $nome . "</td>";
            $html .= "<td>" . $id . "</td>";
            $html .= "<td>" . $cpf . "</td>";
            $html .= "<td>" . $contr . "</td>";
            $html .= "<td>" . $data . "</td>";
            $html .= "<td>" . $pre . "</td>";
            $html .= "<td>" . $pra . "</td>";
            $html .= "<td>" . $valor . "</td>";
            $html .= '</tr>';
            $cont++;
        } else if ($flag && $cont > 4) {
            $valor_arquivo = intval(substr($linha, 26, 15));
            $flag = FALSE;
        }
    }
    fclose($ponteiro);
    unlink($_UP['pasta'] . $nome_final);
} else {
    echo "Não foi possível enviar o arquivo, tente novamente";    
}

$html .= '<tr>';
$html .= '<td colspan="5">TOTAL</td>';
$html .= '<td></td>';
$html .= '<td></td>';
$html .= "<td>=SOMA(H3:H$cont)</td>";
$html .= '</tr>';

if ($valor_arquivo == $total) {
    $html .= '<tr bgcolor="BLUE">';
    $html .= '<td color="red" colspan="5"></td>';
    $html .= '<td></td>';
    $html .= '<td>NAO HOUVE DIVERGENCIAS</td>';
    $html .= "<td></td>";
    $html .= '</tr>';
}else{
    $html .= '<tr bgcolor="RED">';
    $html .= '<td color="red" colspan="5"></td>';
    $html .= '<td></td>';
    $html .= '<td>HOUVE DIVERGENCIAS</td>';
    $html .= "<td></td>";
    $html .= '</tr>';
}
//Configurações header para forçar o download
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/x-msexcel");
header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
header("Content-Description: PHP Generated Data");

// Envia o conteúdo do arquivo
echo $html;
exit;
