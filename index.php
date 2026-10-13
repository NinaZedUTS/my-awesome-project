<?php

require_once 'auth.php';

requireLogin();

?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My AI Assistant</title>


    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body>


<div class="app">


    <!-- HEADER -->

    <header class="header">

        <div>

            <h1>
Specialised health advice
            </h1>

            <p>
                Powered by Stroke Foundation guidelines and layers of fine-tuning<br>

<a href="logout.php">
    Logout
</a>            </p>
        </div>


        <button
            id="clearButton"
            class="clear-button"
        >
            New chat
        </button>

    </header>



    <!-- CHAT -->

    <main
        id="chat"
        class="chat"
    >

        <div class="welcome">


            <h2>
                How can I help?
            </h2>

            <p>
                Ask me anything health related..
            </p>

        </div>

    </main>



    <!-- INPUT -->

    <form
        id="chatForm"
        class="input-area"
    >

        <textarea
            id="message"
            rows="1"
            placeholder="Type your message..."
        ></textarea>


        <button
            id="sendButton"
            type="submit"
        >
            Send
        </button>

    </form>


</div>

<script src="js/app.js"></script>


</body>

</html>
