@extends('layouts.layout_guest')

@section('pagelevelstyles')
@parent

@stop

@section('innercontent')

<div class="container">
    <?php
        $api_token = Auth::check() ? Auth::user()->api_token : "";
    ?>
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-lg-12">
            <a class="breadcrumb" href="<?= url('/users') ?>">Users</a> /
            <a class="breadcrumb"><?= $user->username ?>'s raffles</a>
        </div>
    </div>
    
    <h4>Active Raffles for User <a href="<?= url("/users/details/$user->id") ?>"><?= $user->username ?></a></h4>
    <div ng-controller="UsersRafflesController" ng-init="userInit('<?= $api_token ?>', '<?= url('') ?>', '<?= $user->id ?>')">
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
                            <th style="width: 200px;"><a href="#" ng-click="toggle_sort('name')">Product <i class="fa @{{ sort_params.caret }}"></i></a></th>
                            <th style="width: 100px;"><a href="#" ng-click="toggle_sort('category')">Category <i class="fa @{{ sort_params.caret }}"></i></a></th>
                            <th style="width: 100px;"><a href="#" ng-click="toggle_sort('condition')">Condition <i class="fa @{{ sort_params.caret }}"></i></a></th>
                            <th><a>Actions</a></th>
                        </tr>
                    </thead>

                    <tbody id="grid-content" style="display: none;">
                        <tr ng-cloak ng-repeat="item in items">
                            <td ng-bind-html="item.name | unsafe"></td>
                            <td ng-bind-html="item.category | unsafe"></td>
                            <td ng-bind-html="item.condition | unsafe"></td>
                            <td>
                                <button class="btn btn-info btn-detail" ng-click="toggle('raffle-details', item.id)"><i class="fa fa-info-circle"></i> Details</button>
                                <button class="btn btn-danger btn-delete" ng-click="toggle('delete', item.id)" ng-show="item.status_id !== 3"><i class="fa fa-trash-o"></i> Delete</button>
                                <a ng-show="item.status_id !=== -1" href="<?= url('/transactions/') ?>/@{{ item.id }}" class="btn btn-success btn-detail"><i class="fa fa-paypal"></i> Transactions</a>
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
                                        <p><span style="color: peru"><i class="fa fa-book"></i></span> <span style="font-weight: bold;">Description: </span><span style="color: #006699;" ng-bind-html="raffleDetails.product_desc | unsafe"></span></p>
                                        <p><span style="color: peru"><i class="fa fa-exclamation-circle"></i></span> <span style="font-weight: bold;">Status: </span><span style="color: #006699;" ng-bind-html="raffleDetails.status | unsafe"></span></p>
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
                            <button type="button" class="btn btn-default" ng-disabled="false" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i> No</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-delete" ng-click="confirmDelete(item.id)"><i class="fa fa-trash-o"></i> Yes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

@endsection

@section('javascripts')
@parent
    <script src='{{ asset("app/controllers/users_raffles_controller.js") }}'></script>
@endsection