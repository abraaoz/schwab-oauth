<?php
session_start();

function parseJwt($token)
{
    $base64Url = explode('.', $token)[1] ?? '';
    $base64 = str_replace(['-', '_'], ['+', '/'], $base64Url);
    $jsonPayload = base64_decode($base64);
    return json_decode($jsonPayload, true);
}

$redirect_uri = "https://oauth.codeedge.com.br";
$queryParams = $_GET;

if (isset($queryParams['client_id'], $queryParams['client_secret'])) {
    $_SESSION['client_id'] = $queryParams['client_id'];
    $_SESSION['client_secret'] = $queryParams['client_secret'];
    header("Location: https://api.schwabapi.com/v1/oauth/authorize?client_id={$queryParams['client_id']}&redirect_uri={$redirect_uri}");
    exit;
} else if (isset($queryParams['code'], $queryParams['session'])) {
    $client_id = $_SESSION['client_id'] ?? '';
    $client_secret = $_SESSION['client_secret'] ?? '';

    if ($client_id && $client_secret) {
        $response = file_get_contents("https://api.schwabapi.com/v1/oauth/token", false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Authorization: Basic ' . base64_encode("$client_id:$client_secret"),
                    'Content-Type: application/x-www-form-urlencoded'
                ],
                'content' => http_build_query([
                    'grant_type' => 'authorization_code',
                    'code' => $queryParams['code'],
                    'redirect_uri' => $redirect_uri
                ])
            ]
        ]));

        $json = json_decode($response, true);
        if ($json) {
            $decoded_id_token = parseJwt($json['id_token'] ?? '');
            $token_json = [
                "creation_timestamp" => $decoded_id_token['iat'] ?? null,
                "token" => array_merge($json, ["expires_at" => $decoded_id_token['exp'] ?? null])
            ];
            header("Content-Type: application/json");
            echo json_encode($token_json, JSON_PRETTY_PRINT);
        } else {
            echo "Error retrieving token.";
        }
        session_destroy();
        exit;
    }
} else {
    echo <<<HTML
    <form>
        <label>
            client_id:
            <input type="text" name="client_id" value="{$_SESSION['client_id']}" />
        </label>
        <label>
            client_secret:
            <input type="text" name="client_secret" value="{$_SESSION['client_secret']}" />
        </label>
        <button type="submit">Generate Token</button>
    </form>
    HTML;
}
