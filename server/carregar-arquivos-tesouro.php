<?php

require "config/util.php";

foreach($arrCarteira as $carteira){

    //carrega arquivos do tesouro
    carregarArquivos($carteira->titulo,$carteira->dataCompra);


}

function carregarArquivos($titulo,$dataCompra){

    global $pastaArquivos;
    $dataCompra = DateTime::createFromFormat('d/m/Y' , $dataCompra);
    $anoAtual = date('Y');
    for($ano = $dataCompra->format('Y'); $ano <= $anoAtual ; $ano++){
        $nomeArquivo = nomeArquivo($titulo) . '_'.$ano.'.xls';
        $arquivo = $pastaArquivos.$nomeArquivo;
        if(!file_exists($arquivo)){
            copiarArquivoInternet($nomeArquivo);
            continue;
        }
        elseif(date('Ymd',filemtime($pastaArquivos.$nomeArquivo)) != date('Ymd')){
            unlink($pastaArquivos.$nomeArquivo);
            copiarArquivoInternet($nomeArquivo);
            continue;
        }
    }
}


//
//
//try {
//
//    /**  Create a new Reader of the type defined in $inputFileType  **/
//    $objReader = PHPExcel_IOFactory::createReader('Excel5');
//
//    /**  Advise the Reader of which WorkSheets we want to load  **/
//    $objReader->setLoadSheetsOnly('LFT 070317');
//
//    /**  Load $inputFileName to a PHPExcel Object  **/
//    $objPHPExcel = $objReader->load('LFT_2014.xls');
//
//
//    debug($objPHPExcel->getActiveSheet()->getCell('A3')->getValue());
//
//
//
//} catch (\Exception $e) {
//    var_dump($e->getMessage());
//}