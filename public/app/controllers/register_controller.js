app.controller('RegisterController', function($scope, $http) {
    
    $scope.countries = [];
    
    $scope.getCountries = function() {
        $http.get($scope.api_url + "get-countries").success(function(response) {
            $scope.countries = response;
            if ($scope.country_id)
            {
                var id = $scope.country_id;
                var found = false;
                for (var i = 0; i < $scope.countries.length; i++)
                {
                    if ($scope.countries[i].id === parseInt(id))
                    {
                        found = true;
                        $scope.country = $scope.countries[i];
                        $scope.getStates();
                        break;
                    }
                }
                if (!found)
                {
                    $scope.country = null;
                    $scope.states = null;
                    $scope.state = null;
                }
            }
        });
    };
    
    $scope.getStates = function() {
        $http.get($scope.api_url + "get-states?country_id=" + $scope.country.id).success(function(response) {
            $scope.states = response;
        });
    };
    
    $scope.userInit = function(home_url, country_id, state_id, phone_number) {
        $scope.api_url = home_url + '/api/v1/';
        $scope.country_id = country_id;
        $scope.state_id = state_id;
        $scope.phone_number = phone_number;
        $scope.getCountries();
    };
    
    $scope.getStates = function() {
        $('#div-loading-sm').fadeIn(500);
        $http.get($scope.api_url + "get-states?country_id=" + $scope.country.id).success(function(response) {
            $scope.states = response;
            if ($scope.state_id)
            {
                $scope.findState($scope.state_id);
            }
            $('#div-loading-sm').fadeOut(500);
        });
    };
    
    $scope.findCountry = function(id) {
        var found = false;
        for (var i = 0; i < $scope.countries.length; i++)
        {
            if ($scope.countries[i].id === parseInt(id))
            {
                found = true;
                $scope.country = $scope.countries[i];
                $scope.getStates();
                break;
            }
        }
        if (!found)
        {
            $scope.country = null;
            $scope.states = null;
            $scope.state = null;
        }
    };
    
    $scope.findState = function(id) {
        var found = false;
        for (var i = 0; i < $scope.states.length; i++)
        {
            if ($scope.states[i].id === parseInt(id))
            {
                found = true;
                $scope.state = $scope.states[i];
                break;
            }
        }
        if (!found)
        {
            $scope.state = null;
        }
    };
    
});
