<?php
            session_start();
            $_SESSION['base_url'] = "https://betacloud.sellandsign.com";
            $_SESSION['token'] = "";
            $_SESSION['cdi'] = 0;
            $_SESSION['contractor_id'] = 0;
            $_SESSION['actor_id'] = 0;
            
            // $_SESSION['base_url'] = "https://betaqacss.calindasoftware.com";
            // $_SESSION['token'] = "5699057|TklngrkyKWgol6f7nq1qsLez5vL7fJHqXXeTGKF6wy0=";
            // $_SESSION['cdi'] = 1911;
            // $_SESSION['contractor_id'] = 17906;
            // $_SESSION['actor_id'] = 12831;
            
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
           <button onclick="redirect_to_sign()">Sign contract</button>
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

            function redirect_to_sign() {
                const success = encodeURIComponent('https://www.google.com');
                const error = encodeURIComponent('https://bing.com');
                const encoded = encodeURIComponent(infos.token);
                const url = `http://betacloud.sellandsign.com/calinda/sellandsign/#/contract/${infos.contract_id}/sign;c_id=${infos.contract_id};no_ui=true;refback=${success};errorback=${error};j_token=${encoded}`
                console.log(url);
                location.href = url;
            }
        </script>
    </body>
</html>
