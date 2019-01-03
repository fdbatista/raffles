<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Site Contact</title>
</head>
<body style="font-family: Verdana">
    <p>
        Mail from: <?= $model->name . ' (' . $model->email . ')' ?><br />
        Subject: <?= $model->subject ?><br />
        Body:<br /><br />
        <?= $model->body ?>
    </p>
</body>
</html>
