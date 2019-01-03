@extends('layouts.layout_guest')

@section('innercontent')
<?php
    $api_token = Auth::check() ? Auth::user()->api_token : "";
?>
<div class="container" ng-app="RafflesApp">
    <h2>Product Conditions</h2>
    <div ng-controller="productConditionsController" ng-init="userInit('<?= $api_token ?>', '<?= url('') ?>')">
        
        <div class="row">
            <form name="frmSearch">
                <div class="form-group error">
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="searchCriteria" name="searchCriteria" placeholder="Search terms" value="@{{ criteria }} " ng-model="criteria" ng-required="true">
                    </div>
                    <div class="col-sm-3">
                        <a class="btn btn-success" id="btnSearch" name="btnSearch" ng-click="search(criteria)"><i class="fa fa-btn fa-search"></i>Search</a>
                    </div>
                </div>
            </form>
        </div>
        <br/>
        <button id="btn-add" class="btn btn-primary" ng-click="toggle('add', 0)"><span class="fa fa-arrow-circle-right"></span> Add Item</button>
        
        <div class="text-center hidden" id="img-loading">
            <img src='{{ asset("assets/img/loading.gif") }}' />
        </div>
            
        <div class="row">
            <div class="col-lg-6">
                <div id="div-message" style="margin-top: 10px; display: none; max-height: 30px; padding: 5px;" class="alert alert-info fade in">
                    @{{ message }}
                </div>
            </div>
        </div>
        
        <!-- Table-to-load-the-data Part -->
        <table class="table">
            <thead>
                <tr>
                    <th><a href="#" ng-click="toggle_sort('id')">ID <i class="fa @{{ sort_params.caret }}"></i></a></th>
                    <th><a href="#" ng-click="toggle_sort('name')">Name <i class="fa @{{ sort_params.caret }}"></i></a></th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="grid-content" style="display: none;">
                <tr ng-repeat="productCondition in productConditions">
                    <td>@{{ productCondition.id }}</td>
                    <td>@{{ productCondition.name }}</td>
                    <td>
                        <button class="btn btn-warning btn-xs btn-detail" ng-click="toggle('edit', productCondition.id)"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn btn-danger btn-xs btn-delete" ng-click="toggle('delete', productCondition.id)"><i class="fa fa-warning"></i> Delete</button>
                    </td>
                </tr>
            </tbody>

        </table>
        
        <!-- Modal (Pop up when detail button clicked) -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myModalLabel">@{{ form_title }}</h4>
                    </div>
                    <div class="modal-body">
                        <form name="frmProductConditions" class="form-horizontal" novalidate="">
                            <div class="form-group error">
                                <label for="name" class="col-sm-3 control-label">Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="@{{ productCondition.name}} " ng-model="productCondition.name" ng-required="true">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close" ng-disabled="false"><i class="fa fa-arrow-left"></i> Cancel</button>
                        <button type="button" class="btn btn-success" data-dismiss="modal" id="btn-save" ng-click="save(modalstate, id, '<?php echo Auth::check() ? Auth::user()->api_token : ""; ?>')" ng-disabled="frmProductConditions.$invalid"><i class="fa fa-save"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal (Pop up when delete button clicked) -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myModalLabel">Delete Item</h4>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete item <span class="label label-warning">@{{ productCondition.name }}</span>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" ng-disabled="false" data-dismiss="modal" aria-label="Close"><i class="fa fa-arrow-left"></i> Cancel</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-delete" ng-click="confirmDelete(productCondition.id, '<?php echo Auth::check() ? Auth::user()->api_token : ""; ?>')"><i class="fa fa-warning"></i> Delete</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

@endsection

@section('javascripts')
@parent
<script src='{{ asset("app/controllers/product_conditions_controller.js") }}'></script>
@endsection