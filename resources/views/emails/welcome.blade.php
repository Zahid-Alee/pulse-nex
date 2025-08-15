<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome!</title>
    <style>
        /* It's best practice to use inline CSS for emails */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        .container {
            padding: 20px;
            border: 1px solid #ddd;
            max-width: 600px;
            margin: auto;
        }

        .header {
            font-size: 24px;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="header">Hi, {{ $user->name }}!</h1>
        <p>
            Welcome to our application! We're excited to have you on board.
        </p>
        <p>
            Your registered email is: <strong>{{ $user->email }}</strong>.
        </p>
        <p>
            Thanks,<br>
            The {{ config('app.name') }} Team
        </p>
    </div>
</body>

</html>
