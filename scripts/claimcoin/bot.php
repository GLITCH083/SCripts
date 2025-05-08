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
    $r = Run("https://claimcoin.in/login",header0());
    $csrf = explode('"', explode('<input type="hidden" name="csrf_token_name" value="', $r)[1])[0];
    $cap = captcha("https://claimcoin.in/login", "0x4AAAAAAAxlE0vMwF0PrsaO", $apihost, $apikey, "turnstile");
    $email = (SaveData(host,'email'));
    $pass = SaveData(host,'pass');
    $req = "csrf_token_name=$csrf&email=" . urlencode($email) . "&password=$pass&captcha=turnstile&cf-turnstile-response=$cap";
    $r = Run1("https://claimcoin.in/auth/login",headers('claimcoin.in'),$req);
    if($r['info']['redirect_url'] == "https://claimcoin.in/dashboard"){
        rewardBox("Login",["INFO" => "Login Success","Host" => host,"Account" => SaveData(host,'email')]);
        return;
    }
}
function dashboard(){
    global $l;
    $r = Run("https://claimcoin.in/dashboard",header0());
    $bal = explode('</h2>', explode('<h2 class="f-w-600">', $r)[1])[0];
    if($bal == ""){login(); return dashboard();}
    rewardBox("Dashboard",["Balance" => $bal]);
}

function faucet(){
    global $l;
        $dictionaries = [
        "numberword" => numberword(),
        "wordnumber" => wordnumber(),
        "numberroman" => numberroman(),
        "romannumber" => romannumber(),
        "mathans" => mathans(),
        "ansmath" => ansmath(),
        "oox" => oox(),
        "xxx" => xxx(),
        "xox" => xox(),
        "oxo" => oxo(),
        "zoo" => zoo(),
        "ooz" => ooz(),
        "animals" => animals()
    ];
    while(true){
        $host = "claimcoin.in";
        @unlink("main_{$host}.png");
        @unlink("image_0_{$host}.png");
        @unlink("image_1_{$host}.png");
        @unlink("image_2_{$host}.png");
        unset($t, $options, $match, $ids, $images);

       $r = Run("https://claimcoin.in/faucet",header0());
        if(preg_match('/Daily limit reached, see you tomorrow/', $r)){
       exit;
}        
        $timer = explode(" - 1;", explode("var wait = ", $r)[1])[0];
    if($timer){
        madfaucet();
        countdown($timer,"");
        continue;
    }
        if(!bs64Image(explode('" alt=""', explode('<img src="data:image/png;base64,', $r)[1])[0], "main_{$host}.png")){
            countdown(10,"Wait for");
            continue;
        }
        $start = microtime(true);
        $main = @preg_split('/[;:,. ]+/', shell_exec("tesseract main_{$host}.png stdout --psm 7 2>&1"));
        $match = mainWordMatch($main, $dictionaries);
        if (count($match) < 3) {
            continue;
        }
        

        $images = stripslashes(explode('"]',explode('var ablinks= ["',$r)[1])[0]);
        preg_match_all('/<a href="#" rel="(.*?)">/', $images, $ids);
        preg_match_all('#<img src="data:image/png;base64,(.*?)" alt=""#', $images, $image);
        foreach($image[1] as $key => $value){
            if(!bs64Image($value, "image_{$key}_{$host}.png")){
                countdown(10,"Wait for");
                continue 2;
            }
            $options[$ids[1][$key]] = @preg_replace('/\s+/', '', shell_exec("tesseract image_{$key}_{$host}.png stdout --psm 7 2>&1"));
        }
        $t = compare($match, $options, $dictionaries);
        $end = microtime(true);
        $ab_solve = $end - $start;
        if (empty($t) || count($t) < 3) {
            continue;
        }
        $atb = "+".implode("+", $t);
    $csrf = explode('"', explode('<input type="hidden" name="csrf_token_name" id="token" value="', $r)[1])[0];
    $cap = IconCaptchaBypass($r,$host,"https://claimcoin.in/icaptcha/req","WebKitFormBoundaryWcEFUS6RzDVYqgFt",SaveData(host,'cookie'),saveData(host,'user_Agent'));
        $req = "antibotlinks=$atb&csrf_token_name=$csrf&$cap";
    $r = Run("https://claimcoin.in/faucet/verify",headers('claimcoin.in'),$req);
        $r = Run("https://claimcoin.in/faucet",header0());
        $msg = explode("', 'success')</script>", explode("Swal.fire('Good job!', '", $r)[1])[0];
    if($msg){
            $r = Run("https://claimcoin.in/dashboard",header0());
    $bal = explode('</h2>', explode('<h2 class="f-w-600">', $r)[1])[0];
        rewardbox("Faucet",["Reward " => "$msg","Balance " => $bal,"AB-Values" => $atb]);
       }
    }
}

