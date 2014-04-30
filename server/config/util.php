<?php


error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Brazil/East');
require_once 'debug.php';
require_once 'config/PHPExcel/Classes/PHPExcel.php';
carregarJson('config/carteira.json',$arrCarteira);
carregarJson('config/config.json',$config);
$urlBase = $config->urlBase;
$pastaArquivos = 'arquivos/';





#####################################################

/**
 * Carrega arquivo json dentro de variável, já transformado em objeto
 * @param $path Caminho até json
 * @param $var Variável a ser carregada. Essa variável é recebida por referência
 * @return mixed Variável
 */
function carregarJson($path, &$var){
    $var = json_decode(file_get_contents($path));
    return $var;
}

function copiarArquivoInternet($nomeArquivo){
    global $pastaArquivos,$urlBase;
    $c = curl_init();
    curl_setopt($c,CURLOPT_URL,$urlBase.$nomeArquivo);
    curl_setopt($c,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($c, CURLOPT_SSLVERSION, 3);
    curl_setopt($c,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($c, CURLOPT_PORT , 443);
    curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)');
    curl_setopt($c,CURLOPT_BINARYTRANSFER,true);
    $retorno = curl_exec($c);
    $info = curl_getinfo($c);
    if( !preg_match('/200/',$info['http_code'])) error('erro ' . $info['http_code'] . 'ao baixar ' . $nomeArquivo);
    file_put_contents($pastaArquivos.$nomeArquivo,$retorno);
}



function nomeArquivo($titulo){
    global $config;
    $titulo = current(explode(" ",$titulo));
    if(!isset($config->arquivo->$titulo)) error("Título $titulo não configurado");
    return $config->arquivo->$titulo;
}

function sheet($titulo){
    global $config;
    $nome = current(explode(" ",$titulo));
    if(!isset($config->sheet->$nome)) error("Título $nome não configurado");
    return str_replace($nome,$config->sheet->$nome,$titulo);
}

/**
 * @param $titulo
 * @param $ano
 * @return Excel
 */
function abrirArquivo($titulo,$ano){

    global $pastaArquivos;
    /**  Create a new Reader of the type defined in $inputFileType  **/
    $objReader = PHPExcel_IOFactory::createReader('Excel5');

    /**  Advise the Reader of which WorkSheets we want to load  **/
    $objReader->setLoadSheetsOnly(sheet($titulo));

    $nomeArquivo = $pastaArquivos.nomeArquivo($titulo) . '_'.$ano.'.xls';

    /**  Load $inputFileName to a PHPExcel Object  **/
    return new Excel($objReader->load($nomeArquivo));

//    return debug($objPHPExcel->getActiveSheet()->getCell('A3')->getValue());
}

function dataVencimento($titulo){
    list($x,$dataVencimento) = explode(' ' , $titulo);
    return DateTime::createFromFormat('dmy',$dataVencimento);
}

function calcularCota($titulo,$dia,$valor){

    global $arrCarteira,$config;

    $cota = 0;
    foreach($arrCarteira as $carteira){
        if($carteira->titulo==$titulo){
            $dataCompra = DateTime::createFromFormat('d/m/Y',$carteira->dataCompra);
            if($dataCompra->format('Ymd')<$dia->format('Ymd')) {
                $cota+=$carteira->quantidade;
            }
        }
    }
    return $valor*$cota;

}


function montarCarteiraUnica($arrCarteira,$titulo=null){
    $arrCarteiraUnica = [];
    //elminar titulos repetidos, deixando somente o mais antigo
    foreach($arrCarteira as $carteira){
        if($titulo && $titulo!=$carteira->titulo) continue;
        if(!array_key_exists($carteira->titulo,$arrCarteiraUnica)){
            $arrCarteiraUnica[$carteira->titulo] = $carteira;
            continue;
        }

        if(
            DateTime::createFromFormat('d/m/Y',$arrCarteiraUnica[$carteira->titulo]->dataCompra)->format('Ymd')>
            DateTime::createFromFormat('d/m/Y',$carteira->dataCompra)->format('Ymd')
        ) $arrCarteiraUnica[$carteira->titulo] = $carteira;
    }
    return $arrCarteiraUnica;
}


Class Excel{

    /**
     * @var PHPExcel
     */
    protected $phpexcel;

    public function __construct($phpexcel){
        $this->phpexcel = $phpexcel;
    }

    public function cell($coluna,$linha){
        $matriz = $coluna.$linha;
        return $this->phpexcel->getActiveSheet()->getCell($matriz)->getValue();
    }

}



function error($msg){
    http_response_code(500);
    exit($msg);
}
