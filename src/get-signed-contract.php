<?php

require_once 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

session_start();
$base_url = $_SESSION['base_url'];
$token = $_SESSION['token'];
$contract_id = $_SESSION['contract_id'];

$client = new Client([
    // You can set any number of default request options.
    'timeout'  => 30.0,
]);

//Get signed contract ------------------------------------------------
try {
    $resp_sc = $client->request('GET', "$base_url/calinda/hub/selling/do", [
        'query' => [
            'm' => 'getSignedContract',
            //'contract_id' => 28583,
            'contract_id' => $contract_id
        ],
        'headers' => [

            'j_token' => $token,
            'Accept' => 'application/json'

        ]
    ]);

    $pdf = "data:application/pdf;base64," . base64_encode($resp_sc->getBody()->getContents());
    $resp_json = array('code' => $resp_sc->getStatusCode(), 'pdffile' => $pdf);
    echo json_encode($resp_json);
}
catch (RequestException $e) {
    $resp_json = array('code' => 400, 'pdffile' => $e->getMessage());   
    echo json_encode($resp_json);
}

?>