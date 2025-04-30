<?php

error_reporting(0);
define("host", "claimcoin.in");

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
      //  "Cookie: " . saveData('cookie')
    ];
    }
    function headers($host){
        return ["User-Agent: ".saveData(host,'user_Agent'),
            "Accept: application/json, text/javascript, */*; q=0.01",
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
            "Referer: https://$host/",
            "Origin: https://$host",
//            "x-requested-with: XMLHttpRequest",
           // "Cookie: " . saveData('cookie')
    ];
}


function login(){
    global $l,$apihost,$apikey;
    unlink('cookie.txt');
    $r = Run("https://claimcoin.in/login",header0());
    $csrf = explode('"', explode('<input type="hidden" name="csrf_token_name" value="', $r)[1])[0];
    $cap = captcha("https://claimcoin.in/login", "0x4AAAAAAAxlE0vMwF0PrsaO", $apihost, $apikey, "turnstile");
    $email = (SaveData(host,'email'));
    $pass = SaveData(host,'pass');
    $req = "csrf_token_name=$csrf&email=" . urlencode($email) . "&password=$pass&captcha=turnstile&cf-turnstile-response=$cap";
    $r = Run1("https://claimcoin.in/auth/login",headers('claimcoin.in'),$req);
    if($r['info']['redirect_url'] == "https://claimcoin.in/dashboard"){
        echo "[LOGIN SUCCESS: " . SaveData(host,'email') . "]" . NEWLINE;
        print WHITE;fast($l);
        return;
    }
}
function dashboard(){
    global $l;
    $r = Run("https://claimcoin.in/dashboard",header0());
    $bal = explode('</h2>', explode('<h2 class="f-w-600">', $r)[1])[0];
    if($bal == ""){login(); return dashboard();}
    fast("[INFO] BALANCE: {$bal}\n");
    print WHITE;fast($l);
}

function faucet(){
    global $l;
    while(true){
    $r = Run("https://claimcoin.in/faucet",header0());
    countdown(10,"[0]");
    $r = Run("https://claimcoin.in/faucet/complete_step_one",header0());
    $r = Run("https://claimcoin.in/faucet/verify",header0());
    countdown(10,"[1]");
    $r = Run("https://claimcoin.in/faucet/complete_step_two",header0());
    $r = Run("https://claimcoin.in/faucet/claim",header0());
    $csrf = explode('"', explode('<input type="hidden" name="csrf_token_name" id="token" value="', $r)[1])[0];
    $cap = IconCaptchaBypass($r,$host,"https://claimcoin.in/icaptcha/req","WebKitFormBoundaryWcEFUS6RzDVYqgFt",SaveData(host,'cookie'),saveData(host,'user_Agent'));
    $req = "csrf_token_name=$csrf&$cap";
    $r = Run("https://claimcoin.in/faucet/process_claim",headers('claimcoin.in'),$req);
    $r = Run("https://claimcoin.in/faucet",header0());
        $msg = explode("', 'success')</script>", explode("Swal.fire('Good job!', '", $r)[1])[0];
    if($msg){
            $r = Run("https://claimcoin.in/dashboard",header0());
    $bal = explode('</h2>', explode('<h2 class="f-w-600">', $r)[1])[0];
        rewardbox("claimcoin.in",["Reward " => "$msg","Balance " => $bal]);
       }
    }
}

function madfaucet(){
    global $l;
    while(true){
    $r = Run("https://claimcoin.in/madfaucet",header0());if(preg_match('/Daily limit reached, see you tomorrow/', $r)){
       exit("Daily Limit Rached\n");
}
    $timer = explode(" - 1;", explode("var wait = ", $r)[1])[0];
    if($timer){countdown($timer,"");continue;}
    $csrf = explode('"', explode('<input type="hidden" name="csrf_token_name" id="token" value="', $r)[1])[0];
    $cap = IconCaptchaBypass($r,$host,"https://claimcoin.in/icaptcha/req","WebKitFormBoundaryWcEFUS6RzDVYqgFt",SaveData('cookie'),saveData('user_Agent'));
    $req = "csrf_token_name=$csrf&$cap";
    $r = Run("https://claimcoin.in/madfaucet/verify",headers('claimcoin.in'),$req);
    $r = Run("https://claimcoin.in/madfaucet",header0());
    $msg = explode("', 'success')</script>", explode("Swal.fire('Good job!', '", $r)[1])[0];
    if($msg){
            }
    }    
}
selecting();
clear();
SaveData(host,'User_Agent');
SaveData(host,'email');
SaveData(host,'pass');
clear();
showBannerBox('claimcoin.in');
dashboard();
faucet();



