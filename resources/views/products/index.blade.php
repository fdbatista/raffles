@extends('layouts.layout_guest')

@section('pagelevelstyles')
@parent

<link href="{{ asset('assets/plugins/datetimepicker/jquery.datetimepicker.css')}}" rel="stylesheet">

<link href="{{ asset('assets/plugins/jquery-file-upload/css/blueimp-gallery.min.css')}}" rel="stylesheet">
<link href="{{ asset('assets/plugins/jquery-file-upload/css/jquery.fileupload.css')}}" rel="stylesheet">
<link href="{{ asset('assets/plugins/jquery-file-upload/css/jquery.fileupload-ui.css')}}" rel="stylesheet">
<noscript><link href="{{ asset('assets/plugins/jquery-file-upload/css/jquery.fileupload-noscript.css')}}" rel="stylesheet"></noscript>
<noscript><link href="{{ asset('assets/plugins/jquery-file-upload/css/jquery.fileupload-ui-noscript.css')}}" rel="stylesheet"></noscript>

@stop

@section('innercontent')
<div class="container">
    <?php
        $api_token = Auth::check() ? Auth::user()->api_token : "";
    ?>
    <h2>Products</h2>
    <div ng-controller="ProductsController" ng-init="userInit('<?= $api_token ?>', '<?= url('') ?>')">
        <div class="row">
            <form ng-cloak name="frmSearch">
                <div class="form-group error">
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="searchCriteria" name="searchCriteria" placeholder="Search terms" value="@{{ criteria }} " ng-model="criteria" ng-required="true">
                    </div>
                    <div class="col-sm-3">
                        <a class="btn btn-success" id="btnSearch" name="btnSearch" ng-click="search(criteria)"><i class="fa fa-btn fa-search"></i> Search</a>
                    </div>
                </div>
            </form>
        </div>
        <br/>
        <button id="btn-add" class="btn btn-primary" ng-click="toggle('add', 0)"><span class="fa fa-arrow-circle-right"></span> Add Item</button>
        
        <div class="text-center" id="img-loading">
            <img src='{{ asset("assets/img/loading.gif") }}' />
        </div>
        <div class="row" style="margin-top: 5px">
            <div class="col-lg-5">
                <div id="div-message" class="alert fade in" style="display:none; height: auto; padding: 5px; opacity: 0; width: auto">
                    <p ng-bind-html="message | unsafe"></p>
                </div>
            </div>
            <div class="col-lg-12" id="products-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 200px;"><a href="#" ng-click="toggle_sort('name')">Name <i class="fa @{{ sort_params.caret }}"></i></a></th>
                            <th style="width: 100px;"><a href="#" ng-click="toggle_sort('category')">Category <i class="fa @{{ sort_params.caret }}"></i></a></th>
                            <th style="width: 100px;"><a href="#" ng-click="toggle_sort('condition')">Condition <i class="fa @{{ sort_params.caret }}"></i></a></th>
                            <th style="width: 300px;"><a>Product Actions</a></th>
                            <th><a>Raffle Actions</a></th>
                        </tr>
                    </thead>

                    <tbody id="grid-content" style="display: none;">
                        <tr ng-cloak ng-repeat="item in items">
                            <td ng-bind-html="item.name | unsafe"></td>
                            <td ng-bind-html="item.category | unsafe"></td>
                            <td ng-bind-html="item.condition | unsafe"></td>
                            <td>
                                <button class="btn btn-success btn-xs btn-detail" ng-click="toggle('edit', item.id)" ng-show="item.status_id !== 3"><i class="fa fa-edit"></i> Edit</button>
                                <button class="btn btn-warning btn-xs btn-detail" ng-click="toggle('imgs', item.id)" ng-show="item.status_id !== 3"><i class="fa fa-camera"></i> Images</button>
                                <button class="btn btn-danger btn-xs btn-delete" ng-click="toggle('delete', item.id)" ng-show="item.status_id !== 3"><i class="fa fa-trash-o"></i> Delete</button>
                            </td>
                            <td>
                                <button class="btn btn-info btn-xs btn-detail" ng-click="toggle('raffle-details', item.id)"><i class="fa fa-info-circle"></i> Details</button>
                                <button class="btn btn-primary btn-xs btn-detail" ng-click="toggle('raffle', item.id)" ng-show="item.status_id !== 3"><i class="fa fa-clock-o"></i> Edit</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="col-lg-10">
                    <ul class="pagination">
                        <li><a href="#" ng-click="changePage(1)">&laquo;</a></li>
                        <li ng-cloak ng-repeat="page in paginationConfig.pages"><a href="#" ng-click="changePage(page)">@{{ page }}</a></li>
                        <li><a href="#" ng-click="changePage(-1)">&raquo;</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Modal (Pop up when edit button clicked) -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myModalLabel">@{{ form_title }}</h4>
                    </div>
                    <div class="modal-body">
                        <form name="frmProduct" id="frmProduct" class="form-horizontal" novalidate="" style="display: none;">
                            <div class="col-md-12">
                                <div id="div-message-product" class="alert fade in" style="display: none; height: auto; padding: 5px; margin-top: 5px; width: auto">
                                    <p ng-bind-html="message | unsafe"></p>
                                </div>
                            </div>
                            <div class="form-group error">
                                <label for="name" class="col-sm-3 control-label">Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="@{{ item.name}} " ng-model="item.name" ng-required="true" required maxlength="75">
                                </div>
                            </div>
                            <div class="form-group error">
                                <label for="description" class="col-sm-3 control-label">Description</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="description" name="description" placeholder="Description" value="@{{ item.description}} " maxlength="500" ng-model="item.description" ng-required="true"></textarea>
                                </div>
                            </div>
                            <div class="form-group error">
                                <label for="quantity" class="col-sm-3 control-label">Quantity</label>
                                <div class="col-sm-3">
                                    <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Quantity" value="@{{ item.quantity}} " ng-model="item.quantity" ng-required="true" required min="1">
                                </div>
                            </div>
                            <div class="form-group error">
                                <label for="category_id" class="col-sm-3 control-label">Category</label>
                                <div class="col-sm-5">
                                    <select name="category_id" id="category_id" ng-model="item.category_id" class="form-control">
                                        <option ng-repeat="elem in categories" value="@{{ elem.id }}">@{{ elem.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group error">
                                <label for="condition_id" class="col-sm-3 control-label">Condition</label>
                                <div class="col-sm-5">
                                    <select name="condition_id" id="condition_id" ng-model="item.condition_id" class="form-control">
                                        <option ng-repeat="elem in productConditions" value="@{{ elem.id }}">@{{ elem.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group error">
                                <label for="contact_method_id" class="col-sm-3 control-label">Contact Method</label>
                                <div class="col-sm-5">
                                    <select name="contact_method_id" id="contact_method_id" ng-model="item.contact_method_id" class="form-control">
                                        <option ng-repeat="elem in contactMethods" value="@{{ elem.id }}">@{{ elem.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="btn-save" ng-click="save(modalstate, id)" ng-disabled="frmProduct.$invalid"><i class="fa fa-save"></i> Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close" ng-disabled="false"><i class="fa fa-times"></i> Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal (Pop up when images button clicked) -->
        <div class="modal fade" id="imgsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myModalLabel">Item Images</h4>
                    </div>
                    <br />
                    <form id="fileupload" action="{{ url('/products/upload-files/') }}" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="api_token" id="api_token" value="<?= $api_token ?>" />
                        <input type="hidden" name="product_id" id="product_id" value="@{{ id }}" />
                        
                        <!-- Redirect browsers with JavaScript disabled to the origin page -->
                        <noscript>
                            <input type="hidden" name="redirect" value="https://blueimp.github.io/jQuery-File-Upload/">
                        </noscript>
                        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                        <div class="row fileupload-buttonbar">
                            <div class="container">
                                <div class="col-lg-12">
                                    <p>Maximum filesize allowed: <code><?= $filesize ?> KB</code>.</p>
                                    <!-- The fileinput-button span is used to style the file input field as button -->
                                    <span class="btn btn-success fileinput-button">
                                        <i class="glyphicon glyphicon-plus"></i>
                                        <span>Add images...</span>
                                        <!--<input type="file" name="files[]" multiple accept="image/*" >-->
                                        <input type="file" name="files[]" multiple accept=".jpg, .png, .jpeg, .gif, .bmp">
                                    </span>
                                    <button type="submit" class="btn btn-primary start">
                                        <i class="glyphicon glyphicon-upload"></i>
                                        <span>Start upload</span>
                                    </button>
                                    <button type="reset" class="btn btn-warning cancel">
                                        <i class="glyphicon glyphicon-ban-circle"></i>
                                        <span>Cancel upload</span>
                                    </button>
                                    <button type="button" class="btn btn-danger delete">
                                        <i class="glyphicon glyphicon-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                    <input type="checkbox" class="toggle" value="Check all">
                                    <!-- The global file processing state -->
                                    <span class="fileupload-process"></span>
                                </div>
                            </div>
                            
                            <!-- The global progress state -->
                            <div class="col-lg-5 fileupload-progress fade">
                                <!-- The global progress bar -->
                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                </div>
                                <!-- The extended global progress state -->
                                <div class="progress-extended">&nbsp;</div>
                            </div>
                        </div>
                        <!-- The table listing the files available for upload/download -->
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-3">
                                    <table style="max-width: 300px;" role="presentation" class="table table-striped"><tbody style="max-width: 300px;" class="files" id="files-list"></tbody></table>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- The blueimp Gallery widget -->
                    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
                        <div class="slides"></div>
                        <h3 class="title"></h3>
                        <a class="prev">‹</a>
                        <a class="next">›</a>
                        <a class="close">×</a>
                        <a class="play-pause"></a>
                        <ol class="indicator"></ol>
                    </div>
                    <!-- The template to display files available for upload -->
                    <script id="template-upload" type="text/x-tmpl">
                    {% for (var i=0, file; file=o.files[i]; i++) { %}
                        <tr class="template-upload fade">
                            <td>
                                <span class="preview"></span>
                            </td>
                            <td>
                                <p class="name">{%=file.name%}</p>
                                <strong class="error text-danger"></strong>
                            </td>
                            <td>
                                <p class="size">Processing...</p>
                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
                            </td>
                            <td>
                                {% if (!i && !o.options.autoUpload) { %}
                                    <button class="btn btn-primary start" disabled>
                                        <i class="glyphicon glyphicon-upload"></i>
                                        <span>Start</span>
                                    </button>
                                {% } %}
                                {% if (!i) { %}
                                    <button class="btn btn-warning cancel">
                                        <i class="glyphicon glyphicon-ban-circle"></i>
                                        <span>Cancel</span>
                                    </button>
                                {% } %}
                            </td>
                        </tr>
                    {% } %}
                    </script>
                    <!-- The template to display files available for download -->
                    <script id="template-download" type="text/x-tmpl">
                    {% for (var i=0, file; file=o.files[i]; i++) { %}
                        <tr class="template-download fade">
                            <td>
                                <span class="preview">
                                    {% if (file.thumbnailUrl) { %}
                                        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img style="max-height: 100px; max-width: 100px;" src="{%=file.thumbnailUrl%}"></a>
                                    {% } %}
                                </span>
                            </td>
                            <td>
                                <p class="name">
                                    {% if (file.url) { %}
                                        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                                    {% } else { %}
                                        <span>{%=file.name%}</span>
                                    {% } %}
                                </p>
                                {% if (file.error) { %}
                                    <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                                {% } %}
                            </td>
                            <td>
                                <span class="size">{%=o.formatFileSize(file.size)%}</span>
                            </td>
                            <td>
                                {% if (file.deleteUrl) { %}
                                    <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                                        <i class="glyphicon glyphicon-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                    <input type="checkbox" name="delete" value="1" class="toggle">
                                {% } else { %}
                                    <button class="btn btn-warning cancel">
                                        <i class="glyphicon glyphicon-ban-circle"></i>
                                        <span>Cancel</span>
                                    </button>
                                {% } %}
                            </td>
                        </tr>
                    {% } %}
                    </script>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" ng-disabled="false" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i> Close</button>
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
                    <div id="div-delete-question">
                        <div class="modal-body">
                            <div>
                                Are you sure you want to delete item <span class="label label-warning">@{{ item.name }}</span>?
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" ng-disabled="false" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i> Cancel</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-delete" ng-click="confirmDelete(item.id)"><i class="fa fa-trash-o"></i> Delete</button>
                        </div>
                    </div>
                    <div id="undeletable-item">
                        <div class="modal-body">
                            <div>
                                <p><i class="fa fa-lock"></i> This item has an active raffle. Therefore, it cannot be deleted anymore.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal (Pop up when edit raffle button clicked) -->
        <div class="modal fade" id="raffleEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myModalLabel">Edit Item's Raffle</h4>
                    </div>
                    <div class="modal-body">
                        <div id="uneditable-raffle">
                            <p><i class="fa fa-lock"></i> This raffle has already begun. Therefore, it cannot be updated anymore.</p>
                        </div>
                        <div class="col-md-12">
                            <div id="div-message-raffle" class="alert fade in" style="height: 25px; padding: 5px; display: none;">
                                <p ng-bind-html="message | unsafe"></p>
                            </div>
                        </div>
                        <div id="frmRaffle">
                            <form name="frmRaffle" class="form-horizontal" novalidate="">
                                <div class="form-group error">
                                    <label for="first_number" class="col-sm-2 control-label">First number</label>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" id="first_number" name="first_number" placeholder="First number" value="@{{ currRaffle.first_number }}" ng-model="currRaffle.first_number" ng-required="true" min="1" max="99999999999">
                                    </div>
                                    <label for="last_number" class="col-sm-2 control-label">Last number</label>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" id="last_number" name="last_number" placeholder="Last number" value="@{{ currRaffle.last_number }}" ng-model="currRaffle.last_number" ng-required="true" min="@{{ currRaffle.first_number }}" max="99999999999">
                                    </div>
                                </div>
                                <div class="form-group error">
                                    <label for="ticket_price" class="col-sm-2 control-label">Tickets price</label>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" id="ticket_price" name="ticket_price" placeholder="Tickets price" value="@{{ currRaffle.ticket_price }}" ng-model="currRaffle.ticket_price" ng-required="true"min="0.1" max="9999999.99" >
                                    </div>
                                    <label for="min_start_tickets" class="col-sm-2 control-label">Minimum tickets</label>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" id="min_start_tickets" name="min_start_tickets" placeholder="Minimum sold tickets" value="@{{ currRaffle.min_start_tickets }}" ng-model="currRaffle.min_start_tickets" ng-required="true" min="1" max="@{{ currRaffleMinTickets }}" >
                                    </div>
                                </div>
                                <div class="form-group error">
                                    <label for="starting_date" class="col-sm-2 control-label">Starting date</label>
                                    <div class="col-sm-4">
                                        <input type="datetime" class="form-control datetimepicker" id="starting_date" name="starting_date" placeholder="Starting date" value="@{{ currRaffle.starting_date }}" ng-model="currRaffle.starting_date" ng-required="true">
                                    </div>
                                    <label for="ending_date" class="col-sm-2 control-label">Ending date</label>
                                    <div class="col-sm-4">
                                        <input type="datetime" class="form-control datetimepicker" id="ending_date" name="ending_date" placeholder="Ending date" value="@{{ currRaffle.ending_date }}" ng-model="currRaffle.ending_date" ng-required="true" min="@{{ currRaffle.starting_date }}">
                                    </div>
                                </div>
                            </form>
                            <button type="button" class="btn btn-warning" id="btn-cancel-raffle" ng-click="cancelRaffle(id)" ng-disabled="false"><i class="fa fa-clock-o"></i> Cancel Raffle Schedule</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="btn-save-raffle" ng-cloak ng-hide="currRaffle.editable === 0" ng-click="saveRaffle(id)" ng-disabled="frmRaffle.$invalid"><i class="fa fa-save"></i> Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close" ng-disabled="false"><i class="fa fa-times"></i> Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal (Pop up when raffle details clicked) -->
        <div class="modal fade" id="raffleDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myModalLabel">Raffle Details</h4>
                    </div>
                    <div class="modal-body">
                        <div id="frmRaffleDetails">
                            <form name="frmRaffle" class="form-horizontal" novalidate="">
                                <div class="form-group">
                                    <div class="col-sm-12" style="font-size: 14px;">
                                        <p><span style="color: peru"><i class="fa fa-book"></i></span> <span style="font-weight: bold;">Status: </span><span style="color: #006699;" ng-bind-html="raffleDetails.status | unsafe"></span></p>
                                        <p><span style="color: peru"><i class="fa fa-calculator"></i></span> <span style="font-weight: bold;">Tickets Sold/Required: </span><span style="color: #006699;">@{{ raffleDetails.sold_tickets }}/@{{ raffleDetails.min_start_tickets }}</span></p>
                                        <p><span style="color: peru"><i class="fa fa-calendar"></i></span> <span style="font-weight: bold;">Date Range: </span><span style="color: #006699;">From <code>@{{ raffleDetails.starting_date }}</code> until <code>@{{ raffleDetails.ending_date }}</code></span></p>
                                        <p ng-show="raffleDetails.winner_ticket !== null"><span style="color: peru"><i class="fa fa-star"></i></span> <span style="font-weight: bold;">Winning Ticket: </span><span style="color: #006699;">@{{ raffleDetails.winner_ticket }}</span></p>
                                        <p ng-show="raffleDetails.winner_id !== null"><span style="color: peru"><i class="fa fa-user"></i></span> <span style="font-weight: bold;">Winner: </span><a href="{{ url('/') }}/users/details/@{{ raffleDetails.winner_id }}">View Details</a></p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close" ng-disabled="false"><i class="fa fa-times"></i> Close</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

@endsection

@section('javascripts')
@parent
<script src='{{ asset("app/controllers/products_controller.js") }}'></script>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/tmpl.min.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/load-image.all.min.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/canvas-to-blob.min.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/jquery.blueimp-gallery.min.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/jquery.iframe-transport.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/jquery.fileupload.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/jquery.fileupload-process.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/jquery.fileupload-image.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/jquery.fileupload-audio.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/jquery.fileupload-video.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/jquery.fileupload-validate.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/jquery.fileupload-ui.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/main.js')}}"></script>
<script src="{{ asset('assets/plugins/datetimepicker/jquery.datetimepicker.full.min.js')}}"></script>
<script>
    $.datetimepicker.setLocale('en');
    $('.datetimepicker').datetimepicker({
            value: '',
            step: 5,
            //disabledDates: ['1986/01/08','1986/01/09','1986/01/10'],
            startDate: ''

    });
</script>
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/cors/jquery.xdr-transport.js')}}"></script>
<![endif]-->

@endsection