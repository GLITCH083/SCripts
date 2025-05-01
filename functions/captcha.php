
<?php
include_once("function.php");

function captcha($url, $sitekey, $apihost, $apikey, $method){
    $max_attempt = 100;
    $i = 0;
    $capurl = "http://$apihost/in.php?key=$apikey&method=$method&sitekey=$sitekey&pageurl=$url";
    $data = http_build_query([
        'apikey' => $apikey,
        'pageurl' => $url,
        'method' => $method,
        'sitekey' => $sitekey,
    ]);
    $res = Run($capurl, $data);
    
    $parts = explode('OK|', $res);
       if (count($parts) > 1) {
           $task = $parts[1];
           animation("Captcha ID successfully Created [$task]");
           if ($task) {
               while (true) {
                if($i == $max_attempt){
                    return;
                } 
                $request_url = "http://$apihost/res.php?key=$apikey&action=get&id=$task";
                $res = Run($request_url);
                $result_parts = explode('OK|', $res);
                if (count($result_parts) > 1) {
                    $res = $result_parts[1];
                    $success++; // Increment success counter
                    animation("Captcha Successfully Get At Attempt [$i/$max_attempt]");
                    return $res; // Return the solved CAPTCHA
                } elseif ($res == "ERROR_CAPTCHA_UNSOLVABLE" || $res == "ERROR_BAD_DATA" || $res == "ERROR_WRONG_USER_KEY" || $res == "ERROR_ZERO_BALANCE" || $res == "ERROR_SITEKEY" || $res == "SITEKEY_IS_INCORRECT" || $res == 'WRONG_RESULT' || $res == "HCAPTCHA_NOT_FOUND" || $res == "TURNSTILE_NOT_FOUND") {
                    echo GREY . "ERROR" . WHITE . " > " . RED . $res . NEWLINE;
                    flush();
                    return false; // Return false if the CAPTCHA is unsolvable
                } else {
                    animation("Getting Captcha Attempt [$i/$max_attempt]");
                    $i++;
                    flush();
                    sleep(3);
                }
            }
        }
    }
    return false; // Return false if no task was created
}

function antibot1($r) {
    global $apikey, $apihost;
    $i = 0;
    $max_attempt = 20;
        $url = "http://$apihost/in.php"; // Use double quotes for variable interpolation
    
        // Extract captcha information
        $bot1 = isset(explode('\"', explode('rel=\"', $r)[1])[0]) ? explode('\"', explode('rel=\"', $r)[1])[0] : '';
        $bot2 = isset(explode('\"', explode('rel=\"', $r)[2])[0]) ? explode('\"', explode('rel=\"', $r)[2])[0] : '';
        $bot3 = isset(explode('\"', explode('rel=\"', $r)[3])[0]) ? explode('\"', explode('rel=\"', $r)[3])[0] : '';
        $bot4 = isset(explode('\"', explode('rel=\"', $r)[4])[0]) ? explode('\"', explode('rel=\"', $r)[4])[0] : '';

        if (!$bot1 || !$bot2 || !$bot3) {
            return 0;
        }
    
        // Extract main and additional captcha images
        $main = explode('"', explode('data:image/png;base64,', $r)[1])[0];
        $img1 = explode('"', explode('data:image/png;base64,', $r)[2])[0];
        $img2 = explode('"', explode('data:image/png;base64,', $r)[3])[0];
        $img3 = explode('"', explode('data:image/png;base64,', $r)[4])[0];
        $img4 = explode('"', explode('data:image/png;base64,', $r)[4])[0];

        // Prepare data for API request
        $data = array(
            'key' => $apikey,
            'method' => 'antibot',
            'main' => $main,
            $bot1 => $img1,
            $bot2 => $img2,
            $bot3 => $img3,
            $bot4 => $img4
        );
    print_r($data);
        // Set up HTTP request options
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
    
        $context = stream_context_create($options);
    
        $response = file_get_contents($url, false, $context);
    
        $task = isset(explode('OK|', $response)[1]) ? explode('OK|', $response)[1] : '';
    
        // Handle captcha solving result
        if ($task) {
            animation("Captcha ID successfully Created [$task]");
            while (true) {
                if($i == $max_attempt){
                    return;
                }
                // Check captcha solving status
                $r2 = file_get_contents("http://$apihost/res.php?key=".$apikey."&id=".$task);
print_r($r2); echo NEWLINE;
                $hasil = isset(explode('OK|', $r2)[1]) ? explode('OK|', $r2)[1] : '';
                $antb = explode(',', $hasil);
    
                if ($hasil) {
                    animation("Captcha Successfully Get At Attempt  [$i/$max_attempt]");
                    return "+" . implode("+", $antb);
                    break;
                } else if ($r2 == "CAPCHA_NOT_READY") {
                    animation("Getting Captcha Attempt  [$i/$max_attempt]");
                    $i++;
                    sleep(3);
                } else {
                    return 0;
                }
            }
        } else {
            return 0;
        }
    }



    function image($apikey,$apihost,$body,$method){
        $max_attempt = 25;
        $i = 0;
        $capurl = "http://$apihost/in.php";
        $data = http_build_query([
            'key' => $apikey,
            'method' => $method,
            'body' => $body,
        ]);
    
        $res = Run($capurl,'Content-Type: application/x-www-form-urlencoded; charset=UTF-8', $data);
        $parts = explode('OK|', $res);
        if (count($parts) > 1) {
            $task = $parts[1];
            animation("Captcha ID successfully Created [$task]");
            if ($task) {
                while (true) {
                 if($i == $max_attempt){
                     return;
                 } 
                 $request_url = "http://$apihost/res.php?key=$apikey&action=get&id=$task";
                 $res = Run($request_url);
                 $result_parts = explode('OK|', $res);
                 if (count($result_parts) > 1) {
                     
                    $res = $result_parts[1];

                     
                     animation("Captcha Successfully Get At Attempt [$i/$max_attempt]");
                     return $res;
                 } elseif ($res == "ERROR_CAPTCHA_UNSOLVABLE") {
                     $failed++;
                     flush();
                     return false;
                 } else {
                     animation("Getting Captcha Attempt [$i/$max_attempt]");
                     $i++;
                 }
             }
         }
     }
     return false;
    }


