<?php

require_once 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

session_start();
$base_url = $_SESSION['base_url'];
$token = $_SESSION['token'];
$cdi = $_SESSION['cdi'];
$contractor_id = $_SESSION['contractor_id'];

$client = new Client([
    // You can set any number of default request options.
    'timeout'  => 30.0,
]);

$scp_obj = json_decode(file_get_contents(__dir__.'/resources/sendcommandpacket.apiv3', true), false);
$scp_obj->contract->contract_definition_id = $cdi;
$scp_obj->contractors[0]->id = $contractor_id;

try {
//SendCommanPacket ------------------------------------------------------
//Méthode à privilégier par rapport à la méthoe étape par étape - demande beaucoup moins de travail
    $resp_scp = $client->request('POST', "$base_url/calinda/hub/selling/do?m=sendCommandPacket", [
        'headers' => [
            'j_token' => $token,
            'Accept' => 'application/json'
            ],

        'multipart' => [
            [
                'name'     => 'sendcommandpacket.apiv3',
                'contents' => json_encode($scp_obj),
                'filename' => 'sendcommandpacket.apiv3',
                'headers'  => [
                    'Content-type' => 'application/json'
                ]
            ],
            [
                'name'     => '1contractant.pdf',
                'contents' => fopen(__DIR__.'/resources/1contractant.pdf', 'r'),
                'filename' => '1contractant.pdf',
                'headers'  => [
                    'Content-type' => 'application/pdf'
                ]
            ]
        ]
    ]);

    $resp_obj = json_decode($resp_scp->getBody()->getContents());
    $_SESSION['contract_id'] = $resp_obj->contract_id;
    $_SESSION['customer_id'] = $resp_obj->customer_number;
    $resp_json = array('code' => $resp_scp->getStatusCode(), 'response' => "contract created with id $resp_obj->contract_id and customer $resp_obj->customer_number");
    echo json_encode($resp_json);
}
catch (RequestException $e) {
    $resp_json = array('code' => 400, 'response' => $e->getMessage());   
    echo json_encode($resp_json);
}

?>