function madfaucet(){
    global $l;
            $dictionaries = [
        "numberword" => numberword(),
        "wordnumber" => wordnumber(),
        "numberroman" => numberroman(),
        "romannumber" => romannumber(),
        "mathans" => mathans(),
        "ansmath" => ansmath(),
        "oox" => oox(),
        "xxx" => xxx(),
        "xox" => xox(),
        "oxo" => oxo(),
        "zoo" => zoo(),
        "ooz" => ooz(),
        "animals" => animals()
    ];

    while(true){
        $host = "claimcoin.in";
        @unlink("main_{$host}.png");
        @unlink("image_0_{$host}.png");
        @unlink("image_1_{$host}.png");
        @unlink("image_2_{$host}.png");
        unset($t, $options, $match, $ids, $images);
    $r = Run("https://claimcoin.in/madfaucet",header0());
        if(preg_match('/Daily limit reached, see you tomorrow/', $r)){
       faucet();
}
    $timer = explode(" - 1;", explode("var wait = ", $r)[1])[0];
    if($timer){faucet();countdown($timer,"");continue;}
        if(!bs64Image(explode('" alt=""', explode('<img src="data:image/png;base64,', $r)[1])[0], "main_{$host}.png")){
            countdown(10,"Wait for");
            continue;
        }
        $start = microtime(true);
        $main = @preg_split('/[;:,. ]+/', shell_exec("tesseract main_{$host}.png stdout --psm 7 2>&1"));
        $match = mainWordMatch($main, $dictionaries);
        if (count($match) < 3) {
            continue;
        }
        

        $images = stripslashes(explode('"]',explode('var ablinks= ["',$r)[1])[0]);
        preg_match_all('/<a href="#" rel="(.*?)">/', $images, $ids);
        preg_match_all('#<img src="data:image/png;base64,(.*?)" alt=""#', $images, $image);
        foreach($image[1] as $key => $value){
            if(!bs64Image($value, "image_{$key}_{$host}.png")){
                countdown(10,"Wait for");
                continue 2;
            }
            $options[$ids[1][$key]] = @preg_replace('/\s+/', '', shell_exec("tesseract image_{$key}_{$host}.png stdout --psm 7 2>&1"));
        }
        $t = compare($match, $options, $dictionaries);
        $end = microtime(true);
        $ab_solve = $end - $start;
        if (empty($t) || count($t) < 3) {
            continue;
        }
        $atb = "+".implode("+", $t);
    $csrf = explode('"', explode('<input type="hidden" name="csrf_token_name" id="token" value="', $r)[1])[0];
    $cap = IconCaptchaBypass($r,$host,"https://claimcoin.in/icaptcha/req","WebKitFormBoundaryWcEFUS6RzDVYqgFt",SaveData(host,'cookie'),saveData(host,'user_Agent'));
        $req = "antibotlinks=$atb&csrf_token_name=$csrf&$cap";
    $r = Run("https://claimcoin.in/madfaucet/verify",headers('claimcoin.in'),$req);
    $r = Run("https://claimcoin.in/madfaucet",header0());
    $msg = explode("', 'success')</script>", explode("Swal.fire('Good job!', '", $r)[1])[0];
    if($msg){
        $r = Run("https://claimcoin.in/dashboard",header0());
    $bal = explode('</h2>', explode('<h2 class="f-w-600">', $r)[1])[0];
        rewardbox("Mad-Faucet",["Reward " => "$msg","Balance " => $bal,"AB-Values" => $atb]);

            }
    }    
}

function ptc(){
    global $l;
    while(true){
    $r = Run("https://claimcoin.in/ptc",header0());
    $window = explode("'", explode("location.href='https://claimcoin.in/ptc/window/", $r)[1])[0];
    if(empty($window)){
        $iframe = explode("'", explode("location.href='https://claimcoin.in/ptc/iframe/", $r)[1])[0];
        if(empty($iframe)){faucet();}
        $r = Run("https://claimcoin.in/ptc/iframe/$iframe",header0());
        $timer = explode(";", explode("var timer = ", $r)[1])[0];
        $url = explode("'", explode("var url = '", $r)[1])[0];
        $csrf = explode('"', explode('<input type="hidden" name="csrf_token_name" value="', $r)[1])[0];
        countdown($timer,"");
        $cap = IconCaptchaBypass($r,$host,"https://claimcoin.in/icaptcha/req","WebKitFormBoundaryWcEFUS6RzDVYqgFt",SaveData(host,'cookie'),saveData(host,'user_Agent'));
        $req = "csrf_token_name=$csrf&$cap";
        $r = Run("https://claimcoin.in/ptc/verify/$iframe",headers(host),$req);
        $type = "iframe"; 
    }else{
    $r = Run("https://claimcoin.in/ptc/window/$window",header0());
    $timer = explode(';', explode('var timer = ', $r)[1])[0];
    $url = explode("';", explode("var url = '", $r)[1])[0];
    $csrf = explode('"', explode('<input type="hidden" name="csrf_token_name" value="', $r)[1])[0];
    countdown($timer,"");
    $cap = IconCaptchaBypass($r,$host,"https://claimcoin.in/icaptcha/req","WebKitFormBoundaryWcEFUS6RzDVYqgFt",SaveData(host,'cookie'),saveData(host,'user_Agent'));
    $req = "csrf_token_name=$csrf&$cap";
    $r = Run("https://claimcoin.in/ptc/verify/$window",headers(host),$req);
    $type = "window"; 
}
    $r = Run("https://claimcoin.in/ptc",header0());
    $msg = explode("', 'success')</script>", explode("Swal.fire('Good job!', '", $r)[1])[0];
    $r2 = Run("https://claimcoin.in/dashboard",header0());
    $bal = explode('</h2>', explode('<h2 class="f-w-600">', $r2)[1])[0];
    if($msg){
        rewardBox("Ptc-$type",["Reward" => $msg,"Url" => $url,"Balance" => $bal]);
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
ptc();



