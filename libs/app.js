angular.module("carteira",['ngRoute'])
    .config(function($routeProvider) {

        $routeProvider
            .when('/',{
                templateUrl:'app/grafico.html',
                controller: 'GraficoCtrl',
                resolve: {

                }
            })
            .when('/carteira',{
                templateUrl:'app/carteira.html',
                controller: 'CarteiraCtrl'
            })
            .when('/titulo/:titulo',{
                templateUrl:'app/titulo.html',
                controller: 'TituloCtrl'
            })
    });


function MainCtrl($scope,$http){

    $scope.showError = function(msg){
        $scope.erro = true;
        $scope.msgError = msg;
    }
}
function GraficoCtrl($scope,$http){

    $scope.graficoDivisaoCarteira = function(){

        $http.get('server/divisao_carteira.php')
            .error(function(msg){
                $scope.carregando = false;
                $scope.$parent.showError(msg);
            })
            .success(function(retorno){
               Grafico.divisaoCarteira(retorno);
            });

        $http.get('server/soma_rentabilidade.php')
            .error(function(msg){
                $scope.carregando = false;
                $scope.$parent.showError(msg);
            })
            .success(function(retorno){
               Grafico.somaRentabilidade(retorno);
            });

        $http.get('server/valor_carteira.php')
            .error(function(msg){
                $scope.$parent.showError(msg);
            })
            .success(function(retorno){
                Grafico.linha('#valorCarteira',retorno.serie,'Valor da Carteira',retorno.legenda,'Valor em Reais',null,'R$ ');
            });

        $http.get('server/rentabilidade_acumulada.php')
            .error(function(msg){
                $scope.$parent.showError(msg);
            })
            .success(function(retorno){
                Grafico.linha('#rentabilidadeAcumulada',retorno.serie,'Rentabilidade Acumulada',retorno.legenda,'Percentual','%');
            });

        $http.get('server/taxas.php')
            .error(function(msg){
                $scope.$parent.showError(msg);
            })
            .success(function(retorno){
                Grafico.linha('#taxa',retorno.serie,'Taxas dos títulos em sua carteira',retorno.legenda,'Percentual','%');
            });




    }

    $scope.graficoDivisaoCarteira();

}

function CarteiraCtrl($scope){
}

function TituloCtrl($scope,$routeParams,$http){
    $scope.titulo = $routeParams.titulo;

    $http.get('server/valor_carteira.php?titulo='+$scope.titulo)
        .error(function(msg){
            $scope.$parent.showError(msg);
        })
        .success(function(retorno){
            Grafico.linha('#valorCarteira',retorno.serie,'Valor da Carteira',retorno.legenda,'Valor em Reais',null,'R$ ');
        });

    $http.get('server/rentabilidade_acumulada.php?titulo='+$scope.titulo)
        .error(function(msg){
            $scope.$parent.showError(msg);
        })
        .success(function(retorno){
            Grafico.linha('#rentabilidadeAcumulada',retorno.serie,'Rentabilidade Acumulada',retorno.legenda,'Percentual','%');
        });

    $http.get('server/taxas.php?titulo='+$scope.titulo)
        .error(function(msg){
            $scope.$parent.showError(msg);
        })
        .success(function(retorno){
            Grafico.linha('#taxa',retorno.serie,'Taxas dos títulos em sua carteira',retorno.legenda,'Percentual','%');
        });

}

$(function () {
    Highcharts.setOptions({
        lang:{
            decimalPoint: ',',
            thousandsSep: '.'
        }
    });
});