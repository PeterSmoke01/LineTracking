<?php
    $LINEData = file_get_contents('php://input');
    $jsonData = json_decode($LINEData,true);
    $replyToken = $jsonData["events"][0]["replyToken"];
    $text = $jsonData["events"][0]["message"]["text"];
    $userId = $jsonData['events'][0]['source']['userId'];
    $utype = $jsonData['events'][0]['type'];
    
    function sendMessage($replyJson,$token){
        $datasReturn = [];
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $token['URL'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $replyJson,
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$token['AccessToken'],
            "cache-control: no-cache",
            "content-type: application/json; charset=UTF-8",
        ),
        ));

        $result = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $datasReturn['result'] = 'E';
            $datasReturn['message'] = $err;
        } else {
            if($result == "{}"){
            $datasReturn['result'] = 'S';
            $datasReturn['message'] = 'Success';
            }else{
            $datasReturn['result'] = 'E';
            $datasReturn['message'] = $result;
            }
        }

        return $result;
    }

    if ($text == "ติดตามการขนส่งสินค้า") {
        include("reply_msg/sellOrder_msg.php");
        file_put_contents("log.txt", "sellOrder_msg.php called" . PHP_EOL, FILE_APPEND);
    } if (preg_match('/^[a-zA-Z0-9-]+$/', $text)) {
        $trackingNumber = $text;
        include("reply_msg/testTracking_msg.php");
        file_put_contents("log.txt", "testTracking_msg.php called with tracking number $trackingNumber" . PHP_EOL, FILE_APPEND);
    } 
    // else {
    //     include("reply_msg/sellOrder_msg.php");
    //     file_put_contents("log.txt", "sellOrder_msg.php called" . PHP_EOL, FILE_APPEND);
    // }
    

    $replymessage = json_decode($message);

    $lineData['URL'] = "https://api.line.me/v2/bot/message/reply";
    $lineData['AccessToken'] = "yemp33+Yjy1ZwaYMwzGvFL7TH0cxqQLFkUADgFDF7hIhhmV9z+Sc3UWeIn/13SHYWPZEzBPheQhi7wKTTGz7aytRXRBZMvAUIr/MsTWgrNNcbi/uFVEkbmhXyZ4Xd1AQZJjSP6Qkv9ASzdmPfi9zdwdB04t89/1O/w1cDnyilFU=";
    $replyJson["replyToken"] = $replyToken;
    $replyJson["messages"][0] = $replymessage;
    $encodeJson = json_encode($replyJson);
    $results = sendMessage($encodeJson,$lineData);

    // Log การส่งข้อความ
    file_put_contents('log.txt', 'Sending message: ' . $encodeJson . PHP_EOL, FILE_APPEND);
    file_put_contents('log.txt', 'Response from LINE: ' . $results . PHP_EOL, FILE_APPEND);

    http_response_code(200);
?>