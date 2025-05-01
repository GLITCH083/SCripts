<?php

error_reporting(0);
define("host", "earnbitmoon.club");

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

function dashboard(){
    global $l,$apikey,$typeapi,$apihost;
    $url = "https://earnbitmoon.club/";
    $r = Run($url, header0());
    $bal = explode(' Coins</b>', explode('<b id="sidebarCoins">',$r)[1])[0];
    if ($bal == ""){unlinkData(host,"cookie");saveData(host,'cookie');return dashboard();}
    $usd = explode('</b>', explode('<b>', $r)[1])[0];
    $user = explode('</font>', explode('<font class="text-success">',$r)[1])[0];
    $claim = explode('</b>', explode('<b>', $r)[4])[0];
    $totalclaim = explode('</b>', explode('<b>', $r)[5])[0];
    rewardBox("Dashboard",["User " => $user,"Balance" => "$bal / $usd","Claims" => "$claim / $totalclaim"]);
}
function faucet(){
global $l,$cll,$type;
    while(true){
    $url = "https://earnbitmoon.club/";
    $r = Run($url, header0());
    $timer = explode(',', explode(' $("#claimTime").countdown(', $r)[1])[0];
    if($timer){
        $timer = ($timer/1000)-round(microtime(true));
        countdown($timer,"");
        $r = Run($url, header0());
    }
    $token = explode("';", explode("var token = '", $r)[1])[0];    
    $start = time();        
    $msg = sumbitfaucet($token);
    $end = time();
    $complete = $end - $start;        
    $url = "https://earnbitmoon.club/";
    $r = Run($url, header0());
    $bal = explode('</b>', explode('<b id="sidebarCoins">',$r)[1])[0];
    $usd = explode('</b>', explode('<b>', $r)[1])[0];
    $claim = explode('</b>', explode('<b>', $r)[4])[0];
    $totalclaim = explode('</b>', explode('<b>', $r)[5])[0];
    if($msg){
        $i++;
        rewardBox("Faucet",["Number" => $msg['number'],"Reward" => $msg['reward'],"Balance " => "$bal / $usd","Claims" => "$claim / $totalclaim","IconCaptcha" => "Complete In $complete Seconds"]);
        
        }    
    }
}
$pll = 0;
function ptc(){
    global $header, $l, $apikey, $apihost, $api, $url_id, $apiurl, $success, $failed, $cookie, $userAgent, $config, $configFile, $i, $type, $x, $ptc,$pll;
    while(true){
    $url = "https://earnbitmoon.club/ptc.html";
    $r = Run($url, header0());
             
        if(empty($sid) || empty($key)) {
            return faucet();
        }
    if(preg_match('/There is no website available yet!/', $r)){faucet();}
    $key = explode("'", explode("'&key=", $r)[1])[0];
    $sid = explode("',", explode("opensite('", $r)[1])[0];
    $link = "https://earnbitmoon.club/surf.php?sid=$sid&key=$key";
    $r = Run($link, header0());
    $timer = explode(';', explode('var secs =', $r)[1])[0];
    $token = explode("';", explode("var token = '", $r)[1])[0];
    countdown($timer, "");
    $r = sumbitptc($sid, $token);
    $link = $r['msg'];
    $url0 = $r['url'];    
    $url = "https://earnbitmoon.club/ptc.html";
    $r = Run($url, header0());
    $bal = explode('</b>', explode('<b id="sidebarCoins">',$r)[1])[0];
    $usd = explode('</b>', explode('<b>', $r)[1])[0];
    $ptc = explode('</span>', explode(' <span class="badge badge-info">',$r)[3])[0];
    if($link){
        $pll++;
        rewardBox("PTC",["Reward" => "$link","Balance" => "$bal / $usd","Viewed Url" => " $url0"]);
    }
  }
}

clear();
showBannerBox(host);
dashboard();
ptc();



function sumbitfaucet($token){
    global $header, $l, $apikey, $apihost, $api, $url_id, $apiurl, $success, $failed, $cookie, $userAgent, $config, $configFile, $i, $type, $l;
    $nn = 0;
    while(true){
        foreach (captcha1() as $captcha) {
            captchasolver($captcha,saveData(host,'cookie'),SaveData(host,'user_Agent'));
        $url = 'https://earnbitmoon.club/system/ajax.php';
        $request = "a=getFaucet&token=$token&captcha=3&challenge=false&response=false&ic-hf-id=1&ic-hf-se=$captcha&ic-hf-hp=";
        $r = Run($url, headers('earnbitmoon.club'), $request);
        $js = json_decode(strip_tags($r), true);
        if ($js["status"] == 200) {
       return ["number" => $js['number'],"reward" => $js['reward']];
                }else{
                    $nn++;
              //      animation("Bypassing IconCaptcha Attempt [$nn]");
                    if($nn == 20){return;}
                }
            }
       }
}
function sumbitptc($sid, $token){
    global $header, $l, $apikey, $apihost, $api, $url_id, $apiurl, $success, $failed, $cookie, $userAgent, $config, $configFile, $i, $type, $l;
  $nn = 0;
    while(true){
    foreach (captcha1() as $captcha) {
        captchasolver($captcha,saveData(host,'cookie'),SaveData(host,'user_Agent'));
    $url = 'https://earnbitmoon.club/system/ajax.php';
    $request = "a=proccessPTC&data=$sid&token=$token&captcha=3&challenge=false&response=false&ic-hf-id=1&ic-hf-se=$captcha&ic-hf-hp=";
    $r = Run($url, headers('earnbitmoon.club'), $request);
    $js = json_decode(strip_tags($r), true);
    if ($js["status"] == 200) {
        return ["msg" =>$js['message'],"url" => $js['redirect']];
            }else{
                $nn++;
   //                 animation("Bypassing IconCaptcha Attempt [$nn]");
                    if($nn == 20){return;}
            }
        }
   }
}