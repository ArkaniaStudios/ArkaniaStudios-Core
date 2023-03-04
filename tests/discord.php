<?php

/*function setDiscordGameActivity($clientId, $activityName, $activityType = 0, $activityUrl = "") {
    // Set up the API endpoint URL
    $url = "https://discord.com/api/v9/users/$clientId/settings";

    // Set up the headers for the API request
    $headers = [
        "Content-Type: application/json",
        "Authorization: " // Replace YOUR_BOT_TOKEN with your actual bot token
    ];

    // Set up the data to send in the API request
    $data = [
        "status" => "online",
        "custom_status" => [
            "text" => $activityName,
            "emoji_name" => "",
            "emoji_id" => ""
        ],
        "activities" => [
            [
                "name" => $activityName,
                "type" => $activityType,
                "url" => $activityUrl
            ]
        ]
    ];

    // Send the API request using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    // Return the API response as an array
    return json_decode($result, true);
}

$clientID = "380325895338065920"; // Replace with the client ID of the user you want to modify the game activity for
$activityName = "Test activité"; // Replace with the name of the game activity you want to set
$activityType = 0; // Replace with the type of the game activity you want to set (0 = playing, 1 = streaming, 2 = listening, 3 = watching)
$activityUrl = ""; // Replace with the URL of the stream you want to set (if activityType is set to 1)

$result = setDiscordGameActivity($clientID, $activityName, $activityType, $activityUrl);
var_dump($result);*/


// Replace YOUR_WEBHOOK_URL_HERE with your Discord webhook URL
$webhook_url = "https://discord.com/api/webhooks/1080089314530164756/V2lLDuom_uv_2PH--bgYVONnyESXmusFBB5Ee2kCEAxeKo_6Xdh8Fht2tGw-RbAQWIvZ";

// Function to send a message to Discord
function send_discord_message($message)
{
    global $webhook_url;

    $data = array("content" => $message);
    $options = array(
        "http" => array(
            "header" => "Content-Type: application/json",
            "method" => "POST",
            "content" => json_encode($data)
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($webhook_url, false, $context);
    return $result;
}

// Function to edit a message on Discord
function edit_discord_message($message_id, $new_message): bool|string {
    global $webhook_url;

    $data = array("content" => $new_message);
    $options = array(
        "http" => array(
            "header" => "Content-Type: application/json",
            "method" => "PATCH",
            "content" => json_encode($data)
        )
    );
    $url = $webhook_url . "/messages/" . $message_id;
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}

// Example usage
$message = "Hello, World!";
$response = send_discord_message($message);
$message_id = json_decode($response)->id;
$new_message = "Hello, World! (edited)";
$response = edit_discord_message($message_id, $new_message);
echo $response;


// Définir l'URL du webhook
$webhookUrl = "https://discord.com/api/webhooks/1080153437972475926/vfgzzbCes2Bl9LT2qQtb5q2KWPkvCjld6Mjg0olYKMdyTuXjfzOEbAjhp_gzWxIzaqxr";

// Créer un tableau avec les informations à envoyer
$data = array(
    "content" => "Bonjour, ceci est un message envoyé depuis un webhook Discord en utilisant PHP."
);

// Encodage des données au format JSON
$dataJson = json_encode($data);

// Configurer la requête HTTP POST
$options = array(
    "http" => array(
        "header"  => "Content-Type: application/json\r\n",
        "method"  => "POST",
        "content" => $dataJson
    )
);

// Créer un contexte HTTP pour la requête
$context = stream_context_create($options);

// Envoyer la requête POST au webhook
$result = file_get_contents($webhookUrl, false, $context);

// Vérifier si l'envoi a réussi
if ($result === false) {
    echo "Une erreur s'est produite lors de l'envoi du message au webhook.";
} else {
    echo "Le message a été envoyé avec succès au webhook.";
}
