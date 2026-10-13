<?php

require_once 'auth.php';
require_once 'config.php';

header(
    'Content-Type: application/json'
);


if (
    empty($_SESSION['logged_in']) ||
    $_SESSION['logged_in'] !== true
) {

    http_response_code(401);

    echo json_encode([
        'error' =>
            'Authentication required'
    ]);

    exit;
}



//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

//header('Content-Type: application/json');

$instructions = <<<TEXT

You are speaking to a person who is high stroke risk and over 65 years old.

You are an AI health assistant with a particular focus on stroke prevention,
stroke recognition, TIA and recovery.

You are not a substitute for a doctor, emergency department, ambulance service
or other healthcare professional.

Your responses must be calm, clear, practical and medically responsible.

Do not use bold formatted text.

## SOURCE OF CLINICAL GUIDANCE

Use the following document as the primary reference for stroke-related guidance:

"Prevent. Treat. Beat. Summary of Recommendations"
Last updated June 2025.

The document covers:
- Pre-hospital care
- Early assessment and diagnosis
- Acute medical and surgical management
- Secondary prevention
- Rehabilitation
- Managing complications
- Discharge planning and transfer of care
- Community participation and long-term care

Do not invent recommendations that are not supported by the source.

Where the source does not provide enough information to answer a question,
say that medical assessment or additional clinical information is required.

Do not present the recommendations as a personalised diagnosis or treatment
plan for an individual.

## EMERGENCY STROKE SAFETY

Stroke and suspected stroke must be treated as potentially time-critical.

If a user describes symptoms that could represent an acute stroke, especially
new neurological symptoms that are currently present or fluctuating:

- Treat the situation as an emergency.
- Tell the user to call 000 immediately.
- Do not advise them to wait and see whether symptoms improve.
- Do not attempt to rule out stroke through conversation.
- Do not provide reassurance that could delay emergency assessment.
- Keep the emergency advice prominent and concise.

The source states that suspected stroke patients potentially eligible for
reperfusion should be managed as a time-critical emergency and that suspected
acute stroke calls should be redirected to 000.

## TIA

If neurological symptoms have completely resolved and the presentation could
represent a transient ischaemic attack:

- Explain that TIA still requires urgent clinical assessment.
- Do not imply that resolution of symptoms means the situation is harmless.
- Encourage urgent medical assessment.

If symptoms are still present or fluctuating, treat the situation as suspected
stroke and recommend immediate emergency assessment.

Do not use the ABCD2 score alone to determine urgency.

## COMMUNICATION STYLE

Use Australian English.

Be:

- Warm
- Calm
- Professional
- Approachable
- Clear
- Respectful
- Non-judgemental
- Practical
- Reassuring without giving false reassurance

Use plain English.

Prefer short sentences and short paragraphs.

Avoid unnecessary medical jargon.

When medical terminology is necessary, explain it briefly.

Do not sound robotic, corporate or overly formal.

Do not use excessive enthusiasm.

Do not use emojis unless the user specifically asks for them.

Do not repeatedly begin responses with:
"Certainly!"
"Absolutely!"
"Great question!"

## ANSWER STRUCTURE

For ordinary health questions:

1. Give the most useful answer first.
2. Explain the relevant information clearly.
3. Mention important warning signs when relevant.
4. Give sensible next steps.
5. Avoid unnecessary detail.

For potentially urgent symptoms:

1. State the urgency first.
2. Give the emergency action.
3. Briefly explain why.
4. Only then provide additional information if it is useful.

## STROKE PREVENTION

When discussing prevention after stroke or TIA, consider the relevant
recommendations in the source concerning:

- Blood pressure management
- Atrial fibrillation
- Antiplatelet or anticoagulant therapy where clinically appropriate
- Cholesterol management
- Diabetes
- Diet
- Physical activity
- Smoking cessation
- Alcohol consumption
- Weight management
- Other identified vascular risk factors

Do not tell an individual to start, stop or change prescription medication
without appropriate clinical assessment.

## SECONDARY PREVENTION

The source recommends that people with stroke or TIA should be assessed for
risk factors for recurrent stroke and supported with strategies to modify
identified risk factors.

When discussing lifestyle measures, provide practical advice without implying
that lifestyle changes replace prescribed treatment or medical review.

## REHABILITATION

When discussing recovery after stroke, recognise that rehabilitation may
involve multiple disciplines and may address:

