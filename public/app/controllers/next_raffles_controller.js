app.controller('NextRafflesController', function($scope, $http) {
    
    $scope.productImages = {};
    $scope.raffleNumbers = {};
    $scope.selectedNumbers = [];
    $scope.items = [];
    $scope.itemsCount = 0;
    $scope.raffle = {};
    
    $scope.paginationConfig = {
        itemsPerPage: 9,
        currentPage: 0,
        currentAvailableNumberPage: 0,
        pages: [],
        availableNumbersPages: []
    };
    
    $scope.toggle_sort = function(column, orderMode) {
        $scope.itemsOrderField = column;
        $scope.itemsOrderMode = orderMode;
        $scope.updateItemsList();
    };
    
    $scope.userInit = function(api_token, home_url, category_id, product_id) {
        $scope.raffle = {};
        $scope.api_token = api_token;
        //http://localhost:8080/raffles/app/public
        $scope.api_url = home_url + '/api/v1/';
        $scope.category_id = category_id;
        $scope.product_id = product_id;
        $scope.searchTerm = '';
        $scope.searchNumber = '';
        $scope.itemsOrderField = 'ending_date';
        $scope.itemsOrderMode = 'asc';
        $scope.raffleNumbersCount = 0;
        $scope.rangeStart = 1;
        $scope.rangeEnd = 1;
        $scope.updatePagination();
        $scope.changePage(1);
    };
    
    $scope.updateItemsList = function () {
        $('#grid-content-raffles').hide();
        $('#div-message').hide();
        $('#img-loading').show();
        var url = $scope.api_url;
        if ($scope.product_id !== '')
        {
            url += 'raffles/get-next-raffle-by-product-id/?' + 'productId=' + $scope.product_id;
        }
        else
        {
            url += 'raffles/get-next-raffles/?category_id=' + $scope.category_id + '&order_column=' + $scope.itemsOrderField + '&order_mode=' + $scope.itemsOrderMode + '&page=' + $scope.paginationConfig.currentPage + '&itemsPerPage=' + $scope.paginationConfig.itemsPerPage + '&search_term=' + $scope.searchTerm;
        }
        
        alert(url);
        
        $http({
            method: 'GET',
            url: url,
            data: null,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $scope.items = [];
            $.each(response, function(i, item)
            {
                if (i === 0)
                {
                    $scope.itemsCount = item.total;
                }
                else
                {
                    $scope.items.push(item);
                }
            });
            $scope.updatePagination();
            $scope.showGrid();
        }).error(function(response) {
            $scope.showMessage('Error: ' + response, 'alert-danger', '#div-message');
        });
    };
    
    $scope.search = function()
    {
        $scope.paginationConfig.currentPage = 1;
        $scope.product_id = '';
        $scope.updateItemsList();
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
        divMsg.hide();
        divMsg.show();
        divMsg.animate({'opacity' : 1}, 3000).animate({'opacity' : 0}, 3000);
    };
    
    $scope.updatePagination = function() {
        var totalPages = Math.ceil($scope.itemsCount / $scope.paginationConfig.itemsPerPage);
        $scope.paginationConfig.pages = [];
        for (var i = 1; i <= totalPages; i++)
        {
            $scope.paginationConfig.pages.push(i);
        }
    };
    
    $scope.updateNumbersPagination = function() {
        var totalPages = Math.ceil($scope.raffleNumbersCount / 48);
        $scope.paginationConfig.availableNumbersPages = [];
        for (var i = 1; i <= totalPages; i++)
        {
            $scope.paginationConfig.availableNumbersPages.push(i);
        }
    };
    
    $scope.changePage = function(currentPage)
    {
        var newPage = (currentPage === -1) ? $scope.paginationConfig.pages.length : currentPage;
        if ($scope.paginationConfig.currentPage !== newPage)
        {
            $scope.paginationConfig.currentPage = newPage;
            $scope.updateItemsList();
        }
    };
    
    $scope.showGrid = function () {
        $('#img-loading').hide();
        if ($scope.items.length > 0)
        {
            $('#grid-content-raffles').fadeIn(1500);
        }
        /*else
        {
            $scope.showMessage('No results found', 'alert-warning', '#div-message');
        }*/
    };
    
    $scope.changeRaffleImage = function (imagePath)
    {
        $scope.raffle.image_path = imagePath;
        $('#product-main-image').hide();
        $('#product-main-image').fadeIn(1500);
    };
    
    $scope.resetNumbersSelection = function()
    {
        $scope.selectedNumbers = [];
        $scope.selectedNumbersStr = '';
        $scope.searchRaffleAvailableNumbers($scope.paginationConfig.currentAvailableNumberPage);
    };
    
    $scope.setCurrentRaffle = function(id, dependencies) {
        for (var i = 0; i < $scope.items.length; i++)
        {
            if ($scope.items[i].id === id)
            {
                $scope.raffle = $scope.items[i];
                if (dependencies === 'images')
                {
                    $scope.getProductImages();
                }
                if (dependencies === 'numbers')
                {
                    $('#btn-checkout-tickets').show();
                    $('#btn-back-to-tickets').hide();
                    $('#btn-assign-tickets').hide();
                    $('#div-available-numbers-pagination').hide();
                    $scope.searchNumber = '';
                    $scope.selectedNumbersStr = '';
                    $scope.selectedNumbers = [];
                    $scope.rangeStart = $scope.items[i].first_number;
                    $scope.rangeEnd = $scope.items[i].last_number;
                    $scope.searchRaffleAvailableNumbers(1);
                }
                break;
            }
        }
    };
    
    $scope.getProductImages = function()
    {
        $http({
            method: 'GET',
            url: $scope.api_url + "products/get-product-images/" + $scope.raffle.product_id,
            data: null,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $scope.productImages = response;
            $scope.showGrid();
        }).error(function(response) {
            $scope.showMessage(response, 'alert-danger', '#div-message-tickets');
        });
    };
    
    $scope.searchRaffleAvailableNumbers = function(page)
    {
        $('#grid-content-tickets').hide();
        $('#img-loading-tickets').show();
        
        $scope.paginationConfig.currentAvailableNumberPage = page === -1 ? $scope.paginationConfig.availableNumbersPages[$scope.paginationConfig.availableNumbersPages.length - 1] : page;
        
        $http({
            method: 'GET',
            url: $scope.api_url + "raffles/search-raffle-available-numbers/?raffle_id=" + $scope.raffle.id + '&rangeStart=' + $scope.rangeStart + '&rangeEnd=' + $scope.rangeEnd + "&page=" + $scope.paginationConfig.currentAvailableNumberPage + "&number=" + $scope.searchNumber,
            data: null,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $scope.raffleNumbersCount = response[0].count;
            response.splice(0, 1);
            $scope.raffleNumbers = response;
            $scope.updateNumbersPagination();
            $scope.showTicketsList();
            $('#div-available-numbers-pagination').show();
        }).error(function(response) {
            $scope.showMessage(response, 'alert-danger', '#div-message-tickets');
            $scope.showTicketsList();
        });
    };
    
    $scope.showTicketsList = function ()
    {
        $('#img-loading-tickets').fadeOut(300, function(){
            $('#grid-content-tickets').fadeIn(500);
            $('#div-available-numbers-pagination').fadeIn(500);
        });
    };
    
    $scope.updateNumberSelection = function(number)
    {
        if ($('#chkbox_number_' + number).attr('checked') === 'checked')
        {
            if ($scope.selectedNumbers.indexOf(number) === -1)
                $scope.selectedNumbers.push(number);
        }
        else
        {
            $scope.selectedNumbers.splice($scope.selectedNumbers.indexOf(number), 1);
        }
        $scope.selectedNumbers.sort($scope.compareNumbers);
        $scope.selectedNumbersStr = $scope.selectedNumbers.join(', ');
        $('#div-selected-numbers').hide();
        $('#div-selected-numbers').fadeIn(500);
    };
    
    $scope.compareNumbers = function (a, b)
    {
        return a - b;
    }
    
    $scope.submitToPaypal = function()
    {
        $('#btn-assign-tickets').fadeOut(500);
        $('#btn-back-to-tickets').fadeOut(500);
        if ($scope.selectedNumbers.length > 0)
        {
            var amount = $scope.raffle.ticket_price * $scope.selectedNumbers.length;
            $http({
                method: 'POST',
                url: $scope.api_url + "raffles/register-tickets-request",
                data: "raffle_id=" + $scope.raffle.id + "&api_token=" + $scope.api_token + "&numbers=" + $scope.selectedNumbersStr + "&numbers_count=" + $scope.selectedNumbers.length,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function(response) {
                if (response !== "")
                {
                    $('#item_number').attr('value', response);
                    $('#total-amount').attr('value', amount);
                    $('#form-submit-paypal').submit();
                }
                else
                {
                    $scope.showMessage("An error has ocurred. Please, try again later", 'alert-danger', '#div-message-tickets');
                }
            }).error(function(response) {
                $scope.showMessage(response, 'alert-danger', '#div-message-tickets');
                $scope.selectedNumbers = [];
                $('#btn-assign-tickets').fadeIn(1500);
            });
        }
        else
        {
            $scope.showMessage("You must select at least one ticket number.", 'alert-warning', '#div-message-tickets');
            $('#btn-assign-tickets').fadeIn(1500);
        }
    };
    
    $scope.checkoutTickets = function()
    {
        if ($scope.selectedNumbers.length > 0)
        {
            $('#div-available-numbers-pagination').fadeOut(500);
            $('#grid-content-tickets').fadeOut(500);
            $('#btn-reset-numbers-selection').fadeOut(500);
            $('#div-search-number').fadeOut(500, function(){
                $('#div-checkout-tickets').removeClass('col-md-5');
            });
            $('#btn-checkout-tickets').fadeOut(500, function()
            {
                $('#btn-back-to-tickets').fadeIn(500);
                $('#btn-assign-tickets').fadeIn(500);
            });
        }
        else
        {
            $scope.showMessage("You must select at least one ticket number.", 'alert-warning', '#div-message-tickets');
        }
    };
    
    $scope.goBackToTickets = function()
    {
        $('#btn-back-to-tickets').fadeOut(500);
        $('#btn-assign-tickets').fadeOut(500, function(){
            $('#div-checkout-tickets').addClass('col-md-5');
            $('#btn-reset-numbers-selection').fadeIn(500);
            $('#div-available-numbers-pagination').fadeIn(500);
            $('#div-search-number').fadeIn(500);
            $('#grid-content-tickets').fadeIn(500);
            $('#btn-checkout-tickets').fadeIn(500);
        });
    };

    $scope.validateRange = function() {
        if (parseInt($scope.rangeStart) < 1)
        {
            $scope.rangeStart = 1;
        }
        if (parseInt($scope.rangeEnd) < parseInt($scope.rangeStart))
        {
            $scope.rangeEnd = $scope.rangeStart;
        }
    };
    
});
