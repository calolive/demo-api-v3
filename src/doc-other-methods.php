//<?php
require_once 'vendor/autoload.php';
use GuzzleHttp\Client;

echo '<img src="https://www.docker.com/sites/default/files/horizontal.png">';
echo "<br><br>";

echo <<<EOD
    <div style="text-align: center">
        <button>Créer contrat</button>
        <button>Signer contrat</button>
        <button>Voir contrat signé</button>      
    </div>
EOD;

$base_url = "https://betaqacss.calindasoftware.com";
$token = '5699057|z+n3zhz4gF8+buJCOCS5mGmj+WqjEBrHuBrm2dAfifM=';
$cid = 1911;
$base_url = "https://betacloud.sellandsign.com";
$token = 'DMFLSBX|zwWdYOcj+sNJWDBc10NB7RV/gQzvMKYE9Ryd+CcoCXs=';
$cid = 28844;

$client = new Client([
    // You can set any number of default request options.
    'timeout'  => 30.0,
]);

//Create customer -----------------------------------------------------------
$response = $client->request('POST', "$base_url/calinda/hub/selling/model/customer/update?action=selectOrCreateCustomer", [
    'headers' => [
        'j_token' => $token,
        'Content_Type' => 'application/json',
        'Accept' => 'application/json'

    ],
    'body' => fopen(__DIR__.'/resources/customer.apiv3', 'r')
]);

$customer_arr = json_decode($response->getBody()->getContents(), true);
//var_dump($customer_arr);
echo "<br><br>";
echo "customer number" . $customer_arr['number'];

//Create recipient ----------------------------------------------------------
$recipient_arr = json_decode(file_get_contents(__dir__.'/resources/recipient.apiv3', true), true);
$recipient_arr['customer_number'] = $customer_arr['number'];

$resp_recip = $client->request('POST', "$base_url/calinda/hub/selling/model/contractor/create?action=getOrCreateContractor", [
    'headers' => [
        'j_token' => $token,
        'Content_Type' => 'application/json',
        'Accept' => 'application/json'

    ],
    'body' => json_encode($recipient_arr)
]);

$resp_recip_arr = json_decode($resp_recip->getBody()->getContents(), true);
echo "<br><br>";
echo "numéro de signataire : " . $resp_recip_arr['id'];

//Create contract ----------------------------------------------------------
$contract_arr = json_decode(file_get_contents(__dir__.'/resources/contract.apiv3', true), true);
$contract_arr['date'] = round(microtime(true) * 1000);
$contract_arr['customer_number'] = $customer_arr['number'];
$contract_arr['contract_definition_id'] = $cid;

$resp_contract = $client->request('POST', "$base_url/calinda/hub/selling/model/contract/create?action=createContract", [
    'headers' => [
        'j_token' => $token,
        'Content_Type' => 'application/json',
        'Accept' => 'application/json'

    ],
    'body' => json_encode($contract_arr)
]);

$resp_contract_arr = json_decode($resp_contract->getBody()->getContents(), true);
echo "<br><br>";
echo "numéro de contrat : " . $resp_contract_arr['id'];

//Upload document ----------------------------------------------------------
$resp_upload = $client->request('POST', "$base_url/calinda/hub/selling/do", [
    'query' => [
        'm' => 'uploadContract',
        'id' => $resp_contract_arr['id']
    ],
    'headers' => [
        'j_token' => $token,
        'Content_Type' => 'multipart/form-data',
        'Accept' => 'application/json'

    ],
    'multipart' => [
        [
            'name' => 'file',
            'contents' => fopen(__DIR__.'/resources/1contractant.pdf', 'r')
        ]

    ]
]);

echo "<br><br>";
echo "UPLOAD : " . $resp_upload->getBody()->getContents();

//Add recipient to contract ------------------------------------------------
$rfc_arr = json_decode(file_get_contents(__dir__.'/resources/recipient-for-contract.apiv3', true), true);
$rfc_arr['contract_id'] = $resp_contract_arr['id'];
$rfc_arr['contractor_id'] = $resp_recip_arr['id'];

