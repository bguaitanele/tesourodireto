var Grafico = {}

Grafico.divisaoCarteira = function(dados){
        $('#divisaoCarteira').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Divisão da Carteira'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>R$ {point.y}</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'Valor',
                data: dados,
                cursor: 'pointer',
                point: {
                    events:{
                        click: function(){
                            document.location.href="#titulo/"+this.name;
                        }
                    }
                }
            }]
        });
    };


Grafico.somaRentabilidade = function(dados){

    console.log(dados.serie);
    $('#somaRentabilidade').highcharts({
        title: {
            text: 'Lucro',
            x: -20 //center
        },
        xAxis: {
            categories: dados.legenda,
            labels:{
                rotation:45
            }
        },
        yAxis: {
            title: {
                text: 'Valor em Reais'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        legend:{
            enabled:false
        },
        tooltip: {
            valuePrefix: 'R$ '
        },
        series: dados.serie,
    });

//    Grafico.linha('#somaRentabilidade',dados['serie'],'Soma da rentabilidade',dados['legenda'],'Valor em Reais',null,'R$ ');
}

Grafico.linha = function(id,dados,titulo,legendas,nomeY,sufixo,prefixo){

    $(id).highcharts({
        title: {
            text: titulo?titulo:'',
            x: -20 //center
        },
        xAxis: {
            categories: legendas?legendas:'',
            labels:{
                rotation:45
            }
        },
        yAxis: {
            title: {
                text: nomeY?nomeY:''
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: sufixo?sufixo:'',
            valuePrefix: prefixo?prefixo:''
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: dados



    });
}