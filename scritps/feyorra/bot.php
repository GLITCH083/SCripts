<?php

error_reporting(0);
define("host", "feyorra.top");

function selecting(){
    global $typeapi,$apihost,$apikey,$l;
    clear();
    showBannerBox('Captcha');
    echo WHITE . "[1] Multibot" . WHITE . NEWLINE; 
    echo WHITE . "[2] Xevil" . WHITE . NEWLINE;
    $typeapi = readline("Select Option: ");  
    if($typeapi == 1){
        $apihost = "api.multibot.in";
        $apikey = saveData(host,'Multibot');
    }elseif($typeapi == 2){
        $apihost = "api.sctg.xyz";
        $apikey = saveData(host,'Xevil');
    }else{
        echo RED . "Wrong Choice Try Again!";
        selecting();
    }
}


function header0(){
    return ["accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
        "User-Agent: " . saveData(host,'user_Agent'),
        "Cookie: " . saveData(host,'cookie')
    ];
    }
    function headers($host){
        return ["User-Agent: ".saveData(host,'user_Agent'),
            "Accept: application/json, text/javascript, */*; q=0.01",
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
            "Referer: https://$host/",
            "Origin: https://$host",
//            "x-requested-with: XMLHttpRequest",
            "Cookie: " . saveData(host,'cookie')
    ];
}
function getcode($r){
    global $l;
    $r = explode('.gif"', explode('src="https://feyorra.top/assets/images/captcha/', $r)[1])[0];
    $r = Run("https://feyorra.top/assets/images/captcha/$r.gif", header0());
    file_put_contents("img.gif", $r);
    $capArray = feyorra_Captcha(); // returns array

    foreach ($capArray as $cap) {
        if (strlen($cap) >= 4 && ctype_digit($cap)) {
            return $cap;
        }
    }
    return null;
}

function faucet(){
  global $l;
  $r = Run("https://feyorra.top/faucet",header0());
            if(preg_match('/Just a moment.../', $r)){
        cf_bypass("https://feyorra.top/faucet");
        rewardBox("CF",["msg" => "CF-BYPASSED"]);
        return faucet();
    }
    
  $bal = explode('</h3>', explode('<h3>', $r)[3])[0];  
    if($bal == ""){unlinkData(host,'cookie');SaveData(host,'cookie');return faucet();}
    rewardBox("Dashboard",["Balance" => $bal]);
  
    while(true){    
  $r = Run("https://feyorra.top/faucet",header0());
        if(preg_match('/Just a moment.../', $r)){
        cf_bypass("https://feyorra.top/faucet");
        rewardBox("CF",["msg" => "CF-BYPASSED"]);
        return faucet();
    }
  $timer = explode(' - 1;', explode('let wait = ', $r)[1])[0];  
        if($timer){countdown($timer,"");continue;}
  $csrf = explode('"', explode('<input type="hidden" name="csrf_token_name" id="token" value="', $r)[1])[0];
  $token = explode('"', explode('<input type="hidden" name="token" value="', $r)[1])[0];
  $cap_name = explode('"', explode('name="', $r)[4])[0];
  echo "Bypassing Text Image";      
  $cap = getcode($r);
  echo "\r                          \r";      
  if(empty($cap)){continue;}    
  $req = "csrf_token_name=$csrf&token=$token&$cap_name=$cap";
  $r = Run("https://feyorra.top/faucet/verify",headers('feyorra.top'),$req);
  $r = Run("https://feyorra.top/faucet",header0());
        if(preg_match('/Just a moment.../', $r)){
        cf_bypass("https://feyorra.top/faucet");
        rewardBox("CF",["msg" => "CF-BYPASSED"]);
        return faucet();
    }
  $msg = explode("',", explode("title: '", $r)[1])[0];    
          $bal = explode('</h3>', explode('<h3>', $r)[3])[0];  
    if($bal == ""){unlinkData(host,'cookie');SaveData(host,'cookie');return faucet();}
    if($msg){
        $msg = str_replace('has been added to your balance','',$msg);
        rewardBox("Faucet",["Reward" => $msg,"Text Code" => $cap,"Balance" => $bal]);
        } 
    }        
}


function feyorra_Captcha($path = "img.gif") {
    $gifPath = $path;
    $frameDir = "images/";
    
    if (!file_exists($frameDir)) {
        mkdir($frameDir);
    }
    
    $cmd = "ffmpeg -i $gifPath {$frameDir}frame_%03d.png 2>nul";
    shell_exec($cmd);
    
    $frameFiles = glob($frameDir . "*.png");
    $results = [];
    
    foreach ($frameFiles as $frameFile) {
        $ocr = shell_exec("tesseract $frameFile stdout --psm 11 -c tessedit_char_whitelist=0123456789 2>nul");
        $ocr = trim($ocr);
        if (!empty($ocr)) {
            $results[] = $ocr;
        }
        unlink($frameFile);
    }

    unlink($gifPath);
    
    return $results; // <<< RETURN ALL results!
}
clear();
SaveData(host,'cookie');
saveData(host,'user_Agent');
clear();
showBannerBox(host);
faucet();