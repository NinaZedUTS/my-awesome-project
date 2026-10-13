<?php

require_once 'config.php';
require_once 'auth.php';


$error = '';


// Already logged in?

if (
    !empty($_SESSION['logged_in']) &&
    $_SESSION['logged_in'] === true
) {

    header('Location: index.php');

    exit;
}


// Process login

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    $password =
        $_POST['password'] ?? '';


    if (
        password_verify(
            $password,
            CHAT_PASSWORD_HASH
        )
    ) {

        session_regenerate_id(true);

        $_SESSION['logged_in'] = true;

        $_SESSION['login_time'] =
            time();


        header('Location: index.php');

        exit;

    } else {

        $error =
            'Incorrect password.';
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Chat Login</title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            font-family:
                Arial,
                sans-serif;

            background: #f5f7fa;

        }


        .login-box {

            width: 100%;

            max-width: 400px;

            padding: 40px;

            background: white;

            border-radius: 12px;

            box-shadow:
                0 10px 40px
                rgba(0,0,0,.1);

        }


        h1 {

            margin-top: 0;

            text-align: center;

        }


        input {

            width: 100%;

            padding: 14px;

            margin-top: 10px;

            margin-bottom: 20px;

            border: 1px solid #ccc;

            border-radius: 6px;

            font-size: 16px;

        }


        button {

            width: 100%;

            padding: 14px;

            border: 0;

            border-radius: 6px;

            background: #222;

            color: white;

            font-size: 16px;

            cursor: pointer;

        }


        button:hover {

            opacity: .9;

        }


        .error {

            margin-bottom: 20px;

            padding: 10px;

            background: #ffe5e5;

            color: #b00000;

            border-radius: 6px;

            text-align: center;

        }

    </style>

</head>


<body>


<div class="login-box">

    <h1>Chat Login</h1>


    <?php if ($error): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <form method="POST">

        <label for="password">

            Password

        </label>


        <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            required
        >


        <button type="submit">

            Login

        </button>

    </form>

</div>


</body>

</html>