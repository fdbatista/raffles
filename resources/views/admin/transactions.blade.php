@extends('layouts.layout_guest')

@section('pagelevelstyles')
@parent
<link href="{{ asset('assets/plugins/bootstrap-checkbox/css/build.css')}}" rel="stylesheet">
@stop

@section('innercontent')
<div class="container">
    <?php
        $api_token = Auth::check() ? Auth::user()->api_token : "";
    ?>
    
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-lg-12">
            <a class="breadcrumb" href="<?= url('/users') ?>">Users</a> / 
            <a class="breadcrumb" href="<?= url("/users/raffles/$user->id") ?>"><?= $user->username ?>'s raffles</a> /
            <a class="breadcrumb">Transactions for <?= $raffle_id ?></a>
        </div>
    </div>
    
    <h2>Transactions for raffle #<?= $raffle_id ?></h2>
    <div ng-controller="TransactionsLogController" ng-init="userInit('<?= $api_token ?>', '<?= url('') ?>', '<?= $raffle_id ?>')">
        <div class="row" style="margin-top: 5px">
            <div class="col-lg-3">
                <input type="text" value="@{{ itemsFilter }}" placeholder="Search term" ng-model="itemsFilter" ng-change="showGrid()" class="form-control" />
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10">
                <div id="div-message" class="alert fade in" style="display: none; height: auto; padding: 5px; margin-top: 5px;">
                    <p ng-bind-html="message | unsafe"></p>
                </div>
            </div>
        </div>
        <div class="row">
            
            <div class="text-center" id="img-loading">
                <img src='{{ asset("assets/img/loading.gif") }}' />
            </div>
            
            <div class="col-lg-12" id="products-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 30px;"><a href="#" ng-click="toggle_sort('tickets_count')">Tickets</a></th>
                            <th style="width: 50px;"><a href="#" ng-click="toggle_sort('paypal_transaction_id')">ID</a></th>
                            <th style="width: 25px;"><a href="#" ng-click="toggle_sort('user_name')">User</a></th>
                            <th style="width: 50px;"><a href="#" ng-click="toggle_sort('created_at')">Date</a></th>
                            <th style="width: 100px;"><a href="#" ng-click="toggle_sort('transaction_description')">Description</a></th>
                            <th style="width: 100px;"><a href="#" ng-click="toggle_sort('transaction_status')">Status</a></th>
                            <th style=""><a href="#" ng-click="toggle_sort('amount_to_refund')">Refund</a></th>
                        </tr>
                    </thead>

                    <tbody id="grid-content" style="display: none;">
                        <tr ng-repeat="item in items | filter:itemsFilter | orderBy:itemsOrderField:itemsOrderType">
                            <td><a class="btn btn-success" style="border-radius: 10px!important;" ng-click="toggle('view-tickets', item.id)"> @{{ item.tickets_count }}</a></td>
                            <td>@{{ item.paypal_transaction_id }}</td>
                            <td>@{{ item.user_name }}</td>
                            <td>@{{ item.created_at }}</td>
                            <td ng-bind-html="item.transaction_description | unsafe"></td>
                            <td>@{{ item.transaction_status }}</td>
                            <td>
                                <a href="#" ng-show="item.transaction_status_id !== 4" ng-click="toggle('make-refund', item.id)" class="btn btn-primary"><i class="fa fa-usd"></i> @{{ item.amount_to_refund }}</a>
                                <a ng-show="item.transaction_status_id === 4"><i class="fa fa-check-circle"></i> @{{ item.amount_to_refund }}</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="col-lg-10">
                    <ul class="pagination">
                        <li><a href="#" ng-click="changePage(1)">«</a></li>
                        <li ng-repeat="page in paginationConfig.pages"><a href="#" ng-click="changePage(page)">@{{ page }}</a></li>
                        <li><a href="#" ng-click="changePage(-1)">&raquo;</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!--Modal dialog for showing tickets-->
        <div class="modal fade" id="ticketsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myModalLabel">Tickets Bought in this Raffle</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div ng-cloak class="col-md-1 col-sm-1 col-xs-1" ng-repeat="raffleNumber in raffleNumbers">
                                    <div class="checkbox checkbox-info">
                                        <label for="chkbox_number_@{{ raffleNumber }}">@{{ raffleNumber }}</label>
                                        <input id="chkbox_number_@{{ raffleNumber }}" class="styled" type="checkbox" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" ng-disabled="false" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i> Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal (Pop up when delete button clicked) -->
        <div class="modal fade" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myModalLabel">Refund Confirmation</h4>
                    </div>
                    <div id="div-delete-question">
                        <div class="modal-body">
                            <div>
                                <span class="label label-danger" style="font-size: 14px; font-weight: 1em;"><i class="fa fa-exclamation-triangle"></i> Caution!</span><br /><br />
                                <b>This action cannot be undone.</b><br /><br />
                                You are about to refund <code>$@{{ item.amount_to_refund }}</code> to user <b>@{{ item.user_name }}</b>.<br />
                                Are you sure you want to proceed?
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" ng-disabled="false" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i> No</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-delete" ng-click="confirmRefund(item.id)"><i class="fa fa-check"></i> Yes</button>
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
<script src='{{ asset("app/controllers/transactions_log_controller.js") }}'></script>

<!--[if (gte IE 8)&(lt IE 10)]>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/cors/jquery.xdr-transport.js')}}"></script>
<![endif]-->

@endsection