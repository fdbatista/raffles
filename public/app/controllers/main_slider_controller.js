app.controller('MainSliderController', function($scope, $http/*, fileUpload*/) {
    
    $scope.menu = [
        ['bold', 'italic', 'underline', 'strikethrough'],
        //['format-block'],
        ['font'],
        ['font-size'],
        /*['font-color', 'hilite-color'],
        ['remove-format'],*/
        ['ordered-list', 'unordered-list', 'outdent', 'indent'],
        ['left-justify', 'center-justify', 'right-justify'],
        ['code', 'quote', 'paragraph'],
        ['link', 'image'],
        ['css-class']
    ];

    $scope.cssClasses = ['test1', 'test2'];
    
    var divMsg = $('#div-message');
    
    $scope.userInit = function(api_token, home_url) {
        $scope.currItem = [];
        $scope.api_token = api_token;
        $scope.api_url = home_url + '/api/v1/';
        $scope.updateItemsList();
    };
    
    //show modal form
    $scope.showModal = function(action, id) {
        $scope.id = id;
        $scope.action = action;
        switch (action) {
            case 'add':
                $scope.currItem = [];
                $scope.currItem.id = 0;
                $scope.currItem.image_path = null;
                $scope.currItem.content = null;
                $scope.myFile = null;
                $scope.form_title = "Add Item";
                $('#modal-edit').modal('show');
                break;
            case 'edit':
                $scope.form_title = "Edit Item";
                for (var i = 0; i < $scope.allItems.length; i++)
                {
                    if ($scope.allItems[i].id === id)
                    {
                        $scope.currItem = $scope.allItems[i];
                        break;
                    }
                }
                $('#modal-edit').modal('show');
                break;
            case 'delete':
                $('#modal-delete').modal('show');
                break;
            default:
                break;
        }
    };
    
    $scope.updateItemsList = function() {
        $http({
            method: 'GET',
            url: $scope.api_url + "get-slider-items",
            params: {api_token: $scope.api_token},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $scope.allItems = response;
            $scope.showGrid();
        }).error(function(response) {
            $scope.showMessage('Error: ' + response, 'alert-danger');
        });
        $('#img-loading').addClass('hidden');
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
        $('#img-loading').addClass('hidden');
    };

    $scope.sendItem = function()
    {
        var fd = new FormData();
        fd.append('api_token', $scope.api_token);
        fd.append('id', $scope.currItem.id);
        fd.append('image', $scope.myFile);
        fd.append('content', $scope.currItem.content);
        var url = ($scope.id === 0) ? $scope.api_url + 'store-slider-item' : $scope.api_url + 'update-slider-item';
        
        $http.post(url, fd,
        {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(response)
        {
            if (response.indexOf('Error') === 0)
                $scope.showMessage(response, 'alert-danger');
            else
                $scope.showMessage(response, 'alert-info');
            $scope.updateItemsList();
        })
        .error(function(response)
        {
            $scope.getResponseErrors(response);
            $scope.updateItemsList();
        });
    };
    
    //delete record
    $scope.confirmDelete = function() {
        $http({
            method: 'DELETE',
            url: $scope.api_url + "delete-slider-item",
            params: {'api_token': $scope.api_token, 'id': $scope.id},
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response) {
            $scope.updateItemsList();
        }).error(function(response) {
            $scope.getResponseErrors(response);
            $scope.updateItemsList();
        });
    };
    
    $scope.getResponseErrors = function(response) {
        var errors = '';
        $.each(response, function(i, item)
        {
            errors += item + "<br />";
        });
        $scope.showMessage($scope.rtrim(errors, '<br />'), 'alert-danger');
    };

    $scope.trim = function(s)
    {
        return rtrim(ltrim(s));
    }

    $scope.ltrim = function(s)
    {
        var l=0;
        while(l < s.length && s[l] == ' ')
        {   l++; }
        return s.substring(l, s.length);
    }

    $scope.rtrim = function(s)
    {
        var r=s.length -1;
        while(r > 0 && s[r] == ' ')
        {   r-=1;   }
        return s.substring(0, r+1);
    }

});
