app.controller('categoriesController', function($scope, $http) {
    
    var divMsg = $('#div-message');
    
    $scope.userInit = function(api_token, home_url) {
        $scope.api_token = api_token;
        $scope.api_url = home_url + '/api/v1/';
        $scope.getCategories();
    };
    
    $scope.getCategories = function() {
        $http.get($scope.api_url + "categories").success(function(response) {
            $scope.categories = response;
            $('#grid-content').fadeIn(500);
        });
    };
    
    //scope sorting variables
    $scope.sort_params = {
        'order': 'asc',
        'caret' : 'fa-caret-down'
    };
    
    //changing sorting of displayed data
    $scope.toggle_sort = function(column) {
        $('#grid-content').hide();
        if ($scope.sort_params.order === 'asc')
        {
            $scope.sort_params.order = 'desc';
            $scope.sort_params.caret = 'fa-caret-down';
        }
        else
        {
            $scope.sort_params.order = 'asc';
            $scope.sort_params.caret = 'fa-caret-up';
        }
        $scope.categories = $scope.sortArray(column, $scope.sort_params.order);
        $('#grid-content').fadeIn(500);
    }
    
    $scope.sortArray = function (key, order) {
        return $scope.categories.sort(function(a, b) {
            var x = a[key];
            var y = b[key];
            return (order === 'asc') ? ((x < y) ? -1 : ((x > y) ? 1 : 0)) : ((x > y) ? -1 : ((x < y) ? 1 : 0));
        });
    }
    
    //show modal form
    $scope.toggle = function(modalstate, id) {
        $scope.modalstate = modalstate;
        
        switch (modalstate) {
            case 'add':
                $scope.form_title = "Add Category";
                $scope.category = null;
                $('#myModal').modal('show');
                break;
            case 'edit':
                $scope.form_title = "Edit Category";
                $scope.id = id;
                $http.get($scope.api_url + 'categories/' + id).success(function(response)
                {
                    $scope.category = response;
                });
                $('#myModal').modal('show');
                break;
            case 'delete':
                $scope.id = id;
                $http.get($scope.api_url + 'categories/' + id).success(function(response)
                {
                    $scope.category = response;
                });
                $('#deleteModal').modal('show');
                break;
            default:
                break;
        }
    }

    //save new record / update existing record
    $scope.save = function(modalstate, id, api_token) {
        var url = $scope.api_url + "categories";
        if (modalstate === 'edit'){
            url += '/' + id;
        }
        $http({
            method: 'POST',
            url: url,
            data: $.param($scope.category) + '&api_token=' + api_token,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $http.get($scope.api_url + "categories").success(function(result)
            {
                $scope.categories = result;
            });
            $scope.message = response;
            divMsg.show();
            divMsg.fadeOut(5000);
            $('#grid-content').hide();
            $('#grid-content').fadeIn(500);
        }).error(function(response) {
            $scope.message = response;
            divMsg.show();
            divMsg.fadeOut(5000);
        });
    }
    
    //delete record
    $scope.confirmDelete = function(id, api_token) {
        $http({
            method: 'POST',
            url: $scope.api_url + 'categories/delete/' + id,
            data: 'api_token=' + api_token,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $http.get($scope.api_url + "categories").success(function(response)
            {
                $scope.categories = response;
            });
            $scope.message = response;
            divMsg.show();
            divMsg.fadeOut(5000);
            $('#grid-content').hide();
            $('#grid-content').fadeIn(500);
        }).error(function(response) {
            $scope.message = response;
            divMsg.show();
            divMsg.fadeOut(5000);
        });
    };
    
    //search
    $scope.search = function(criteria) {
        $('#img-loading').removeClass('hidden');
        var url = $scope.api_url + "categories/search/" + criteria;
        
        $http({
            method: 'GET',
            url: url,
            data: null,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $scope.categories = response;
            $('#grid-content').hide();
            $('#grid-content').fadeIn(500);
            $('#img-loading').addClass('hidden');
        }).error(function(response) {
            $scope.message = response;
            divMsg.show();
            divMsg.fadeOut(5000);
        });
    }
    
});
