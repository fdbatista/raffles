<?php
    $api_token = Auth::check() ? Auth::user()->api_token : "";
    $appConfig = \App\Models\AppConfig::find(1);
    $paypal_url = $appConfig->paypal_url;
    $sys_paypal_account = $appConfig->sys_paypal_account;
    $incRaffles = App\Models\VNextRaffle::where('time_remaining', '<', 86401)->orderBy('time_remaining')->get();
    if (count($incRaffles) < 4)
        $incRaffles = App\Models\VNextRaffle::orderBy('time_remaining')->take(12)->get();
?>

@section('pagelevelstyles')

@parent
<link href="{{ asset('assets/plugins/bootstrap-checkbox/css/build.css')}}" rel="stylesheet">
@stop

<div class="row margin-bottom-40" ng-controller="IncommingRafflesController" ng-init="userInit('<?= $api_token ?>', '<?= url('') ?>')">
    
    <div class="col-md-12 sale-product">
        <h2>Incomming raffles</h2>
        <div class="bxslider-wrapper">
            <ul class="bxslider" data-slides-phone="1" data-slides-tablet="2" data-slides-desktop="4" data-slide-margin="15">
                <?php
                    foreach ($incRaffles as $raffle)
                    {
                        $divProductId = "div-countdown-incomming-$raffle->id";
                        $divClockId = "clock-countdown-incomming-$raffle->id";
                    ?>
                    <li id="<?= $divProductId ?>">
                        <div class="product-item" style="height: 400px;">
                            <div class="pi-img-wrapper">
                                <img src="<?= asset($raffle->image_path) ?>" class="img-responsive" alt="<?= $raffle->product_name ?>" style="max-height: 130px; width: auto; margin: 0 auto;">
                                <div>
                                    <a href="<?= asset($raffle->image_path) ?>" class="btn btn-default fancybox-button">Zoom</a>
                                    <a href="<?= url("product-details/$raffle->product_id") ?>" class="btn btn-default fancybox-fast-view">Details</a>
                                </div>
                            </div>
                            <h3><a href="<?= url("product-details/$raffle->product_id") ?>"><?= $raffle->product_name ?></a> | <a href="<?= url("product-list/$raffle->product_category_id") ?>"><?= $raffle->product_category ?></a></h3>
                            <div style="font-style: italic">
                                <?= substr($raffle->product_desc, 0, 74) . '...' ?>
                            </div>
                            
                            <div class="row">
                                <div class="col-lg-12" style="max-height: 120px;">
                                    <p class="text-center"><span style="font-size: 40px; margin: 0 auto 15px auto" class="glyphicon glyphicon-time"></span></p>
                                    <div id="<?= $divClockId ?>" style="margin: 0 auto;">
                                    </div>
                                    <script type="application/javascript">
                                        var clock = $('#<?= $divClockId ?>').FlipClock({
                                            clockFace: 'DailyCounter',
                                            autoStart: false,
                                            callbacks: {
                                                stop: function() {
                                                    var divObj = $('#<?= $divProductId ?>');
                                                    divObj.animate({'opacity' : 0}, 1000, 'swing', function(){
                                                        divObj.remove();
                                                    });
                                                }
                                            }
                                        });
                                        clock.setTime(<?= $raffle->time_remaining ?>);
                                        clock.setCountdown(true);
                                        clock.start();

                                    </script>

                                </div>
                            </div>
                            <div class="pi-price">$<?= $raffle->ticket_price ?></div>
                            <div style="margin-right: 15px;">
                                <?php
                                    if (Auth::check())
                                    {?>
                                        <a href="#buy-tickets-pop-up" ng-click="setCurrentRaffle(<?= $raffle->id ?>, 'numbers')" class="btn btn-default add2cart fancybox-fast-view"><i class="fa fa-btn fa-dollar"></i> Buy tickets</a>
                                    <?php
                                    }
                                    else
                                    {?>
                                        <div class="pull-right">
                                            <form role="form" action="<?= url("/login") ?>" method="GET">
                                                {!! csrf_field() !!}
                                                <input type="hidden" name="raffle_id" value="<?= $raffle->product_id ?>" />
                                                <button type="submit" class="btn btn-default"><i class="fa fa-btn fa-dollar"></i> Buy tickets</button>
                                            </form>
                                        </div>
                                        
                                    <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </li>

                    <?php
                    }
                ?>
                
            </ul>
        </div>
    </div>
    
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


                        <div class="container" style="margin-top: 10px;">
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

@section('javascripts')
@parent
<script src='{{ asset("app/controllers/incomming_raffles_controller.js") }}'></script>

@endsection