function fetchApiBalance($typeapi, $apikey) {
    if ($typeapi == 2) {
        $response = file_get_contents('https://api.sctg.xyz/res.php?key=' . $apikey . '&action=getbalance');
        return $response;
    } else {
        $response = file_get_contents('http://api.multibot.in/res.php?key=' . $apikey . '&action=userinfo');
        $data = json_decode($response, true);
        return $data['balance'];
    }
}

function generateUUID() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return sprintf(
        '%08s-%04s-%04s-%04s-%12s',
        bin2hex(substr($data, 0, 4)),
        bin2hex(substr($data, 4, 2)),
        bin2hex(substr($data, 6, 2)),
        bin2hex(substr($data, 8, 2)),
        bin2hex(substr($data, 10, 6))
    );
}


function IconCaptchaBypass($data,$host,$capu,$webkit,$cookie,$userAgent){
    global $cookie, $userAgent;
        $nn = 0;
        $initTime = round(microtime(true)*1000) - rand(54, 97);
        $iconToken = explode("' />", explode("<input type='hidden' name='_iconcaptcha-token' value='", $data)[1])[0];
        $uuid = generateUUID();
        $headers = [
            'content-type: multipart/form-data; boundary=----'. $webkit,
            'x-requested-with: XMLHttpRequest',
            'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            "x-iconcaptcha-token: ".$iconToken,
            'accept: */*',
            'origin: https://'.$host,
            'sec-fetch-site: same-origin',
            'sec-fetch-mode: cors',
            'sec-fetch-dest: empty',
            'referer: https://'.$host,
            'accept-language: en-GB,en-US;q=0.9,en;q=0.8',
            "cookie: ".$cookie,
            'priority: u=1, i'
        ];
        $url = $capu;
        $payload = base64_encode(json_encode(["widgetId" => $uuid, "action"  => "LOAD", "theme" => "light","token" =>  $iconToken, "timestamp"  => round(microtime(true)*1000), "initTimestamp" => $initTime]));
        $request = '------' . $webkit . '
Content-Disposition: form-data; name="payload"

'.$payload.'
------' . $webkit . '--';
        $data = json_decode(base64_decode(Run($url, $headers, $request)), true);
        $challengeId = $data['identifier'];
        $captcha = [20, 60, 100, 140, 180, 220, 260, 300];

        foreach($captcha as $key => $x){
            $result = ["x" => $x, "y" => rand(25, 30), "w" => 320];
            $payload = encodePayload(["widgetId" => $uuid, "challengeId" => $data['identifier'], "action" => "SELECTION", "x" => $result['x'], "y" => $result['y'], "width" => $result['w'],"token" => $iconToken]);
            $url = $capu;
            $request = '------' . $webkit . '
Content-Disposition: form-data; name="payload"

'.$payload.'
------' . $webkit . '--';
            $data = Run($url, $headers, $request);
            $data = json_decode(base64_decode($data), true);
            if($data['completed']){
                return "captcha=icaptcha&_iconcaptcha-token={$iconToken}&ic-rq=1&ic-wid={$uuid}&ic-cid=$data[identifier]&ic-hp=";
            }
            $nn++;
        }
        return false;
    }


    function captchasolver($captcha,$cookie,$ua)
    { global $userAgent;
     $header0 = [
          "Host: earnbitmoon.club",
          "x-requested-with:XMLHttpRequest",
          "content-type: multipart/form-data; boundary=------WebKitFormBoundaryPvNIrj9zw1N1kh5O",
          "origin: https://earnbitmoon.club",
          "User-Agent: ". $ua,
          "accept: */*", //*/
          "referer: https://earnbitmoon.club/",
          "cookie: $cookie",
                   ];

     $header1 = [
          "Host: earnbitmoon.club",
          "content-type: multipart/form-data; boundary=----WebKitFormBoundaryPXIjpA5uCgwszbBB",
          "x-requested-with:XMLHttpRequest",
          "origin: https://earnbitmoon.club",
          "User-Agent: ".$ua,
          "accept: */*", //*/
          "referer: https://earnbitmoon.club/",
          "cookie: $cookie",
                    ];

     $header2 = [
          "Host: earnbitmoon.club",
          "user-agent: ".$ua,
          "accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8",
          "referer: https://earnbitmoon.club/",
          "cookie: $cookie",
                   ];

     $url = "https://earnbitmoon.club/system/libs/captcha/request.php";
     $payload = '------WebKitFormBoundaryPXIjpA5uCgwszbBB
Content-Disposition: form-data; name="payload"


eyJpIjoxLCJhIjoxLCJ0IjoiZGFyayIsInRzIjoxNjg3NDgzNDg5MjE3fQ==
------WebKitFormBoundaryPXIjpA5uCgwszbBB--';
     $r = Run($url,$header1,$payload);
     if (strpos($r, 'error') !== false) {
         $js = json_decode($r, true);
         $sec = $js["data"];
         $refresher=sleep(($sec/1000)+15);
                                            }
                                            $showbypass = captchashow();
     $link = "https://earnbitmoon.club/system/libs/captcha/request.php?payload=eyJpIjoxLCJ0cyI6MTY4NzQ4MzQ5MDIzM30=";
     $r = Run($link,$header2);

     $payload = base64_encode('{"i":1,"x":'.explode(",",$captcha)[0].',"y":'.explode(",",$captcha)[1].',"w":'.explode(",",$captcha)[2].',"a":2,"ts":'.round(microtime(true))*1000 .'}');
     $link = "https://earnbitmoon.club/system/libs/captcha/request.php";
     $request = '--------WebKitFormBoundaryPvNIrj9zw1N1kh5O
Content-Disposition: form-data; name="payload"


'.$payload.'
--------WebKitFormBoundaryPvNIrj9zw1N1kh5O--';
     $r = Run($link,$header0,$request);
   }
   function captcha1()
   {
   $captcha[] = "74,30,314.828";//2nd block
   $captcha[] = "128,23,314.828";//3rd block
   $captcha[] = "184,28,314.828";//4th block
   $captcha[] = "287,22,314.828";//5th block
   $captcha[] = "278,33,314.824";//6th block*/
   return($captcha);
   }

   function captchashow(){
       echo GREEN . "Bypassing Captcha\r";
       echo GLOWING_WHITE . "                            \r";
   }
  



   function encodePayload($payload){
    return base64_encode(json_encode(array_merge($payload, ["timestamp" => round(microtime(true) * 1000), "initTimestamp" => round(microtime(true) * 1000) - rand(5, 10)])));
}