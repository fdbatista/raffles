<?php
    var_dump($params);
?>

<form method="post" action="<?= $params['return'] ?>">
    {!! csrf_field() !!}
    <p>Destination account: <?= $params['business'] ?></p>
    <p>Item name: <?= $params['item_name'] ?></p>
    <p>Item number: <?= $params['item_number'] ?></p>
    <p>Amount: <?= $params['payment_gross'] . " " . $params['mc_currency'] ?></p>
    <a href="<?= $params['cancel_return'] ?>">Go back</a>
    
    <input type="hidden" name="item_number" value="<?= $params['item_number'] ?>"  />
    <input type="hidden" name="txn_id" value="<?= str_random(25) ?>"  />
    <input type="hidden" name="payment_gross" value="<?= $params['payment_gross'] ?>"  />
    <input type="hidden" name="mc_currency" value="<?= $params['mc_currency'] ?>"  />
    <input type="hidden" name="payment_status" value="Completed"  />
    
    <input type="submit" value="Confirm payment" />
</form>