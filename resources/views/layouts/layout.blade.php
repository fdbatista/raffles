<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<!-- Head BEGIN -->
<head>
  <meta charset="utf-8">
  <?php
    $config = App\Models\AppConfig::find(1);
  ?>
  <title><?= $config->app_title ?></title>

  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta content="Raffle template " name="description">
  <meta content="Rifa Shop UI keywords" name="keywords">
  <meta content="Al-FD" name="author">

  <!--
  <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&subset=all" rel="stylesheet" type="text/css">
  <link href="http://fonts.googleapis.com/css?family=PT+Sans+Narrow&subset=all" rel="stylesheet" type="text/css">
  <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900&subset=all" rel="stylesheet" type="text/css"><!--- fonts for slider on the index page
  -->

  <!-- Global styles START -->          
  <link href='{{ asset("assets/css/site.css") }}' rel='stylesheet' type='text/css'>
  <link href='{{ asset("assets/css/font-awesome.min.css") }}' rel='stylesheet' type='text/css'>
  <link href='{{ asset("assets/css/bootstrap.min.css") }}' rel='stylesheet' type='text/css'>
  <link href='{{ asset("assets/plugins/flip-clock/flipclock.css") }}' rel='stylesheet' type='text/css'>
  <link href='{{ asset("app/lib/angular-ui/select.css") }}' rel='stylesheet' type='text/css'>
  
  <!-- Global styles END -->
  
  <script src="{{ asset('assets/plugins/jquery.min.js')}}" type="text/javascript"></script>
  <script src="{{ asset('assets/plugins/flip-clock/flipclock.min.js')}}" type="text/javascript"></script>
  
  <!-- Page level plugin styles START -->
  @yield('pagelevelstyles')
  <!-- Page level plugin styles END -->

  <!-- Theme styles START -->
  <link href="{{ asset('assets/css/style-rifa.css')}}" rel="stylesheet" type="text/css">
  <link href="{{ asset('assets/css/style.css')}}" rel="stylesheet" type="text/css">
  <link href="{{ asset('assets/css/style-responsive.css')}}" rel="stylesheet" type="text/css">  
  <link href="{{ asset('assets/css/custom.css')}}" rel="stylesheet" type="text/css">  
  
  <!-- Theme styles END -->
</head>
<!-- Head END -->

