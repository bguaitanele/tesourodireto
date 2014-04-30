<?php

require_once 'carregar-arquivos-tesouro.php';

$titulo = isset($_GET['titulo']) ? $_GET['titulo'] : null;
$arrCarteiraUnica = montarCarteiraUnica($arrCarteira,$titulo);

$arrTaxas = [];
foreach($arrCarteiraUnica as $carteira){

    $dataCompra = DateTime::createFromFormat('d/m/Y',$carteira->dataCompra);
    $dataVencimento  = dataVencimento($carteira->titulo);
    $anoCompra = $dataCompra->format('Y');

    for($ano = $anoCompra ; $ano <= date('Y') ; $ano++){

        //se o ano for maior que o da data de vencimento do titulo nao processa ano
        if($ano>$dataVencimento->format('Y')) break;
        $sheet = abrirArquivo($carteira->titulo,$ano);
        $linha = 2;
        do{
            $dia = DateTime::createFromFormat('d/m/Y',$sheet->cell('A',++$linha));
            if(!$dia) break;

            //se o registro for de um dia inferior a compra, nao processa o dia
            if($dia->format('Ymd')<$dataCompra->format('Ymd')) continue;
            $valor = $sheet->cell('D',$linha);
            if(!$valor) break;
            $cota = calcularCota($carteira->titulo,$dia,$valor);
            $arrTaxas[$carteira->titulo][$dia->format('Ym')] =  round($cota,2);

        }while($x=true);
    }
}

$arrSaida = [];

$legendas = [];
//organiza legenda horizontal
foreach($arrTaxas as $taxa) {
    $legendas = $legendas+array_keys($taxa);
}

$legendaFormatada = [];
array_walk($legendas,function($item){
    global $legendaFormatada;
    $legendaFormatada[] = DateTime::createFromFormat('Ym',$item)->format('m/Y');
});

$arrSaida['legenda'] = $legendaFormatada;

foreach($arrTaxas as $titulo => $taxa){

    $data = [];
    foreach($legendas as $legenda){
        $data[] = (array_key_exists($legenda,$taxa)) ? $taxa[$legenda] : null;
    }
    $arrSaida['serie'][] = ['name'=>$titulo,'data'=>$data];
}



exit(json_encode($arrSaida));