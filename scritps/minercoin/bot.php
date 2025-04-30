<?php
error_reporting(0);
define("host", "minercoin.site");
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
    global $l;

    $r = Run("https://minercoin.site/login", header0());

    $email = SaveData(host, 'email');
    $pass = SaveData(host, 'pass');

    $req = "user=" . urlencode($email) . "&password=" . urlencode($pass) . "&login=";
    $r = Run("https://minercoin.site/controllers/auth.controller.php", headers('minercoin.site'), $req);

    echo "Login Success: " . $email . NEWLINE;
    print WHITE; fast($l);
}
function dashboard(){
    global $l;
    $r = Run("https://minercoin.site/dashboard",header0());
    $bal = explode(' </span>', explode('<span class="balance">', $r)[1])[0];
    $bal_ltc = explode('</span>', explode('LTC.png" width="15px"> ', $r)[1])[0];
    $bal_doge = explode('</span>	', explode('DOGE.png" width="15px"> ', $r)[1])[0];
    $ghs = explode('</span></p>', explode('></i>   <span class="float-left mt-2">', $r)[1])[0];
    if($bal == ""){login();return dashboard();}
    fast("[INFO] BALANCE: $bal Coins / $bal_ltc LTC / $bal_doge DOGE\n");
    fast("[INFO] POWER: {$ghs}\n");
    print WHITE;fast($l);
}
function faucet() {
    global $l, $apikey, $typeapi, $apihost;
    while (true) {
        $r = Run("https://minercoin.site/faucet", header0());
        if(preg_match('need to complete', $r)){
            exit("Complete Shortlink First\n");
        }
        $timer = explode(';', explode('let wait = ', $r)[1])[0];
        
        if ($timer && !str_contains($timer, "-")) {
            countdown($timer, "");
        }
        $r = Run("https://minercoin.site/faucet", header0());
        $id = explode('"', explode('<input type="hidden" name="user_id" value="', $r)[1])[0];
        if(empty($id)){login();return faucet();}
        $cap = captcha("https://minercoin.site/faucet", "0x4AAAAAABDsoxGH0Zg2j1rF", $apihost, $apikey, "turnstile");
        $req = "cf-turnstile-response=$cap&user_id=$id&faucet=";
        $r = Run("https://minercoin.site/controllers/claim.controller.php", headers('minercoin.site'), $req);
        
        $r = Run("https://minercoin.site/faucet", header0());
        $claim = explode('</p>', explode('<p class="mb-0 text-center">', $r)[3])[0];
        
        $r2 = Run("https://minercoin.site/dashboard",header0());
        $bal = explode(' </span>', explode('<span class="balance">', $r2)[1])[0];
        $bal_ltc = explode('</span>', explode('LTC.png" width="15px"> ', $r2)[1])[0];
        $bal_doge = explode('</span>	', explode('DOGE.png" width="15px"> ', $r2)[1])[0];
        $ghs = explode('</span></p>', explode('></i>   <span class="float-left mt-2">', $r2)[1])[0];
        
        if(preg_match('/You have claimed/', $r)){
            rewardbox("minercoin.site",["Reward " => "You have claimed 50.00 Mh/s","Claims " => $claim,"Apikey " => fetchApiBalance($typeapi,$apikey), "New Power " => $ghs]);
        }
    }
}


selecting();
clear();
showBannerBox("minercoin.site");
dashboard();
faucet();




