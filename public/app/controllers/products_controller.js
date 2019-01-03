app.controller('ProductsController', function($scope, $http) {
    
    $scope.paginationConfig = {
        itemsPerPage: 10,
        currentPage: 1,
        pages: []
    };
    
    $scope.getCategories = function() {
        $http.get($scope.api_url + "categories").success(function(response) {
            $scope.categories = response;
        });
    };
    
    $scope.getContactMethods = function() {
        $http.get($scope.api_url + "contact-methods").success(function(response) {
            $scope.contactMethods = response;
        });
    };
    
    $scope.getProductConditions = function() {
        $http.get($scope.api_url + "product-conditions").success(function(response) {
            $scope.productConditions = response;
        });
    };
    
    $scope.userInit = function(api_token, home_url) {
        $('#img-loading').show();
        $scope.api_token = api_token;
        $scope.api_url = home_url + '/api/v1/';
        $scope.getCategories();
        $scope.getProductConditions();
        $scope.getContactMethods();
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
            case 'add':
                $scope.item = null;
                $scope.form_title = "Add Item";
                $('#myModal').modal('show');
                $('#frmProduct').fadeIn(1500);
                break;
            case 'edit':
                $scope.form_title = "Edit Item";
                $http.get($scope.api_url + 'products/?id=' + id + '&api_token=' + $scope.api_token).success(function(response)
                {
                    $scope.item = response;
                    $('#frmProduct').fadeIn(1500);
                });
                $('#frmProduct').hide();
                $('#myModal').modal('show');
                break;
            case 'imgs':
                $('#files-list').html('');
                $http.get($scope.api_url + 'products?id=' + id + '&api_token=' + $scope.api_token).success(function(response)
                {
                    $scope.item = response;
                    $('#frmProduct').fadeIn(1500);
                });
                $scope.resetFileUploadPlugin(id);
                $('#imgsModal').modal('show');
                break;
            case 'delete':
                $('#div-delete-question').hide();
                $('#undeletable-item').hide();
                
                $http.get($scope.api_url + 'raffles/get-by-product-id/?product_id=' + id + '&api_token=' + $scope.api_token).success(function(response)
                {
                    $scope.currRaffle = response;
                    if ($scope.currRaffle.deletable === 0)
                    {
                        $('#undeletable-item').fadeIn(1500);
                    }
                    else
                    {
                        $http.get($scope.api_url + 'products/?id=' + id + '&api_token=' + $scope.api_token).success(function(response)
                        {
                            $scope.item = response;
                            $('#div-delete-question').fadeIn(1500);
                        });
                    }
                });
                $('#deleteModal').modal('show');
                break;
            case 'raffle':
                $('#frmRaffle').hide();
                $('#uneditable-raffle').hide();
                $('#btn-save-raffle').removeAttr('disabled');
                $scope.currRaffle = null;
                $http.get($scope.api_url + 'raffles/get-by-product-id/?product_id=' + id + '&api_token=' + $scope.api_token).success(function(response)
                {
                    $scope.currRaffle = response;
                    $scope.currRaffleInit();
                    if ($scope.currRaffle.editable === 0)
                    {
                        $('#uneditable-raffle').fadeIn(1000);
                    }
                    else
                    {
                        $('#frmRaffle').fadeIn(1000);
                    }
                });
                $('#raffleEditModal').modal('show');
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
            url: $scope.api_url + "products/my-products/" + $scope.api_token,
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
    
    /*$scope.showMessage = function(message, divClass) {
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
                divMsg.removeClass('');
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
        $('#img-loading').hide();
    };*/

    //save new record / update existing record
    $scope.save = function(modalstate, id) {
        $('#btn-save').attr('disabled', 'disabled');
        var url = $scope.api_url + "products";
        if (modalstate === 'edit'){
            url += "/" + id;
        }
        $http({
            method: 'POST',
            url: url,
            data: $.param($scope.item) + '&api_token=' + $scope.api_token,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $scope.showMessage(response, 'alert-info', '#div-message-product');
            $scope.updateItemsList($scope.paginationConfig.currentPage);
            if ($scope.modalstate === 'add')
            {
                $scope.item = null;
            }
            $('#btn-save').removeAttr('disabled');
        }).error(function(response) {
            $scope.getResponseErrors(response);
            $('#btn-save').removeAttr('disabled');
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
    
    $scope.resetFileUploadPlugin = function (id) {
        'use strict';

        // Initialize the jQuery File Upload widget:
        $('#fileupload').fileupload({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $scope.api_url + 'products/upload-files/' + id
        });

        // Enable iframe cross-domain access via redirect option:
        $('#fileupload').fileupload(
            'option',
            'redirect',
            window.location.href.replace(
                /\/[^\/]*$/,
                '/cors/result.html?%s'
            )
        );

        if (window.location.hostname === 'blueimp.github.io') {
            // Demo settings:
            $('#fileupload').fileupload('option', {
                url: '//jquery-file-upload.appspot.com/',
                // Enable image resizing, except for Android and Opera,
                // which actually support image resizing, but fail to
                // send Blob objects via XHR requests:
                disableImageResize: /Android(?!.*Chrome)|Opera/
                    .test(window.navigator.userAgent),
                maxFileSize: 999000,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
            });
            // Upload server status check for browsers with CORS support:
            if ($.support.cors) {
                $.ajax({
                    url: '//jquery-file-upload.appspot.com/',
                    type: 'HEAD'
                }).fail(function () {
                    $('<div class="alert alert-danger"/>')
                        .text('Upload server currently unavailable - ' +
                                new Date())
                        .appendTo('#fileupload');
                });
            }
        } else {
            // Load existing files:
            $('#fileupload').addClass('fileupload-processing');
            $.ajax({
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCredentials: true},
                url: $('#fileupload').fileupload('option', 'url'),
                dataType: 'json',
                context: $('#fileupload')[0]
            }).always(function () {
                $(this).removeClass('fileupload-processing');
            }).done(function (result) {
                $(this).fileupload('option', 'done')
                    .call(this, $.Event('done'), {result: result});
            });
        }
    };
    
    $scope.validateCurrRaffle = function()
    {
        return $scope.currRaffle.first_number &&
            $scope.currRaffle.last_number &&
            $scope.currRaffle.ticket_price &&
            $scope.currRaffle.min_start_tickets &&
            $scope.currRaffle.starting_date &&
            $scope.currRaffle.ending_date;
    };
    
    //save new raffle / update existing record
    $scope.saveRaffle = function(product_id) {
        if ($scope.validateCurrRaffle())
        {
            $('#btn-save-raffle').attr('disabled', 'disabled');
            var url = $scope.api_url + "raffles/store/" + product_id;
            $http({
                method: 'POST',
                url: url,
                data: $.param($scope.currRaffle) + '&api_token=' + $scope.api_token,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function(response) {
                $scope.showMessage(response, response.indexOf('Error') === -1 ? 'alert-info' : 'alert-danger', '#div-message-raffle');
                $('#btn-save-raffle').removeAttr('disabled');
            }).error(function(response) {
                $scope.getResponseErrors(response);
                $('#btn-save-raffle').removeAttr('disabled');
            });
        }
        else
        {
            $scope.showMessage('All fields in this form are required', 'alert-warning', '#div-message-raffle');
        }
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
    
    $scope.rtrim = function(s)
    {
        var r=s.length -1;
        while(r > 0 && s[r] == ' ')
        {   r-=1;   }
        return s.substring(0, r+1);
    }
    
    //cancel raffle
    $scope.cancelRaffle = function(product_id) {
        var url = $scope.api_url + "raffles/destroy/";
        $http({
            method: 'DELETE',
            url: url,
            params: {product_id: product_id, api_token: $scope.api_token},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $scope.showMessage(response, 'alert-info', '#div-message-raffle');
            $scope.currRaffle = null;
        }).error(function(response) {
            $scope.showMessage(response, 'alert-danger', '#div-message-raffle');
        });
    };
    
});
