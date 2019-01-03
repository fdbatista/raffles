app.controller('UsersRafflesController', function($scope, $http) {
    
    $scope.paginationConfig = {
        itemsPerPage: 10,
        currentPage: 1,
        pages: []
    };
    
    $scope.userInit = function(api_token, home_url, user_id) {
        $('#img-loading').show();
        $scope.api_token = api_token;
        $scope.api_url = home_url + '/api/v1/';
        $scope.user_id = user_id;
        $scope.updateItemsList(1);
    };
    
    $scope.currRaffleInit = function() {
        $scope.currRaffleMinTickets = $scope.currRaffle.last_number - $scope.currRaffle.first_number + 1;
    };
    
    //scope sorting variables
    $scope.sort_params = {
        'order': 'asc',
        'caret' : 'fa-caret-down'
    };
    
    //changing sorting of displayed data
    $scope.toggle_sort = function(column) {
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
        $scope.items = $scope.sortArray(column, $scope.sort_params.order);
        $scope.showGrid();
    };
    
    $scope.sortArray = function (key, order) {
        return $scope.items.sort(function(a, b) {
            var x = a[key];
            var y = b[key];
            return (order === 'asc') ? ((x < y) ? -1 : ((x > y) ? 1 : 0)) : ((x > y) ? -1 : ((x < y) ? 1 : 0));
        });
    };
    
    //show modal form
    $scope.toggle = function(modalstate, id) {
        $scope.modalstate = modalstate;
        $scope.id = id;
        switch (modalstate) {
            case 'delete':
                $('#div-delete-question').hide();
                for (var i = 0; i < $scope.items.length; i ++)
                {
                    if ($scope.items[i].id === id)
                    {
                        $scope.item = $scope.items[i];
                        $('#div-delete-question').fadeIn(1500);
                        break;
                    }
                }
                $('#deleteModal').modal('show');
                break;
            case 'raffle-details':
                $('#frmRaffleDetails').hide();
                $http.get($scope.api_url + 'get-raffle-details/?id=' + id + '&api_token=' + $scope.api_token).success(function(response)
                {
                    $scope.raffleDetails = response;
                    $('#frmRaffleDetails').fadeIn(1000);
                });
                $('#raffleDetailsModal').modal('show');
                break;
            default:
                break;
        }
    };
    
    $scope.updateItemsList = function(pageNumber) {
        $http({
            method: 'GET',
            url: $scope.api_url + "get-user-products/?api_token=" + $scope.api_token + "&user_id=" + $scope.user_id,
            data: null,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $scope.allItems = response;
            $scope.updatePagination(pageNumber);
        }).error(function(response) {
            $scope.showMessage('Error: ' + response, 'alert-danger', '#div-message');
        });
        $('#img-loading').hide();
    };
    
    $scope.updatePagination = function(currentPage) {
        var totalPages = Math.ceil($scope.allItems.length / $scope.paginationConfig.itemsPerPage);
        $scope.paginationConfig.pages = [];
        for(var i = 0; i < totalPages; i++) {
            $scope.paginationConfig.pages.push(i + 1);
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
        $scope.paginationConfig.currentPage = currentPage;
        $scope.showGrid();
    };
    
    $scope.showGrid = function()
    {
        $('#grid-content').hide();
        $('#grid-content').fadeIn(500);
    };
    
    $scope.showMessage = function(message, divClass, divId) {
        $scope.message = message;
        var divMsg = $(divId);
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
        divMsg.animate({'opacity' : 1}, 3000).animate({'opacity' : 0}, 3000, function(){
            divMsg.hide();
        });
    };
    
    //delete record
    $scope.confirmDelete = function(id) {
        $http({
            method: 'POST',
            url: $scope.api_url + 'products/delete/' + id,
            data: $.param($scope.item) + '&api_token=' + $scope.api_token,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $scope.showMessage(response, 'alert-info', '#div-message');
            $scope.updateItemsList($scope.paginationConfig.currentPage);
        }).error(function(response) {
            $scope.showMessage(response, 'alert-danger', '#div-message');
        });
    };
    
    //search
    $scope.search = function(criteria) {
        $('#img-loading').show();
        $http({
            method: 'GET',
            url: $scope.api_url + "products/search/",
            params: {criteria: criteria, api_token: $scope.api_token},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $scope.allItems = response;
            $scope.updatePagination(1);
            $('#img-loading').hide();
        }).error(function(response) {
            $scope.showMessage(response, 'alert-danger', '#div-message');
        });
    };
    
    $scope.getResponseErrors = function(response) {
        var errors = '';
        $.each(response, function(i, item)
        {
            errors += item + "<br />";
        });
        var messageDivId = '';
        if ($scope.modalstate === 'raffle')
        {
            messageDivId = '#div-message-raffle';
        }
        else
        {
            messageDivId = '#div-message-product';
        }
        $scope.showMessage($scope.rtrim(errors, '<br />'), 'alert-danger', messageDivId);
    };
    
});
