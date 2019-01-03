@extends('layouts.layout_guest')

@section('pagelevelstyles')
@parent

@stop

@section('innercontent')

@include('flash')

<div class="row">
    <h3>Operation results</h3>
    <table>
        
        <tbody>
            <tr>
                <td>Operation #:</td>
                <td><?= $params['item_number'] ?></td>
            </tr>
            <tr>
                <td>PayPal Transaction ID:</td>
                <td><?= $params['paypal_transaction_id'] ?></td>
            </tr>
            <tr>
                <td>Amount:</td>
                <td><?= $params['amount_payed'] ?></td>
            </tr>
            <tr>
                <td>Currency:</td>
                <td><?= $params['currency'] ?></td>
            </tr>
            <tr>
                <td>PayPal Operation Status:</td>
                <td><?= $params['product_status'] ?></td>
            </tr>
            <tr>
                <td>Overall Result:</td>
                <td><?= $params['final_result'] ?></td>
            </tr>
        </tbody>
    </table>
    <br />
    <a href="<?= url('/product-list') ?>" class="btn btn-primary"><i class="fa fa-btn fa-reply"></i> Back to Product List</a>
    
</div>

@stop

@section('javascripts')
@parent
<script src='{{ asset("app/controllers/next_raffles_controller.js") }}'></script>


@endsection
