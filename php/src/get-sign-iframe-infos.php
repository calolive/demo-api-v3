<?php

require_once 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

session_start();
$infos['cdi'] = $_SESSION['cdi'];
$infos['contract_id'] = $_SESSION['contract_id'];
$infos['token'] = $_SESSION['t_token'];
$infos['contractor_id'] = $_SESSION['contractor_id'];
$infos['customer_id'] = $_SESSION['customer_id'];

$resp_json = array('code' => "200", 'response' => $infos);
//var_dump($resp_json);die;
echo json_encode($resp_json);

?>