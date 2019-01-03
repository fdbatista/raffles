app.controller('TransactionsLogController', function($scope, $http) {
    
    var divMsg = $('#div-message');
    
    $scope.paginationConfig = {
        itemsPerPage: 10,
        currentPage: 1,
        pages: []
    };
    
    $scope.userInit = function(api_token, home_url, raffle_id) {
        $scope.itemsOrderField = 'id';
        $scope.itemsOrderType = false;
        $scope.itemsFilter = '';
        $scope.raffle_id = raffle_id;
        $scope.item = null;
        $scope.api_token = api_token;
        $scope.api_url = home_url + '/api/v1/';
        $scope.updateItemsList();
    };
    
    //scope sorting variables
    $scope.sort_params = {
        'order': 'asc',
        'caret' : 'fa-caret-down'
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
            url: $scope.api_url + "get-transactions-log/?api_token=" + $scope.api_token + "&raffle_id=" + $scope.raffle_id,
            params: null,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $scope.allItems = response;
            $scope.updatePagination(1);
            if ($scope.allItems.length === 0)
            {
                $scope.showMessage('No results found.', 'alert-warning');
            }
        }).error(function(response) {
            $scope.showMessage('Error: ' + response, 'alert-danger');
        });
        $('#img-loading').addClass('hidden');
    };
    
    $scope.toggle = function(modalstate, id) {
        $scope.modalstate = modalstate;
        $scope.id = id;
        switch (modalstate) {
            case 'view-tickets':
                for (var i = 0; i < $scope.items.length; i++)
                {
                    if ($scope.items[i].id === id)
                    {
                        $scope.raffleNumbers = $scope.items[i].tickets.split(', ');
                        break;
                    }
                }
                $('#ticketsModal').modal('show');
                break;
            case 'make-refund':
                for (var i = 0; i < $scope.items.length; i++)
                {
                    if ($scope.items[i].id === id)
                    {
                        $scope.item = $scope.items[i];
                        break;
                    }
                }
                $('#refundModal').modal('show');
                break;
            default:
                break;
        }
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

    $scope.showMessage = function(message, divClass)
    {
        $scope.message = message;
        divMsg.addClass(divClass);
        switch (divClass)
        {
            case 'alert-danger':
            {
                divMsg.removeClass('alert-info alert-warning');
                break;
            }
            case 'alert-warning':
            {
                divMsg.removeClass('alert-info alert-danger');
                break;
            }
            default:
                divMsg.removeClass('alert-danger alert-warning');
                break;
        }
        divMsg.show();
        divMsg.animate({'opacity' : 1}, 3000).animate({'opacity' : 0}, 3000, 'swing', function(){
            $(this).hide();
        });
        $('#img-loading').addClass('hidden');
    };
    
    $scope.confirmRefund = function()
    {
        $http({
            method: 'POST',
            url: $scope.api_url + "confirm-refund",
            data: "transaction_id=" + $scope.item.id + "&api_token=" + $scope.api_token,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $scope.updateItemsList();
            $scope.showMessage(response, response.indexOf('Error') === -1 ? 'alert-info' : 'alert-danger', '#div-message');
        }).error(function(response) {
            $scope.showMessage(response, 'alert-danger', '#div-message');
        });
    };
    
});
