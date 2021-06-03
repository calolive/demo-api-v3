<?php
            session_start();
            $_SESSION['base_url'] = "https://betacloud.sellandsign.com";
            $_SESSION['token'] = "TSRSSBX|/ZzL2clktVntIpEBN5kZikFQmG/+WOkLL8zaNJeaLVU=";
            $_SESSION['cdi'] = 25697;
            $_SESSION['contractor_id'] = 1144118;
            
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    </head>
    <body>
        <img src="https://www.docker.com/sites/default/files/horizontal.png">
        <br><br>
        <div style="text-align: center">
           <button onclick="send_request('create-contract')">Créer contrat</button>
           <button onclick="send_request('autoclose')">Autoclose</button>
           <button onclick="send_request('get-status')">Get status</button>
           <button onclick="send_request('create-token')">Create token</button>
           <button onclick="send_request('get-sign-iframe-infos')">Get iframe info</button>
           <button onclick="disp_sign_iframe()">Sign contract</button>
           <button onclick="send_request('get-signed-contract')">Voir contrat signé</button>      
        </div>
        <br><br>
        <div id="messagediv"></div>
        <div id="iframediv" style="height:800px"></div>
        <script>
            var infos = null;

            window.addEventListener("message", function(message) {
                if (message && message.data) {
                    const data = JSON.parse(message.data);
                    console.log(data);
                    const iframe = document.getElementById('iframediv');
                    iframe.innerHTML = "";
                }
            }, false);

            function send_request(endpoint) {
                var objXMLHttpRequest = new XMLHttpRequest();
                objXMLHttpRequest.onreadystatechange = function() {
                    if(objXMLHttpRequest.readyState === 4) {
                        if(objXMLHttpRequest.status === 200) {    
                            console.log(objXMLHttpRequest.responseText);                     
                            const myjson = JSON.parse(objXMLHttpRequest.responseText);
                            const iframe = document.getElementById('iframediv');
                            let message = document.getElementById('messagediv');

                            if (endpoint === 'get-signed-contract') {
                                if (myjson.code == 200) {
                                    message.innerHTML =  "";
                                    iframe.innerHTML = `<iframe src="${myjson.pdffile}" height="100%" width="100%"></iframe>`;
                                }
                                else {
                                    message.innerHTML = `<p>request error. Message : ${myjson.pdffile}`;
                                    iframe.innHTML = "";                              
                                }
                            }
                            else {
                                iframe.innerHTML = "";
                                message.innerHTML = `<p>${myjson.response}<p>`;
                                infos = myjson.response;
                            }
                        } else {
                            alert('Error Code: ' +  objXMLHttpRequest.status);
                            alert('Error Message: ' + objXMLHttpRequest.statusText);
                        }
                    }
                }
                objXMLHttpRequest.open('GET', `${endpoint}.php`);
                objXMLHttpRequest.send();               
            }

            function disp_sign_iframe() {
                const message = document.getElementById('messagediv');
                const iframe = document.getElementById('iframediv');
                message.innerHTML = "";
                const encoded = encodeURIComponent(infos.token);
                const url = `https://betacloud.sellandsign.com/calinda/s/generic_sign_contract_index.html?l_id=14671&direct_contract=${infos.cdi}&cd_id=${infos.cdi}&c_id=${infos.contract_id}&customer_number=${infos.customer_id}&page=1&no_ui=true&j_token=${encoded}`;                              
                iframe.innerHTML = `<iframe width="1024" height="800" src="${url}"></iframe>`;
            }
        </script>
    </body>
</html>
