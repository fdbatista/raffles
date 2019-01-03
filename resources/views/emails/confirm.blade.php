<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign up confirmation</title>
</head>
<body style="font-family: Verdana">
    <p>
        Dear <?= $user->name ?>:<br />
        Thank you very much for signing up at our site.
    </p>
    <p>
        If you're receiving this email by mistake, simply ignore it.
    </p>
    <p>Click on the link below to confirm your email address:</p><br />
    <p>
        <a href='{{ url("confirm-email/" . $user->activation_token) }}'>Confirm my account</a>
    </p>
    <p>
        Please, keep in mind that your request will be available for the next 3 days.<br />
        After that, it will be automatically deleted and you'll need to start the process from scratch.
    </p>
</body>
</html>
