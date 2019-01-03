<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Raffle Transaction Successful</title>
</head>
<body style="font-family: Verdana">
    <p>
        Dear <?= $model->username ?>:<br />
        You have succesfully completed the following transaction:
    </p>
    <p>
        <b>Transaction ID:</b> <?= $model->transaction_id ?><br />
        <b>Date:</b> <?= $model->date ?><br />
        <b>Product name:</b> <?= $model->description ?><br />
        <b>Tickets bought:</b> <?= $model->tickets ?><br />
        <b>Amount payed:</b> <?= $model->amount ?><br />
    </p>
    <p>
        In case of refund, you will receive the <?= $model->percent_to_refund ?>% of the amount payed.
    </p>
    <p>
        Thank you for your support!<br />
    </p>
</body>
</html>
