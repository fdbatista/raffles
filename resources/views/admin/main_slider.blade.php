@extends('layouts.layout_guest')

@section('pagelevelstyles')
@parent

<link href="{{ asset('assets/css/angular-wysiwyg.css')}}" rel="stylesheet">

<style>
    .file-upload {
        position: relative;  
        overflow: hidden;  
        margin: 10px;  
    }
    
    .file-upload input.upload {
        position: absolute;
        top: 0;  
        right: 0;  
        margin: 0;  
        padding: 0;  
        font-size: 20px;  
        cursor: pointer;  
        opacity: 0;  
        filter: alpha(opacity=0);  
    }
    
    #uploadFile {
        line-height: 28px;  
    }
</style>

@stop

@section('innercontent')

@include('flash')

@if (count($errors) > 0)
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
        <p><i class="fa fa-info-circle fa-exclamation-triangle"></i> {{ $error }}</p>
        @endforeach
    </div>
@endif

<?php
    $api_token = Auth::check() ? Auth::user()->api_token : "";
?>

<div class="row" ng-controller="MainSliderController" ng-init="userInit('<?= $api_token ?>', '<?= url('') ?>')">
    <div class="col-lg-12">
        <a class="btn btn-primary" ng-click="showModal('add', 0)"><i class="fa fa-plus-square"></i> Add Item</a>
        <div id="div-message" class="alert fade in" style="display: none; height: auto; padding: 5px; margin-top: 5px; width: auto">
            <p ng-bind-html="message | unsafe"></p>
        </div>
    </div>
    <div class="col-lg-12">
        <table class="table">
            <thead>
                <tr>
                    <th><a>Image</a></th>
                    <th><a>Actions</a></th>
                </tr>
            </thead>
            <tbody id="grid-content">
                <tr ng-cloak ng-repeat="item in allItems">
                    <td><img style="height: 35px; width: auto" src="@{{ item.image_path }}" /></td>
                    <td>
                        <a class="btn btn-success" href="#" ng-click="showModal('edit', item.id)"><i class="fa fa-edit"></i> Edit</a>
                        <a class="btn btn-danger" href="#" ng-click="showModal('delete', item.id)"><i class="fa fa-remove"></i> Delete</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Modal (Pop up when add/edit button clicked) -->
    <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">@{{ form_title }}</h4>
                </div>
                <form name="frmItem" class="form-horizontal" novalidate="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <p style="display:inline;">
                                    <input value="@{{ currItem.image_path }}" id="uploadFile" disabled="disabled" placeholder="Choose Image" type="text" class="form-control">
                                </p>
                            </div>
                            <div class="col-lg-3">
                                <div class="file-upload btn btn-primary" style="top: -10px">
                                    <i class="fa fa-image"></i>
                                    <span> Browse Image</span>
                                    <input file-model="myFile" name="image_path" class="upload" id="uploadBtn" type="file" accept=".jpg, .png, .jpeg, .gif, .bmp" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <p>Content:</p>
                                <wysiwyg ng-cloak textarea-id="question" 
                                    textarea-class="form-control"  
                                    textarea-height="180px" 
                                    textarea-name="textareaQuestion"
                                    textarea-required 
                                    ng-model="currItem.content" 
                                    enable-bootstrap-title="true"
                                    textarea-menu="menu"
                                    disabled="disabled">
                                </wysiwyg>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close" ng-disabled="false"><i class="fa fa-times"></i> Cancel</button>
                        <button type="submit" class="btn btn-success" data-dismiss="modal" id="btn-save-raffle" ng-click="sendItem()" ng-disabled="frmItem.$invalid"><i class="fa fa-save"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal (Pop up when delete button clicked) -->
    <div class="modal fade" id="modal-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Delete Item</h4>
                </div>
                <div id="div-delete-question">
                    <div class="modal-body">
                        <div>
                            Are you sure you want to delete this item?
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" ng-disabled="false" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i> Cancel</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-delete" ng-click="confirmDelete()"><i class="fa fa-trash-o"></i> Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

@endsection

@section('javascripts')
@parent
<script src='{{ asset("app/controllers/main_slider_controller.js") }}'></script>
<script src="scripts/bootstrap-colorpicker-module.js"></script>
<script src="scripts/angular-wysiwyg.js"></script>

<script>
    document.getElementById("uploadBtn").onchange = function () {
        document.getElementById("uploadFile").value = this.value;
    };
</script>

@endsection