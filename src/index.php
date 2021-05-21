<?php
            session_start();
            $_SESSION['base_url'] = "https://betaqacss.calindasoftware.com";
            $_SESSION['token'] = "";
            $_SESSION['cdi'] = 0;
?>
<!DOCTYPE html>
<html>
    <body>
        <img src="https://www.docker.com/sites/default/files/horizontal.png">
        <br><br>
        <div style="text-align: center">
           <button onclick="send_request('create-contract')">Créer contrat</button>
           <button onclick="send_request('autoclose')">Autoclose</button>
           <button onclick="send_request('get-status')">Get status</button>
           <button onclick="send_request('get-signed-contract')">Voir contrat signé</button>      
        </div>
        <br><br>
        <div id="messagediv"></div>
        <div id="iframediv" style="height: 600px"></div>
        <script>
            function send_request(endpoint) {
                var objXMLHttpRequest = new XMLHttpRequest();
                objXMLHttpRequest.onreadystatechange = function() {
                    if(objXMLHttpRequest.readyState === 4) {
                        if(objXMLHttpRequest.status === 200) {                          
                            let myjson = JSON.parse(objXMLHttpRequest.responseText);
                            let iframe = document.getElementById('iframediv');
                            let message = document.getElementById('messagediv');

                            if (endpoint === 'get-signed-contract') {
                                if (myjson.code == 200) {
                                    message.innerHTML =  "";
                                    iframe.innerHTML = `<iframe src="${myjson.pdffile}" height="100%" width="100%">`;
                                }
                                else {
                                    message.innerHTML = `<p>request error. Message : ${myjson.pdffile}`;
                                    iframe.innHTML = "";                              
                                }
                            }
                            else {
                                iframe.innerHTML = "";
                                message.innerHTML = `<p>${myjson.response}<p>`;
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
        </script>
    </body>
</html>
