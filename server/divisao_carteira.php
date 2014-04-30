<?php

require_once 'carregar-arquivos-tesouro.php';
$arrCarteiraUnica = [];
foreach($arrCarteira as $carteira)
{
    if(isset($arrCarteiraUnica[$carteira->titulo]))
        $arrCarteiraUnica[$carteira->titulo]+=$carteira->quantidade;
    else
        $arrCarteiraUnica[$carteira->titulo]=$carteira->quantidade;
}
$arrDivisao = [];
foreach($arrCarteiraUnica as $titulo => $carteira){

    $excel = abrirArquivo($titulo,date("Y"));
    $x=2;
    do{
        if(!$excel->cell('D',++$x)) break;
        $valor = $excel->cell("D",$x);
        $dia = DateTime::createFromFormat('d/m/Y',$excel->cell('A',$x));
    }while($x=true);
    $cota = round(calcularCota($titulo,$dia,$valor),2);
    $arrDivisao[] = [$titulo,$cota];
}

exit(json_encode($arrDivisao));
