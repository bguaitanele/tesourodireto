<?php

require_once 'carregar-arquivos-tesouro.php';

$somaAporte = [];

function pegarAporteMes($anomes){
    global $somaAporte;
    if(isset($somaAporte[$anomes])) return $somaAporte[$anomes];
    $valorRequerido = 0;
    foreach($somaAporte as $data=>$valor){
        if($data>$anomes) return $valorRequerido;
        $valorRequerido = $valor;
    }
    return $valorRequerido;
}


usort($arrCarteira,function($item1,$item2){

    $d1 = DateTime::createFromFormat('d/m/Y',$item1->dataCompra);
    $d2 = DateTime::createFromFormat('d/m/Y',$item2->dataCompra);
    if($d1->format('Ymd')==$d2->format('Ymd')) return 0;
    return $d1->format('Ymd') > $d2->format('Ymd') ? 1 : -1;

});

##########
# pega todos os valores inseridos, separando por mes
foreach($arrCarteira as $carteira){

    //data da compra do item na carteira
    $dataCompra = DateTime::createFromFormat('d/m/Y',$carteira->dataCompra);

    //abre a planilha do ano em que foi adquirido o item
    $sheet = abrirArquivo($carteira->titulo,$dataCompra->format('Y'));

    $linha = 2;
    //procura o dia em que foi comprado o item
    do{
        $dia = DateTime::createFromFormat('d/m/Y',$sheet->cell('A',++$linha));
        if(!$dia) break;
        if($dia->format('Ymd')>=$dataCompra->format('Ymd')){
            //calcula valor do aporte
            $cota = $sheet->cell('D',$linha)*$carteira->quantidade;
            if(isset($somaAporte[$dataCompra->format('Ym')]))
                $somaAporte[$dataCompra->format('Ym')]+=$cota;
            else
                $somaAporte[$dataCompra->format('Ym')]=end($somaAporte);
            break;

        }
    }while($x=true);
}


$arrCarteiraUnica = montarCarteiraUnica($arrCarteira);

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

$arrTaxa = null;
foreach($arrTaxas as $titulo => $taxa){

    if(!$arrTaxa){
        $arrTaxa = $taxa;
        continue;
    }

    foreach($taxa as $chave=>$valor){
        isset($arrTaxa[$chave])? $arrTaxa[$chave]+= $valor: $arrTaxa[$chave] = $valor;
    }
}
$arrTaxas = [];
foreach($arrTaxa as $chave => $taxa ) {
    $arrTaxas[] = round($taxa-pegarAporteMes($chave), 2);
}
$arrSaida['serie'][] = ['name'=>'Lucro','data'=>$arrTaxas];




//$somaAporteFormatada = [];
//
//foreach($legendas as $legenda){
//
//    $somaAporteFormatada[] = isset($somaAporte[$legenda])?$somaAporte[$legenda]:null;
//}
//$arrSaida['serie'][] = ['name'=>'Aporte','data'=>$somaAporteFormatada];


exit(json_encode($arrSaida));