$resp_rfc = $client->request('POST', "$base_url/calinda/hub/selling/model/contractorsforcontract/insert?action=addContractorTo", [
    'headers' => [
        'j_token' => $token,
        'Content_Type' => 'application/json',
        'Accept' => 'application/json'

    ],
    'body' => json_encode($rfc_arr)
]);

echo "<br><br>";
echo "RFC : " . $resp_rfc->getBody()->getContents();

//Send contract for signature --------------------------------------------
$resp_sign = $client->request('POST', "$base_url/calinda/hub/selling/do", [
    'query' => [
        'm' => 'contractReady',
        'c_id' => $resp_contract_arr['id']
    ],
    'headers' => [
        'j_token' => $token,
        'Accept' => 'application/json'

    ]
]);

SendCommanPacket ------------------------------------------------------
Méthode à privilégier par rapport à la méthoe étape par étape - demande beaucoup moins de travail
$resp_scp = $client->request('POST', "$base_url/calinda/hub/selling/do?m=sendCommandPacket", [
    'headers' => [
        'j_token' => $token
        ],

    'multipart' => [
        [
            'name'     => 'sendcommandpacket.apiv3',
            'contents' => fopen(__DIR__.'/resources/sendcommandpacket.apiv3', 'r'),
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

$resp_scp_arr = json_decode($resp_scp->getBody()->getContents(), true);
echo "<br><br>";
echo "numéro de contrat : " . $resp_scp_arr['contract_id'];

//Contract autoclose ------------------------------------------------------
//$contract_id = $resp_contract_arr['id'];
$contract_id = $resp_scp_arr['contract_id'];
$resp_autoclose = $client->request('POST', "$base_url/calinda/hub/selling/model/contract/update?action=updateContractAutoCloseMode", [
    'headers' => [
        'j_token' => $token,
        'Content_Type' => 'application/json',
        'Accept' => 'application/json'

    ],
    'body' => "{\"auto_close\" : 1 ,\"id\" : $contract_id}"
]);

echo "<br><br>";
echo "AUTOCLOSE : " . $resp_autoclose->getBody()->getContents();

//Get signature status --------------------------------------------------
$resp_ss = $client->request('GET', "$base_url/calinda/hub/selling/model/contractorsforcontract/list", [
    'query' => [
        'action' => 'getContractorsAbout',
        'contract_id' => $contract_id,
        //'contract_id' => 1780855,
        'offset' => 0,
        'size' => 9999
    ],
    'headers' => [
        'j_token' => $token,
        'Accept' => 'application/json'

    ]
]);

echo "<br><br>";
echo "SIGNATURE STATUS : " . $resp_ss->getBody()->getContents();

//Get contract information --------------------------------------------
$resp_ci = $client->request('GET', "$base_url/calinda/hub/selling/model/contract/read", [
    'query' => [
        'action' => 'getContract',
        'contract_id' => $contract_id,
        //'contract_id' => 1780855
    ],
    'headers' => [

        'j_token' => $token,
        'Accept' => 'application/json'

    ]
]);

echo "<br><br>";
echo "CONTRACT INFORMATION : " . $resp_ci->getBody()->getContents();

Get signed contract ------------------------------------------------
Attention, cet appel ne fonctionne qu'une fois que le contrat a été signé et validé
$resp_sc = $client->request('GET', "$base_url/calinda/hub/selling/do", [
    'query' => [
        'm' => 'getSignedContract',
        'contract_id' => 28583,
    ],
    'headers' => [

        'j_token' => $token,
        'Accept' => 'application/json'

    ]
]);

echo "<br><br>";
//echo "SIGNED CONTRACT : " . $resp_sc->getBody()->getContents();
$pdf = "data:application/pdf;base64," . base64_encode($resp_sc->getBody()->getContents());
echo "<iframe id=\"contract\" src=\"$pdf\" height=\"100%\" width=\"100%\" >";

echo "<br>";
echo 'done';


