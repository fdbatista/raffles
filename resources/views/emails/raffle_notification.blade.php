<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $notification->name }}</title>
</head>
<body>
    <p style="color: #31708f; background-color: #d9edf7; border-color: #bce8f1;">
        This is an automatically generated message. Please, do not reply.
    </p><br /><br />
    <p class="text-justify">
        <?= nl2br($notification->content) ?>
    </p>
</body>
</html>