<!-- Body BEGIN -->
<body>
    <!-- BEGIN TOP BAR -->
    <div class="pre-header">
        <div class="container">
            <div class="row">
                <!-- BEGIN TOP BAR LEFT PART -->
                <div class="col-md-6 col-sm-6 additional-shop-info">
                    <ul class="list-unstyled list-inline hidden">
                        
                </div>
                <!-- END TOP BAR LEFT PART -->
                <!-- BEGIN TOP BAR MENU -->
                <div class="col-md-6 col-sm-6 additional-nav">
                    <ul class="list-unstyled list-inline pull-right">
                        @if (Auth::guest())
                            <li><a href="{{ url('/login') }}"><i class="fa fa-sign-in"></i>Login</a></li>
                            <li><a href="{{ url('/register') }}"><i class="fa fa-user-plus"></i>Register</a></li>
                        @else   
                            @if (Auth::user()->isAdministrator())
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    <i class="fa fa-btn fa-gears"></i>Administration <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <li class="dropdown-submenu">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-database"></i>DB Entities<i class="fa fa-angle-right"></i>
                                        </a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="{{ url('/categories') }}"><i class="fa fa-server"></i>Categories</a></li>
                                            <li><a href="{{ url('/product-conditions') }}"><i class="fa fa-signal"></i>Conditions</a></li>
                                            <li><a href="{{ url('/countries') }}"><i class="fa fa-globe"></i>Countries</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-code"></i>Application<i class="fa fa-angle-right"></i>
                                        </a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="{{ url('/app-config') }}"><i class="fa fa-wrench"></i>Configuration</a></li>
                                            <li><a href="{{ url('/main-slider') }}"><i class="fa fa-image"></i>Slider Images</a></li>
                                            <li class="divider"></li>
                                            <li><a href="{{ url('/users') }}"><i class="fa fa-users"></i>Users</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            @endif
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-btn fa-user"></i>My Account <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li class="dropdown-header">{{ Auth::user()->name }}</li>
                                    <li><a href="{{ url('/my-profile') }}"><i class="fa fa-btn fa-edit"></i>My Profile</a></li>
                                    <li class="divider"></li>
                                    <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
                <!-- END TOP BAR MENU -->
            </div>
        </div>        
    </div>
    <!-- END TOP BAR -->

    <!-- BEGIN HEADER -->
    <div role="navigation" class="navbar header no-margin">
        <div class="container">
            <div class="navbar-header">
                <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <!-- END RESPONSIVE MENU TOGGLER -->
                <a href="{{ url('/') }}" class="navbar-brand"><img src="{{ asset('assets/img/logo_red.png')}}" alt="<?= $config->app_title ?>"></a><!-- LOGO -->
            </div>            
            <!-- BEGIN NAVIGATION -->
            <div class="collapse navbar-collapse mega-menu">
                <ul class="nav navbar-nav">
                    <li><a href="{{ url('/product-list')}}">Raffles List</a></li>
                    <li><a href="{{ url('/contact')}}">Contact Us</a></li>
                    @if (Auth::check())
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" data-delay="0" data-close-others="false" data-target="product-list.html" href="product-list.html">
                                View <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu">   
                                <li><a href="{{ url('/my-tickets') }}">My Tickets</a></li>
                                <li><a href="{{ url('/my-transactions') }}">My Transactions</a></li>
                            </ul>
                        </li>
                        <?php
                            $appConfig = \App\Models\AppConfig::find(1);
                            if ((Auth::user()->subscribed == 1 && $appConfig->allow_raffle_creation == 1) || Auth::user()->role_id == 1)
                            {?>
                                <li><a href="{{ url('/products') }}">My Products</a></li>
                            <?php
                            }
                        ?>
                        
                            
                    @endif
                    
                </ul>
            </div>
            <!-- END NAVIGATION -->
        </div>
    </div>
    <!-- END HEADER -->    
    
    <!-- BEGIN CONTENT -->
    
        @yield('content')  
       
    <!-- END CONTENT -->
    
    <!-- BEGIN PRE-FOOTER -->
        <div class="pre-footer">
          <div class="container">
            <div class="row">
              <!-- BEGIN BOTTOM ABOUT BLOCK -->
              <div class="col-md-6 pre-footer-col">
                  <h2>About us</h2>
                  <p>
                      <?= nl2br($config->about_us) ?>
                  </p>
              </div>
              <!-- END BOTTOM ABOUT BLOCK -->              
              <!-- BEGIN BOTTOM CONTACTS -->
              <div class="col-md-4 col-md-offset-2 pre-footer-col" style="text-align:right;">
                <h2>Our Contacts</h2>
                <address class="margin-bottom-40">
                      <?= nl2br($config->contact_us) ?>
                </address>
              </div>
              <!-- END BOTTOM CONTACTS -->
            </div>
            <hr>
            
          </div>
        </div>
    <!-- END PRE-FOOTER -->

    <!-- BEGIN FOOTER -->
    <div class="footer padding-top-15">
      <div class="container">
        <div class="row">
          <!-- BEGIN COPYRIGHT -->
          <div class="col-md-6 col-sm-6 padding-top-10">
            2016 © RaffleShop. ALL Rights Reserved. 
          </div>
          <!-- END COPYRIGHT -->
          <!-- BEGIN PAYMENTS -->
          <div class="col-md-6 col-sm-6">
            <ul class="list-unstyled list-inline pull-right margin-bottom-15">
              <!--li><img src="{{ asset('assets/img/payments/western-union.jpg')}}" alt="We accept Western Union" title="We accept Western Union"></li>
              <li><img src="{{ asset('assets/img/payments/american-express.jpg')}}" alt="We accept American Express" title="We accept American Express"></li>
              <li><img src="{{ asset('assets/img/payments/MasterCard.jpg')}}" alt="We accept MasterCard" title="We accept MasterCard"></li>-->
              <li><img src="{{ asset('assets/img/payments/PayPal.jpg')}}" alt="We accept PayPal" title="We accept PayPal"></li>
              <!--li><img src="{{ asset('assets/img/payments/visa.jpg')}}" alt="We accept Visa" title="We accept Visa"></li-->
            </ul>
          </div>
          <!-- END PAYMENTS -->
        </div>
      </div>
    </div>
    <!-- END FOOTER -->

    

    <!-- Load javascripts at bottom, this will reduce page load time -->
    <!-- BEGIN CORE PLUGINS (REQUIRED FOR ALL PAGES) -->
    <!--[if lt IE 9]>
    <script src="{{ asset('assets/plugins/respond.min.js')}}"></script>  
    <![endif]-->  
    
    <script src="{{ asset('assets/plugins/jquery-migrate-1.2.1.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>      
    <script type="text/javascript" src="{{ asset('assets/plugins/back-to-top.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/jQuery-slimScroll/jquery.slimscroll.min.js')}}"></script>
    <!-- END CORE PLUGINS -->

    <!-- BEGIN PAGE LEVEL JAVASCRIPTS (REQUIRED ONLY FOR CURRENT PAGE) -->
    <script type="text/javascript" src="{{ asset('assets/plugins/fancybox/source/jquery.fancybox.pack.js')}}"></script><!-- pop up -->
    <script type="text/javascript" src="{{ asset('assets/plugins/bxslider/jquery.bxslider.min.js')}}"></script><!-- slider for products -->
    <script type="text/javascript" src="{{ asset('assets/plugins/zoom/jquery.zoom.min.js') }}"></script><!-- product zoom -->
    <script src="{{ asset('assets/plugins/bootstrap-touchspin/bootstrap.touchspin.js')}}" type="text/javascript"></script><!-- Quantity -->
    
    <script src='{{ asset("app/lib/angular/angular.min.js") }}'></script>
    <script src='{{ asset("app/lib/angular/angular-route.min.js") }}'></script>
    <script src='{{ asset("app/lib/angular/angular-resource.min.js") }}'></script>
    <script src='{{ asset("app/lib/angular/angular-sanitize.min.js") }}'></script>
    <script src='{{ asset("app/app.js") }}'></script>
    <script src='{{ asset("app/lib/angular-ui/mask.js") }}'></script>
    <script src='{{ asset("app/lib/angular-ui/select.min.js") }}'></script>
    <script src='{{ asset("app/bootstrap-colorpicker-module.js") }}'></script>
    <script src='{{ asset("app/angular-wysiwyg.js") }}'></script>
    
    @yield('javascripts')       

    <script type="text/javascript" src="{{ asset('assets/scripts/app.js')}}"></script>
    
    <script type="text/javascript">
        jQuery(document).ready(function() {
            App.init();    
            App.initBxSlider();            
            App.initImageZoom();
            App.initTouchspin();
            
            @yield('scripts')
        });
    </script>
    
    <!-- END PAGE LEVEL JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
