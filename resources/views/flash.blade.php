@if (session()->has('message'))
    <div class="alert alert-info fade in">
        <i class="fa fa-btn fa-info-circle"></i> {{ session('message') }}
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    </div>
@endif
@if (session()->has('warning'))
    <div class="alert alert-warning fade in">
        <i class="fa fa-btn fa-warning"></i> {{ session('warning') }}
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    </div>
@endif
@if (session()->has('error'))
    <div class="alert alert-danger fade in">
        <i class="fa fa-btn fa-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    </div>
@endif