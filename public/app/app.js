var app = angular.module('RafflesApp', ['ngRoute', 'ngResource', 'ngSanitize', 'colorpicker.module', 'wysiwyg.module', 'ui.mask', 'ui.select'])
        //.constant('API_URL', 'http://localhost:8080/raffles/app/public/api/v1/')
        .filter('unsafe', function($sce) { return $sce.trustAsHtml; })
        .directive('countdown', function () {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    var clock = element.FlipClock({
                        clockFace: 'DailyCounter',
                        autoStart: false,
                        callbacks: {
                            stop: function() {
                                var divRaffle = $('#' + attrs.id.replace('countdown', 'div'));
                                divRaffle.animate({'opacity' : 0}, 3000, 'swing', function(){
                                    divRaffle.hide();
                                });
                            }
                        }
		    });
		    clock.setTime(parseInt(attrs.seconds));
		    clock.setCountdown(true);
		    clock.start();
                }
            };
        })
        
        /*.service('fileUpload', ['$http', function ($http) {
            this.uploadFileToUrl = function(api_token, file, content, uploadUrl){
               var fd = new FormData();
               fd.append('api_token', api_token);
               fd.append('file', file);
               fd.append('content', content);
            
               $http.post(uploadUrl, fd, {
                  transformRequest: angular.identity,
                  headers: {'Content-Type': undefined}
               })
            
               .success(function(){
               })
            
               .error(function(){
               });
            }
         }])*/
        .factory("ProductsFactory", function($http) {
            var interface = {
                name: 'ProductsFactory',
                getMyProducts: function(url){
                    $http({
                        method: 'GET',
                        url: url,
                        data: null,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    }).success(function(response) {
                        return {
                            code : 200,
                            response : response
                        };
                    }).error(function(response, status) {
                        return {
                            code : status,
                            response : response
                        };
                    });
                },
                getAllProducts: function(API_URL) {
                    $http.get(API_URL + "products").success(function(response) {
                        return response;
                    });
                }
            };
            
            return {
                name: interface.name,
                getMyProducts: interface.getMyProducts
            };
            
            }
        )
;
