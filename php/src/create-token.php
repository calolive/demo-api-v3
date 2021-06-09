<?php

require_once 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

session_start();
$base_url = $_SESSION['base_url'];
$token = $_SESSION['token'];
$cdi = $_SESSION['cdi'];
$contract_id = $_SESSION['contract_id'];
$contractor_id = $_SESSION['contractor_id'];
$customer_id = $_SESSION['customer_id'];
$actor_id = $_SESSION['actor_id'];

$client = new Client([
    // You can set any number of default request options.s
    'timeout'  => 30.0,
]);

$ct_obj = json_decode(file_get_contents(__dir__.'/resources/create-token.apiv3', true), false);
$ct_obj->actorId = $actor_id;
$ct_obj->time = round(microtime(true) * 1000) + 3000000;
$ct_obj->parameters->contract_definition_id = $cdi;
$ct_obj->parameters->contract_id = $contract_id;
$ct_obj->parameters->contractor_id = $contractor_id;
$ct_obj->parameters->customer_id = $customer_id;

//Create token -----------------------------------------------------------------------------
try {
    $resp_ct = $client->request('POST', "$base_url/calinda/hub/createTemporaryToken.action", [
        'headers' => [
            'j_token' => $token,
            'Content-type' => 'application/json',
            'Accept' => 'application/json'
        ],
        'body' => json_encode($ct_obj)
    ]);

    $resp_obj = json_decode($resp_ct->getBody()->getContents());
    $t_token = $resp_obj->token->token;
    $_SESSION['t_token'] = $t_token;
    $resp_json = array('code' => $resp_ct->getStatusCode(), 'response' => "token :  $t_token");
    echo json_encode($resp_json);
}
catch (RequestException $e) {
    $resp_json = array('code' => 400, 'response' => $e->getMessage());   
    echo json_encode($resp_json);
}

?>