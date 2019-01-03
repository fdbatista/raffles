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
            <a class="breadcrumb" href="<?= url('/users') ?>">Users</a>
        </div>
    </div>
    <h2>Tickets Bought by User <a href="<?= url("/users/details/$user->id") ?>"><?= $user->username ?></a></h2>
    <div ng-controller="UsersTicketsController" ng-init="userInit('<?= $api_token ?>', '<?= url('') ?>', '<?= $user->id ?>')">
        
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
                            <th style="width: 35px;"></th>
                            <th style="width: 200px;"><a href="#" ng-click="toggle_sort('product_info')">Product Info</a></th>
                            <th style="width: 200px;"><a href="#" ng-click="toggle_sort('status')">Raffle Status</a></th>
                            <th><a href="#" ng-click="toggle_sort('owner')">Raffle Owner</a></th>
                        </tr>
                    </thead>

                    <tbody id="grid-content" style="display: none;">
                        <tr ng-repeat="item in items | filter:itemsFilter | orderBy:itemsOrderField:itemsOrderType">
                            <td><a class="btn btn-success" style="border-radius: 10px!important;" ng-click="toggle('view-tickets', item.raffle_id)"> @{{ item.tickets_bought }}</a></td>
                            <td ng-bind-html="item.product_info | unsafe"></td>
                            <td>@{{ item.status }}</td>
                            <td>
                                @{{ item.owner }}<br />
                                <a href="<?= url("/users/details") ?>/@{{ item.owner_id }}"><i class="fa fa-user"></i> Details</a> | 
                                <a href="<?= url("/users/raffles") ?>/@{{ item.owner_id }}"><i class="fa fa-calendar"></i> Raffles</a>
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
                                        <label for="chkbox_number_@{{ raffleNumber.chosen_number }}">@{{ raffleNumber.chosen_number }}</label>
                                        <input id="chkbox_number_@{{ raffleNumber.chosen_number }}" class="styled" type="checkbox" />
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
        
    </div>
</div>

@endsection

@section('javascripts')
@parent
<script src='{{ asset("app/controllers/users_tickets_controller.js") }}'></script>

<!--[if (gte IE 8)&(lt IE 10)]>
<script src="{{ asset('assets/plugins/jquery-file-upload/js/cors/jquery.xdr-transport.js')}}"></script>
<![endif]-->

@endsection