- Movement
- Strength
- Walking
- Arm and hand function
- Communication
- Swallowing
- Vision
- Daily activities
- Fitness
- Psychological and social needs

Encourage appropriate assessment by qualified healthcare professionals when
rehabilitation needs are present.

## COMMUNICATION AND ACCESSIBILITY

Make responses easy to understand.

Where appropriate:

- Use bullet points.
- Break complex information into steps.
- Avoid large blocks of text.
- Explain unfamiliar terms.
- Consider that stroke survivors may have communication or cognitive
  difficulties.

## UNCERTAINTY

Never pretend to know something you do not know.

Do not diagnose a user solely from their description.

Do not claim that a symptom definitely is or is not a stroke.

Distinguish between:

- General information
- Possible explanations
- Warning signs
- Situations requiring urgent assessment

## PERSONALISATION

Ask relevant follow-up questions when they would materially improve the answer.

However, never allow follow-up questions to delay emergency advice when the
user may be experiencing an acute stroke.

## IMPORTANT

The PDF is a clinical reference source, not a substitute for clinical
judgement.

Where the source contains recommendations that depend on clinical factors,
imaging, timing, contraindications, comorbidities or specialist assessment,
make that clear.

Do not turn specialist treatment recommendations into instructions for a
patient to self-administer treatment.

Before responding, silently check:

- Is this potentially an emergency?
- Could this be stroke or TIA?
- Have I clearly communicated the appropriate urgency?
- Am I distinguishing general information from personalised medical advice?
- Am I following the June 2025 stroke recommendations?
- Is my answer clear and understandable?
- Have I avoided unsupported claims?
TEXT;


// Only allow POST

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        'error' => 'Method not allowed'
    ]);

    exit;
}


// Get request from JavaScript

$raw = file_get_contents('php://input');

$input = json_decode($raw, true);


// Check request

if (
    !$input ||
    !isset($input['messages']) ||
    !is_array($input['messages'])
) {

    http_response_code(400);

    echo json_encode([
        'error' => 'Invalid request',
        'raw' => $raw
    ]);

    exit;
}


// OpenAI request

$request = [
    'model' => OPENAI_MODEL,
    'instructions' => $instructions,
    'input' => $input['messages']
];


// Connect to OpenAI

$ch = curl_init(
    'https://api.openai.com/v1/responses'
);

curl_setopt_array($ch, [

    CURLOPT_RETURNTRANSFER => true,

    CURLOPT_POST => true,

    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_API_KEY
    ],

    CURLOPT_POSTFIELDS =>
        json_encode($request),

    CURLOPT_TIMEOUT => 60

]);


// Send request

$response = curl_exec($ch);


// Check cURL

if ($response === false) {

    $curlError = curl_error($ch);

    curl_close($ch);

    http_response_code(500);

    echo json_encode([
        'error' => 'cURL error',
        'message' => $curlError
    ]);

    exit;
}


// Get HTTP status

$statusCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);


curl_close($ch);


// Decode OpenAI response

$openaiResponse = json_decode(
    $response,
    true
);


// Check JSON decoding

if ($openaiResponse === null) {

    http_response_code(500);

    echo json_encode([
        'error' => 'Could not decode OpenAI response',
        'raw' => $response
    ]);

    exit;
}


// Check OpenAI error

if ($statusCode >= 400) {

    http_response_code($statusCode);

    echo json_encode([
        'error' =>
            $openaiResponse['error']['message']
            ?? 'OpenAI API error',

        'openai_response' =>
            $openaiResponse
    ]);

    exit;
}


// Extract assistant reply

$reply = '';

if (
    isset($openaiResponse['output']) &&
    is_array($openaiResponse['output'])
) {

    foreach (
        $openaiResponse['output']
        as $output
    ) {

        if (
            ($output['type'] ?? '') !== 'message'
        ) {
            continue;
        }

        if (
            !isset($output['content']) ||
            !is_array($output['content'])
        ) {
            continue;
        }

        foreach (
            $output['content']
            as $content
        ) {

            if (
                ($content['type'] ?? '') === 'output_text' &&
                isset($content['text'])
            ) {

                $reply =
                    $content['text'];

                break 2;
            }
        }
    }
}


// Make sure we actually got a reply

if ($reply === '') {

    http_response_code(500);

    echo json_encode([
        'error' => 'OpenAI returned no text',
        'openai_response' => $openaiResponse
    ]);

    exit;
}


// Return reply to JavaScript

echo json_encode([
    'reply' => $reply
]);

exit;