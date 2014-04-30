<?php

require_once 'carregar-arquivos-tesouro.php';

isset($_GET['titulo']) ? $titulo = $_GET['titulo'] : exit("[]");

$ano = date('Y');
$mes = 2-2;
if($mes<=0){
    $mes = 12+$mes;
    --$ano;
}

$x=0;
while($x<3){

    $arrMeses = [];
    do{
        if($mes>12 || $x>2) break;
        $arrMeses[] = $mes.$ano;
        $x++;$mes++;
    }while(true);


    $sheet = abrirArquivo($titulo,$ano);
    $linha = 2;
    do{
        $dia = DateTime::createFromFormat('d/m/Y',$sheet->cell('A',++$linha));
        if(!in_array($dia->format('Ym'),$arrMeses)) continue;
        $valor = $sheet->cell('D',$linha);
        if(!$valor) break;
        $arrTaxas[$dia->format('Y')][$dia->format('n')][$dia->format('d')] = $valor;
    }while($x=true);

    $ano++;$mes=1;
}
exit;

foreach($arrCarteiraUnica as $carteira){

    $dataCompra = DateTime::createFromFormat('d/m/Y',$carteira->dataCompra);
    $dataVencimento  = dataVencimento($carteira->titulo);
    $anoCompra = $dataCompra->format('Y');

    $arrTaxas = [];
    for($ano = $anoCompra ; $ano <= date('Y') ; $ano++){
        if($ano>$dataVencimento->format('Y')) break;
        $sheet = abrirArquivo($carteira->titulo,$ano);
        $linha = 2;
        do{
            $dia = DateTime::createFromFormat('d/m/Y',$sheet->cell('A',++$linha));
//            if($dia && $dia<$dataCompra) continue;
            $valor = $sheet->cell('D',$linha);
            if(!$valor) break;
            $arrTaxas[$dia->format('Y')][$dia->format('n')][$dia->format('d')] = $valor;
        }while($x=true);
    }

    $arrTaxaMensal = [];

    foreach($arrTaxas as $ano => $meses ){

        foreach($meses as $numeroMes => $mes){
            $primeiro = current($mes);
            $ultimo = end($mes);
            $taxaMensal = round($ultimo*100/$primeiro-100,2);
            $arrTaxaMensal[$ano.'-'.$numeroMes] = $taxaMensal;
        }
    }

    $arrTaxasMensais[$carteira->titulo] = $arrTaxaMensal;
}

$arrSaida = [];

$legendas = [];
//organiza legenda horizontal
foreach($arrTaxasMensais as $taxa) $legendas = array_merge($legendas,array_combine(array_keys($taxa),array_keys($taxa)));

$legendaFormatada = [];
array_walk($legendas,function($item){
    global $legendaFormatada;
    $legendaFormatada[] = DateTime::createFromFormat('Y-n',$item)->format('m/Y');
});

$arrSaida['legenda'] = $legendaFormatada;


foreach($arrTaxasMensais as $titulo => $taxa){

    $data = [];
    foreach($legendas as $legenda){
        $data[] = (array_key_exists($legenda,$taxa)) ? $taxa[$legenda] : null;
    }
    $arrSaida['serie'][] = ['name'=>$titulo,'data'=>$data];
}



exit(json_encode($arrSaida));