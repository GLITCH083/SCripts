<?php

error_reporting(0);
define("host", "litepick.io");

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
    return [
    "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
        "User-Agent: " . saveData(host,'user_Agent'),
        //"Cookie: " . saveData(host,'cookie')
    ];
    }
    function headers($host){
        return [
            "User-Agent: ".saveData(host,'user_Agent'),
            "Accept: application/json, text/javascript, */*; q=0.01",
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
            "Referer: https://$host/",
            "Origin: https://$host",
//            "x-requested-with: XMLHttpRequest",
            //"Cookie: " . saveData(host,'cookie')
    ];
}

function check_cloudflare(){
    $r = Run1('https://'."litepick.io".'/login.php',header0());
    if(preg_match('/Just a moment.../', $r['body'])){
        cf_bypass("https://litepick.io");
        rewardBox("CF",["INFO" => "CF-BYPASSED"]);
        return login();
    }
}

function login(){
    global $l,$apikey,$typeapi,$apihost;
    unlink("cookie.txt");
    $r = Run1('https://'."litepick.io".'/login.php',array_merge(["cookie: " . SaveData(host,'cookie')],["User-Agent: " . SaveData(host,'user_Agent')],header0()));
    if(preg_match('/Just a moment.../', $r['body'])){
        cf_bypass("https://litepick.io");
        rewardBox("CF",["INFO" => "CF-BYPASSED"]);
        return login();
    }
    $csrf = explode(';', explode('set-cookie: csrf_cookie_name=', $r['header'])[1])[0];
    $r = $r['body'];
    $email = SaveData(host,'email');
     $password = SaveData(host,'pass');
    $cap = captcha("https://litepick.io/faucet.php", "0x4AAAAAAA0-UWDHOKP0OrgS", $apihost, $apikey, "turnstile") ;
    if($host == "dogepick.io"){
        $req = "action=login&email=$email&password=$password&captcha_type=3&g-recaptcha-response=null&captcha=&h-captcha-response=$cap&twofa=&csrf_test_name=$csrf";
    }else{
    $req = "action=login&email=$email&password=$password&captcha_type=3&g-recaptcha-response=null&h-captcha-response=null&captcha=&twofa=&c_captcha_response=$cap&csrf_test_name=$csrf";
    }     
    $r = Run1("https://litepick.io/process.php",array_merge(["cookie: " . SaveData(host,'cookie')],["User-Agent: " . SaveData(host,'user_Agent')],headers("litepick.io")),$req);
    $claim = json_decode($r['body'],true);
    if($claim['mes'] == "Login successfully!"){
     rewardBox("Login",["INFO" => "Login Successfully","Host" => host,"Account" => $email]);   
        return;
        }elseif($claim['mes'] == "Please enter correct username or password!"){
            rewardBox("Error",["INFO" => $claim['mes']]);
            unlinkData(host,'email');
            unlinkData(host,'pass');
            SaveData(host,'pass');
            SaveData(host,'email');
            return login();
        }
}

function dashboard(){
    global $l;
    $r = Run("https://litepick.io/faucet.php",array_merge(["cookie: " . SaveData(host,'cookie')],["User-Agent: " . SaveData(host,'user_Agent')],header0()));
    if(preg_match('/Just a moment.../', $r)){
        cf_bypass("https://litepick.io");
        rewardBox("CF",["msg" => "CF-BYPASSED"]);
        return dashboard();
    }
    $bal = explode('</span>', explode('<div class="top_balance"><span class="user_balance">', $r)[1])[0];
    $user = explode('. Welcome back!</p>', explode('<p style="margin: 0px auto 30px; font-size: 16px; font-weight: bold">Hello ', $r)[1])[0];
    if($bal == ""){login();return dashboard();}
    RewardBox("Dashboard",["User" => $user,"Balance" => $bal . " LTC"]);
}

function update(){
    $r = Run("https://litepick.io/faucet.php",array_merge(["cookie: " . SaveData(host,'cookie')],["User-Agent: " . SaveData(host,'user_Agent')],header0()));
    if(preg_match('/Just a moment.../', $r)){
        cf_bypass("https://litepick.io");
        rewardBox("CF",["msg" => "CF-BYPASSED"]);
        return update();
    }
$bal = explode('</span>', explode('<div class="top_balance"><span class="user_balance">', $r)[1])[0];
    return $bal;
}
function csrf(){
        $r = Run1("https://litepick.io/faucet.php",array_merge(["cookie: " . SaveData(host,'cookie')],["User-Agent: " . SaveData(host,'user_Agent')],header0()));
    if(preg_match('/Just a moment.../', $r['body'])){
        cf_bypass("https://litepick.io");
        rewardBox("CF",["msg" => "CF-BYPASSED"]);
        return csrf();
    }
	   		$csrf = explode(';', explode('set-cookie: csrf_cookie_name=', $r['header'])[1])[0];
    return $csrf;
}

function faucet(){
    global $l,$apikey,$typeapi,$apihost,$clk;
    while(true){
    $r = Run("https://litepick.io/faucet.php",array_merge(["cookie: " . SaveData(host,'cookie')],["User-Agent: " . SaveData(host,'user_Agent')],header0()));
    if(preg_match('/Just a moment.../', $r)){
        cf_bypass("https://litepick.io");
        rewardBox("CF",["msg" => "CF-BYPASSED"]);
        return faucet();
    }
        
        
    $hash = explode('&',explode('hash=',$r)[1])[0];
		   	$timer = explode(');', explode('show_countdown_clock(', $r)[2])[0];
    if($timer){countdown($timer,"");continue;}
$limit = explode('</b><i', explode('<b class="faucet_claims_remaining">', $r)[1])[0];
if($limit == 0){exit("Limit Reached\n");}
    $csrf = csrf();
    $cap = captcha("https://litepick.io/faucet.php", "0x4AAAAAAA0-UWDHOKP0OrgS", $apihost, $apikey, "turnstile") ;
        $req = "action=claim_hourly_faucet&hash=$hash&captcha_type=3&g-recaptcha-response=null&h-captcha-response=null&captcha=&c_captcha_response=$cap&ft=&csrf_test_name=$csrf";
     $claim = Run1("https://litepick.io/process.php",array_merge(["cookie: " . SaveData(host,'cookie')],["User-Agent: " . SaveData(host,'user_Agent')],headers("litepick.io")),$req);
     $claim = json_decode($claim['body'],true); 
      
        if(str_contains($claim['mes'],"Please wait for")){
            animation("[$host] " . $claim['mes']);return;
        }elseif($claim['mes'] == "You have to login to continue!"){
login();
return faucet();
        }        
        
        $bal = update();
                    date_default_timezone_set('Asia/Karachi');
                    $date = date('Y-m-d H:i:s'); // Output: Current date and time in Pakistan
            $clk++;
            $limit--;
             rewardBox("faucet",["Reward" => "Lucky Number " . $claim['num'] . " " . $claim['mes'],"Balance" => $bal . " LTC","Claims" => $limit,"Apikey" => fetchApiBalance($typeapi, $apikey)]);
        }  
}






clear();
selecting();
clear();
showBannerBox(host);
SaveData(host,'email');
saveData(host,'pass');
clear();
showBannerBox(host);
$r = Run1('https://'."litepick.io".'/login.php',header0());
if(preg_match('/Just a moment.../', $r['body'])){
 check_cloudflare();   
}
dashboard();
faucet();
