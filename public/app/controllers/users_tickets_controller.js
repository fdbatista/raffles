app.controller('UsersTicketsController', function($scope, $http) {
    
    $scope.paginationConfig = {
        itemsPerPage: 10,
        currentPage: 1,
        pages: []
    };
    
    $scope.userInit = function(api_token, home_url, user_id) {
        $scope.itemsOrderField = 'id';
        $scope.itemsOrderType = false;
        $scope.itemsFilter = '';
        $scope.api_token = api_token;
        $scope.api_url = home_url + '/api/v1/';
        $scope.user_id = user_id;
        $scope.updateItemsList();
    };
    
    //scope sorting variables
    $scope.sort_params = {
        'order': 'asc',
        'caret' : 'fa-caret-down'
    };
    
    $scope.toggle = function(modalstate, id) {
        $scope.modalstate = modalstate;
        $scope.id = id;
        switch (modalstate) {
            case 'view-tickets':
                $http.get($scope.api_url + 'get-user-tickets-numbers/?raffle_id=' + id + '&api_token=' + $scope.api_token + "&user_id=" + $scope.user_id).success(function(response)
                {
                    $scope.raffleNumbers = response;
                });
                $('#ticketsModal').modal('show');
                break;
            default:
                break;
        }
    };
    
    //changing sorting of displayed data
    $scope.toggle_sort = function(column) {
        if ($scope.itemsOrderField === column)
        {
            $scope.itemsOrderType = !$scope.itemsOrderType;
        }
        else
        {
            $scope.itemsOrderType = false;
        }
        $scope.itemsOrderField = column;
        $scope.showGrid();
    };
    
    $scope.updateItemsList = function() {
        $http({
            method: 'GET',
            url: $scope.api_url + "user-tickets",
            params: {api_token: $scope.api_token, user_id: $scope.user_id},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $scope.allItems = response;
            $scope.updatePagination(1);
        }).error(function(response) {
            $scope.showMessage('Error: ' + response, 'alert-danger');
        });
        $('#img-loading').addClass('hidden');
    };
    
    $scope.updatePagination = function(currentPage) {
        var totalPages = Math.ceil($scope.allItems.length / $scope.paginationConfig.itemsPerPage);
        $scope.paginationConfig.pages = [];
        for(var i = 1; i <= totalPages; i++) {
            $scope.paginationConfig.pages.push(i);
        }
        $scope.paginationConfig.currentPage = currentPage;
        $scope.changePage(currentPage);
    };
    
    $scope.changePage = function(currentPage)
    {
        if (currentPage === -1)
            currentPage = Math.ceil($scope.allItems.length / $scope.paginationConfig.itemsPerPage);
        var startIndex = $scope.paginationConfig.itemsPerPage * currentPage - $scope.paginationConfig.itemsPerPage;
        $scope.items = $scope.allItems.slice(startIndex, startIndex + $scope.paginationConfig.itemsPerPage);
        $scope.showGrid();
    };
    
    $scope.showGrid = function()
    {
        $('#grid-content').hide();
        $('#grid-content').fadeIn(500);
    };
});
