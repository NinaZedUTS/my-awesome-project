const chat =
    document.querySelector("#chat");

const form =
    document.querySelector("#chatForm");

const input =
    document.querySelector("#message");

const sendButton =
    document.querySelector("#sendButton");

const clearButton =
    document.querySelector("#clearButton");


// Conversation history

let messages = [];


// Send message

form.addEventListener(
    "submit",
    async function (event) {

        event.preventDefault();


        const text =
            input.value.trim();


        if (!text) {
            return;
        }


        // Display user message

        addMessage(
            text,
            "user"
        );


        // Store message

        messages.push({

            role: "user",

            content: text

        });


        input.value = "";

        resizeTextarea();

        setLoading(true);


        // Show typing indicator

        const typing =
            addTypingIndicator();


        try {


            const response =
                await fetch(
                    "chat.php",
                    {

                        method: "POST",

                        headers: {

                            "Content-Type":
                                "application/json"

                        },

                        body: JSON.stringify({

                            messages:
                                messages

                        })

                    }
                );


            const data =
                await response.json();


            if (!response.ok) {

                throw new Error(
                    data.error ||
                    "Request failed"
                );

            }


            // Remove typing indicator

            typing.remove();


            // Store assistant response

          //  messages.push({

                role: "assistant",

                content:
                    data.reply

            });


            // Display response

            addMessage(

                data.reply,

                "assistant"

            );


        } catch (error) {


            console.error(error);


            typing.remove();


            addMessage(

                "Sorry, something went wrong.",

                "assistant"

            );


        }


        setLoading(false);

    }
);


// Add message

function addMessage(
    text,
    role
) {


    const message =
        document.createElement("div");


    message.className =
        `message ${role}`;


    const content =
        document.createElement("div");


    content.className =
        "message-content";


    content.textContent =
        text;


    message.appendChild(
        content
    );


    chat.appendChild(
        message
    );


    scrollToBottom();

}


// Typing indicator

function addTypingIndicator() {


    const message =
        document.createElement("div");


    message.className =
        "message assistant";


    const content =
        document.createElement("div");


    content.className =
        "message-content";


    content.innerHTML = `

        <div class="typing">

            <span></span>

            <span></span>

            <span></span>

        </div>

    `;


    message.appendChild(
        content
    );


    chat.appendChild(
        message
    );


    scrollToBottom();


    return message;

}


// Loading

function setLoading(
    loading
) {

    sendButton.disabled =
        loading;

    input.disabled =
        loading;

}


// Scroll

function scrollToBottom() {

    chat.scrollTop =
        chat.scrollHeight;

}


// New chat

clearButton.addEventListener(
    "click",
    function () {


        messages = [];


        chat.innerHTML = `

            <div class="welcome">

                <div class="welcome-icon">
                    AI
                </div>

                <h2>
                    How can I help?
                </h2>

                <p>
                    Ask me anything
                    to get started.
                </p>

            </div>

        `;

    }
);


// Auto resize

input.addEventListener(
    "input",
    resizeTextarea
);


function resizeTextarea() {

    input.style.height =
        "auto";


    input.style.height =
        Math.min(
            input.scrollHeight,
            150
        ) + "px";

}


// Enter = send
// Shift + Enter = newline

input.addEventListener(
    "keydown",
    function (event) {


        if (
            event.key === "Enter" &&
            !event.shiftKey
        ) {

            event.preventDefault();

            form.requestSubmit();

        }

    }
);

