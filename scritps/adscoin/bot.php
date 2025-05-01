<?php
error_reporting(0);
define("host", "adscoin");
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


function headers($req){
return [
    'Host: faucetwebservice.mobilecloudmining.ru',
    'User-Agent:UnityPlayer/2022.3.17f1 (UnityWebRequest/1.0, libcurl/8.4.0-DEV)',
    'Accept:*/*',
    'Content-Type:application/x-www-form-urlencoded',
    'X-Unity-Version:2022.3.17f1',
    "Content-Length: ".strlen($req)
];
}


function dashboard(){
    global $l;
            $host = "https://faucetwebservice.mobilecloudmining.ru/";
    $id = SaveData(host,'gpgsId');
    $req = "gpgsId=$id";
    $r = Run("$host"."R%7DR4i+jYzc89z-Q/api/v0.1.3/login.php",headers($req),$req);
    $id = explode('|',$r)[0];
    $user = explode('|',$r)[1];
    $curn = explode('|',$r)[2];
    $bal = explode('|',$r)[3];
    $code = explode('|',$r)[14];
    rewardBox("Dashboard",["User" => $user,"ID" => $id,"Country" => $curn,"Balance" => "$bal Coins"]);
}
function claim($limiter){
    global $l;
    while(true){
        $host = "https://faucetwebservice.mobilecloudmining.ru/";
    $id = SaveData(host,'gpgsId');
    $req = "gpgsId=$id";
    $r = Run("$host"."R%7DR4i+jYzc89z-Q/api/v0.1.3/login.php",headers($req),$req);
    $id = explode('|',$r)[0];
    $user = explode('|',$r)[1];
    $curn = explode('|',$r)[2];
    $bal = explode('|',$r)[3];
    $code = explode('|',$r)[14];
    $req = "id=$id";    
    Run("https://faucetwebservice.mobilecloudmining.ru/R%7DR4i+jYzc89z-Q/api/v0.1.3/getConfig.php",headers($req),$req);    
    $ad_watched = 0;
$adprice = [
        "0.000942",
        "0.000103",
        "6.4E-05",
        "0.000942",
        "0.000921",
        "0.000935",
        "0.000487",
        "7.8E-05",
        "7.1E-05",
        "6.6E-05",
        "5.7E-05",
        "4.8E-05",
        "0.001206",
        "0.001305",
        "0.00063",
        "0.001296",
        "0.000642"
        ];
shuffle($adprice);
foreach ($adprice as $price) {
    countdown(rand(5,20),"");
    $last_bal = $bal;    
    $ad_watched++;
    $req = "id=$id&adPriceType=Bid&adPrice=$price&adNumber=$ad_watched&key=$code";    
    $r = Run($host."/R%7DR4i+jYzc89z-Q/api/v0.1.3/plusCoinsForVideo.php",headers($req),$req);
    $bal = explode('|',$r)[0];
    if($bal == "" || $bal == "error" || $bal == "error code: 1015"){$ad_watched--;continue;}
    $reward = $bal - $last_bal;
    rewardBox("Ads",["Reward" => "$reward Coins","Balance" => "$bal Coins"]);
    if($bal >= $limiter){exit("The limiter Has been triggerd");}
        }
    }
}

function withdraw($id,$bal){
    global $l;
    $req = "id=$id";
    $r = Run("https://faucetwebservice.mobilecloudmining.ru/R%7DR4i+jYzc89z-Q/api/v0.1.3/getPaymentsHistory.php",headers($req),$req);
    $i = 1;
    while(true){
    $status = explode('|',$r)[$i];
        if($status == ""){break;}   
    $r0 = explode('|', $r)[4];
    $r0 = explode('&',$r0)[0];
        $i++;
    if($status == "0"){
        return "no";
    }
}
    if($bal < rand(350,350)){return;}
    $bal = floor(Run("https://faucetwebservice.mobilecloudmining.ru/R%7DR4i+jYzc89z-Q/api/v0.1.3/getCoins.php",headers($req),$req));
    $req = "gpgsId=".SaveData(host,'gpgsId')."&walletNumber=".SaveData('Payeer')."&paymentType=PayeerRUB&coins=$bal";
    $r = Run("https://faucetwebservice.mobilecloudmining.ru/R%7DR4i+jYzc89z-Q/api/v0.1.3/paymentOrdered.php",headers($req),$req);
    if($r <= $bal){
        fast(WHITE . "[INFO] Withdraw Request Has been created" . NEWLINE);
        fast(WHITE . "[INFO] PAYEER_NO: " . SaveData('Payeer') . NEWLINE);
        fast(WHITE . "[INFO] BALANCE: $bal" . NEWLINE);
        print WHITE;fast($l);
        return claim();
    }else{
        fast(WHITE . "[ERROR] ERROR WHILE CREATING WITHDRAW" . NEWLINE);
    }
    return;
}
 


clear();
showBannerBox("adscoin");
SaveData(host,'gpgsId');
clear();
showBannerBox("adscoin");
echo CYAN . "If Its your New account Try This Limiter Amounts\n1-4 WD 300-350\n4-8 WD 500\n8-10 WD 800\n10+ WD 1000\n";
$limiter = Readline("Enter Limiter Amount: ");
clear();
showBannerBox("adscoin");
dashboard();
claim($limiter);




