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
    <h2>My Transactions</h2>
    <div ng-controller="MyTransactionsController" ng-init="userInit('<?= $api_token ?>', '<?= url('') ?>')">
        
        <div class="text-center" id="img-loading">
            <img src='{{ asset("assets/img/loading.gif") }}' />
        </div>
        <div class="row" style="margin-top: 5px">
            <div class="col-lg-3">
                <input type="text" value="@{{ itemsFilter }}" placeholder="Search term" ng-model="itemsFilter" ng-change="showGrid()" class="form-control" />
            </div>
        </div>
        <div class="row">           
            <div class="col-lg-12" id="products-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 150px;"><a href="#" ng-click="toggle_sort('transaction_description')">Product</a></th>
                            <th style="width: 150px;"><a href="#" ng-click="toggle_sort('paypal_transaction_id')">Transaction ID</a></th>
                            <th style="width: 150px;"><a href="#" ng-click="toggle_sort('transaction_status')">Status</a></th>
                            <th style="width: 25px;"><a href="#" ng-click="toggle_sort('amount_to_pay')">Amount</a></th>
                            <th style="width: 25px;"><a href="#" ng-click="toggle_sort('amount_to_refund')">Refund</a></th>
                            <th style="width: 100px;"><a href="#" ng-click="toggle_sort('tickets')">Tickets</a></th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody id="grid-content" style="display: none;">
                        <tr ng-repeat="item in items | filter:itemsFilter | orderBy:itemsOrderField:itemsOrderType">
                            <td ng-bind-html="item.transaction_description | unsafe"></td>
                            <td>@{{ item.paypal_transaction_id }}</td>
                            <td>@{{ item.transaction_status }}</td>
                            <td>$@{{ item.amount_to_pay }}</td>
                            <td>$@{{ item.amount_to_refund }}</td>
                            <td>@{{ item.tickets }}</td>
                            <td><a ng-show="item.owner_id !== null" class="btn btn-info" href="<?= url('/') ?>/users/details/@{{ item.owner_id }}"><i class="fa fa-user"></i> Owner Details</a></td>
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
        
    </div>
</div>

@endsection

@section('javascripts')
@parent
<script src='{{ asset("app/controllers/my_transactions_controller.js") }}'></script>

<!--[if (gte IE 8)&(lt IE 10)]>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/cors/jquery.xdr-transport.js')}}"></script>
<![endif]-->

@endsection