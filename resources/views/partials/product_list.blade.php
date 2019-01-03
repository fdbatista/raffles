@extends('layouts.layout_guest')

@section('pagelevelstyles')

@parent
<link href="{{ asset('assets/plugins/bootstrap-checkbox/css/build.css')}}" rel="stylesheet">
@stop

@section('innercontent')

<?php
    $api_token = Auth::check() ? Auth::user()->api_token : "";
?>

@include('flash')
<div ng-controller="NextRafflesController" ng-init="userInit('<?= $api_token ?>', '<?= url('') ?>', '<?= isset($category_id) ? $category_id : null ?>', '<?= isset($product_id) ? $product_id : null ?>')">
    
    <!-- BEGIN PRODUCT LIST -->
    <div>
        <div class="pull-left">
            <input ng-cloak type="text" value="@{{ searchTerm }}" placeholder="Search term" ng-model="searchTerm" class="form-control" />
        </div>
        <div class="pull-left" style="margin: 0 0 0 10px;">
            <button ng-click="search()" class="btn btn-primary"><i class="fa fa-btn fa-search"></i> Search</button>
        </div>
    </div>
    
    <div class="row list-view-sorting clearfix">
        <!--<div class="pull-right">
            <label class="control-label">Items per page: </label>
            <select class="form-control input-sm">
                <option value="3" ng-click="updatePagination(1, 1)">1</option>
                <option value="3" ng-click="updatePagination(3, 1)">3</option>
                <option value="6" ng-click="updatePagination(6, 1)">6</option>
                <option value="9" ng-click="updatePagination(9, 1)" selected="selected">9</option>
                <option value="12" ng-click="updatePagination(12, 1)">12</option>
                <option value="15" ng-click="updatePagination(15, 1)">15</option>
                <option value="all" ng-click="updatePagination(-1, 1)">ALL</option>
            </select>
        </div>-->
        <div class="pull-right">
            <label class="control-label">Sort&nbsp;By:</label>
            <select class="form-control input-sm">
                <option ng-click="toggle_sort('ending_date', 'asc')" value="date_asc" selected="selected">Date ascending</option>
                <option ng-click="toggle_sort('ending_date', 'desc')" value="date_desc">Date descending</option>
                <option ng-click="toggle_sort('product_name', 'asc')" value="name_asc">Name ascending</option>
                <option ng-click="toggle_sort('product_name', 'desc')" value="name_desc">Name descending</option>
                <option ng-click="toggle_sort('ticket_price', 'asc')" value="price_asc">Price ascending</option>
                <option ng-click="toggle_sort('ticket_price', 'desc')" value="price_desc">Price descending</option>
            </select>
        </div>
    </div>
    
    <!-- BEGIN PRODUCT LIST -->
    <div class="alert fade in col-md-4 col-md-offset-4" id="div-message" style="display: none;">
        <i class="fa fa-btn fa-warning"></i> @{{ message }}
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    </div>
    
    <!-- BEGIN PAGINATOR -->
    <div class="row">
        <div class="col-md-8 col-sm-8">
            <span ng-cloak>@{{ (itemsCount > 0) ? paginationConfig.itemsPerPage * (paginationConfig.currentPage - 1) + 1 : 0 }} to @{{ (paginationConfig.itemsPerPage * paginationConfig.currentPage > itemsCount) ? itemsCount : paginationConfig.itemsPerPage * paginationConfig.currentPage }} of <span class="label label-@{{ itemsCount > 0 ? 'info' : 'danger' }}">@{{ itemsCount }}</span> items</span>
        </div>
    </div><br />
    <div class="row">
        <div class="col-md-8 col-sm-8">
            <ul ng-cloak class="pagination">
                <li><a href="#" ng-click="changePage(1)">&laquo;</a></li>
                <li ng-repeat="page in paginationConfig.pages"><a href="#" ng-click="changePage(page)">@{{ page }}</a></li>
                <li><a href="#" ng-click="changePage(-1)">&raquo;</a></li>
            </ul>
        </div>
    </div>
    <br />
    <!-- END PAGINATOR -->
    
    <div class="text-center" id="img-loading">
        <img src='{{ asset("assets/img/loading.gif") }}' />
    </div>
    
    <div class="product-list" id="grid-content-raffles">
        <!-- PRODUCT ITEM START -->
        <div ng-cloak id="div-raffle-@{{ item.id }}" class="pull-left" ng-repeat="item in items" style="margin: 0 5px 0 0;">
            <div class="product-item">
                <div class="pi-img-wrapper">
                    <img ng-cloak style="max-height: 150px; width: auto; margin: 0 auto;" src="<?= url('/') ?>/@{{ item.image_path }}" class="img-responsive" alt="@{{ item.product_name }}">
                    <div>
                        <a href="#product-pop-up" ng-click="setCurrentRaffle(item.id, 'images')" class="btn btn-default fancybox-fast-view">Details</a>
                    </div>
                </div>
                <h3>@{{ item.product_name }} - <span class="badge">@{{ item.product_category }}</span></h3>
                <div>
                    <p class="text-center"><span style="font-size: 40px; margin: 0 auto;" class="glyphicon glyphicon-time"></span></p>
                    <div id="countdown-raffle-@{{ item.id }}" countdown seconds="@{{ item.time_remaining }}" class="clock" style="margin:2em;"></div>
                    
                </div>
                <div style="position: absolute;">
                    <div class="pi-price">$@{{ item.ticket_price }}</div>
                    <div style="margin-left: 115px;">
                        <?php
                            if (Auth::check())
                            {?>
                                <a href="#buy-tickets-pop-up" ng-click="setCurrentRaffle(item.id, 'numbers')" class="btn btn-default add2cart fancybox-fast-view"><i class="fa fa-btn fa-dollar"></i> Buy tickets</a>
                            <?php
                            }
                            else
                            {?>
                                <form role="form" action="<?= url("/login") ?>" method="GET">
                                    {!! csrf_field() !!}
                                    <input type="hidden" name="raffle_id" value="@{{ item.product_id }}" />
                                    <button type="submit" class="btn btn-default"><i class="fa fa-btn fa-dollar"></i> Buy tickets</button>
                                </form>
                            <?php
                            }
                        ?>
                    </div>
                </div>
                
            </div>
        </div>
        <!-- PRODUCT ITEM END -->
    </div>
    <!-- END PRODUCT LIST -->
    
    <!-- BEGIN PAGINATOR -->
    <div class="row">
        <div class="col-md-8 col-sm-8">
            <ul ng-cloak class="pagination">
                <li><a href="#" ng-click="changePage(1)">&laquo;</a></li>
                <li ng-repeat="page in paginationConfig.pages"><a href="#" ng-click="changePage(page)">@{{ page }}</a></li>
                <li><a href="#" ng-click="changePage(-1)">&raquo;</a></li>
            </ul>
        </div>
    </div>
    <!-- END PAGINATOR -->
    
    <!-- END PRODUCT LIST -->
    
    <!-- BEGIN view of a product -->
    <div id="product-pop-up" style="display: none; width: 700px;">
        <div class="product-page product-pop-up">
            <div class="row" id="grid-content-product">
                <div class="col-md-6 col-sm-6 col-xs-3">
                    <div class="product-main-image" id="product-main-image">
                        <img src="<?= url('/') ?>/@{{ raffle.image_path }}" alt="@{{ raffle.product_name }}" class="img-responsive" style="max-height: 480px; max-width: auto;">
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-9">
                    <h1>@{{ raffle.product_name }}</h1>
                    <div class="price-availability-block clearfix">
                        <div class="price">
                            <strong>Ticket price: <span>$</span>@{{ raffle.ticket_price }}</strong>
                        </div>
                    </div>
                    <div class="description">
                        <p><span class="fa fa-gift"></span> @{{ raffle.product_desc }}</p>
                    </div>
                    <div class="description">
                        <p><span class="fa fa-calendar"></span> From @{{ raffle.starting_date }} until @{{ raffle.ending_date }}</p>
                    </div>
                    <div class="description">
                        <p><span class="fa fa-star"></span> Product condition: @{{ raffle.product_condition }}</p>
                    </div>
                    <!--<div class="product-page-cart">
                        <div class="product-quantity">
                            <input id="product-quantity" type="text" value="1" readonly name="product-quantity" class="form-control input-sm">
                        </div>
                        <button class="btn btn-primary" type="submit">Add to cart</button>
                    </div>-->
                </div>
                <div class="sticker sticker-sale"></div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-3">
                    <div class="product-other-images">
                        <a ng-cloak ng-repeat="productImage in productImages" ng-click="changeRaffleImage(productImage.image_path)" href="#"><img alt="@{{ productImage.name }}" title="@{{ productImage.name }}" src="<?= url('/') ?>/@{{ productImage.image_path }}"></a>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-9" style="position:relative; min-height: 60px;">
                    <div class="pull-right" style="position:absolute; bottom: 0; right: 20px;">
                        <?php
                            if (Auth::check())
                            {?>
                                <a href="#buy-tickets-pop-up" ng-click="setCurrentRaffle(raffle.id, 'numbers')" class="btn btn-default add2cart fancybox-fast-view"><i class="fa fa-btn fa-dollar"></i> Buy tickets</a>
                            <?php
                            }
                            else
                            {?>
                                <form role="form" action="<?= url("/login") ?>" method="GET">
                                    {!! csrf_field() !!}
                                    <input type="hidden" name="raffle_id" value="@{{ raffle.product_id }}" />
                                    <button type="submit" class="btn btn-default"><i class="fa fa-btn fa-dollar"></i> Buy tickets</button>
                                </form>
                            <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END fast view of a product -->
    
    <div id="buy-tickets-pop-up" style="display: none; width: 800px;">
        <div class="product-page product-pop-up">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <h1>Available tickets for @{{ raffle.product_name }}</h1>
                </div>
                <div class="col-md-12">
                    <div id="div-message-tickets" class="alert fade in" style="height: 25px; padding: 5px; opacity: 0">
                        <p>@{{ message }}</p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8" id="div-search-number">
                        <div class="container">
                            <div class="pull-left">
                                <input ng-cloak type="text" value="@{{ searchNumber }}" placeholder="Search number" ng-model="searchNumber" class="form-control" />
                            </div>                            
                            <div class="pull-left" style="margin: 0 0 0 10px;">
                                <button ng-click="searchRaffleAvailableNumbers(1)" class="btn btn-primary"><i class="fa fa-btn fa-search"></i> Search</button>
                            </div>
                        </div>

                        <div class="container" style="margin: 10px 0 10px;">
                            <div class="pull-left">
                                <div class="product-quantity">
                                    From <input id="range-start" ng-change="validateRange()" ng-model="rangeStart" type="text" value="1" name="range-start" class="form-control" style="width: 70px!important; margin-right: 20px;">
                                </div>
                                <div class="product-quantity" style="margin-left: 25px;">
                                    to <input id="range-end" ng-change="validateRange()" ng-model="rangeEnd" type="text" value="1" name="range-end" class="form-control" style="width: 70px!important; margin-right: 20px;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="container" style="margin-top: 15px;">
                            <p>Available numbers: <span class="badge-@{{ raffleNumbersCount > 0 ? 'primary' : 'danger' }} text-center" style="color: #fff; border-radius: 5px!important; margin: 0 5px 3px 0; padding: 3px 5px;">@{{ raffleNumbersCount }}</span></p>
                        </div>
                    </div>

                    <div class="col-md-4" id="div-checkout-tickets">
                        <div class="container" style="width: 90%;">
                            <p>
                                <i class="fa fa-hand-pointer-o"></i> Selection: <span class="badge-@{{ selectedNumbers.length > 0 ? 'primary' : 'danger' }} text-center" style="color: #fff; border-radius: 5px!important; margin: 0 5px 3px 0; padding: 3px 5px;"><i class="fa fa-shopping-cart"></i> @{{ selectedNumbers.length }}</span>
                                <a id="btn-reset-numbers-selection" ng-show="selectedNumbers.length > 0" href="#" ng-click="resetNumbersSelection()" class="badge-danger" style="text-decoration: none; color: #fff; border-radius: 5px!important; margin: 0 5px 3px 0; padding: 3px 5px;">Reset</a>
                            </p>
                            <p>
                                <i class="fa fa-dollar"></i> Amount: <span class="badge-warning text-center" style="color: #fff; border-radius: 5px!important; margin: 0 5px 3px 0; padding: 3px 5px;">$@{{ raffle.ticket_price * selectedNumbers.length }}</span>
                            </p>
                            <div id="div-selected-numbers">
                                <div ng-repeat="number in selectedNumbers" class="badge-success text-center" style="color: #fff; float: left; border-radius: 5px!important; margin: 0 5px 3px 0; padding: 3px 5px;"> @{{ number }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12" style="margin-top: 10px;">
                    <div class="text-center" id="img-loading-tickets">
                        <img style="max-height: 50px; width: auto" src='{{ asset("assets/img/loading.gif") }}' />
                    </div>
                </div>
                
                <form id="form-submit-paypal" method="post" action="<?= $paypal_url ?>">
                    {!! csrf_field() !!}
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="cancel_return" value="<?= url('/product-list') ?>">
                    <input type="hidden" name="return" value="<?= url('payment/process-payment-results') ?>">
					<input type="hidden" name="notify_url" value="<?= url('payment/process-payment-results') ?>">
                    <input type="hidden" name="business" value="<?= $sys_paypal_account ?>">
                    <input type="hidden" name="lc" value="C2">
                    <input type="hidden" id="item_name" name="item_name" value="@{{ raffle.product_name }}">
                    <input type="hidden" id="item_number" name="item_number" value="@{{ raffle.product_id }}">
                    <input type="hidden" name="amount" value="@{{ raffle.ticket_price }}" id="total-amount">
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="button_subtype" value="services">
                    <input type="hidden" name="no_note" value="0">
                    
                    <div class="col-md-12 col-sm-12 col-xs-12" id="grid-content-tickets" style="margin-top: 10px;">
                        <div ng-cloak class="col-md-1 col-sm-1 col-xs-1" ng-repeat="raffleNumber in raffleNumbers">
                            <div class="checkbox checkbox-primary">
                                <input ng-checked="selectedNumbers.indexOf(raffleNumber.chosen_number) !== -1" value="@{{ raffleNumber.chosen_number }}" id="chkbox_number_@{{ raffleNumber.chosen_number }}" class="styled" name="numbers[]" type="checkbox" ng-click="updateNumberSelection(raffleNumber.chosen_number)">
                                <label for="chkbox_number_@{{ raffleNumber.chosen_number }}">
                                    @{{ raffleNumber.chosen_number }}
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12" id="div-available-numbers-pagination" style="margin: 10px auto;">
                        <div class="container">
                            <ul ng-cloak class="pagination">
                                <li><a href="#" ng-click="searchRaffleAvailableNumbers(1)">&laquo;</a></li>
                                <li ng-cloak ng-repeat="page in paginationConfig.availableNumbersPages"><a href="#" ng-click="searchRaffleAvailableNumbers(page)">@{{ page }}</a></li>
                                <li><a href="#" ng-click="searchRaffleAvailableNumbers(-1)">&raquo;</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="btn-checkout-tickets" class="btn btn-primary" ng-click="checkoutTickets()" ng-disabled="false">
                            <i class="fa fa-check-square"></i> Checkout
                        </button>
                        <button type="button" style="display: none;" id="btn-back-to-tickets" class="btn btn-danger" ng-click="goBackToTickets()" ng-disabled="false">
                            <i class="fa fa-refresh"></i> Go back
                        </button>
                        <button type="button" style="display: none;" id="btn-assign-tickets" class="btn btn-primary" ng-click="submitToPaypal()" ng-disabled="false">
                            <i class="fa fa-dollar"></i> Confirm Payment
                        </button>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</div>

@stop

@section('javascripts')
@parent
<script src='{{ asset("app/controllers/next_raffles_controller.js") }}'></script>

@endsection
