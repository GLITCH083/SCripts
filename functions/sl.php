
<?php
include_once("function.php");
include_once("captcha.php");

function cutio($url){
    global $l, $apihost, $apikey;
       if (str_contains($url, 'https')) {
    $url = str_replace('https', 'http', $url);
    }
    $url = str_replace('http', 'https', $url);
    $host = parse_url($url, PHP_URL_HOST);
    if (str_contains($url, 'lkfms.pro')) {
        $url = "https://finance240.com/?overrideSession=$url";
        $r = Run($url);
        $host1 = parse_url($url, PHP_URL_HOST);
       }elseif(str_contains($url, 'cutto.io') || str_contains($url,'btcut.io') || str_contains($url,'zshort.io') || str_contains($url,'chainfo.xyz')){
        $url = "https://playonpc.online/?overrideSession=$url";
        $r = Run($url);
        $host1 = parse_url($url, PHP_URL_HOST);
    }elseif(str_contains($url,'easycut.io')){
        $url = "https://business.retrotechreborn.com/?overrideSession=$url";
        $r = Run($url);
        $host1 = parse_url($url, PHP_URL_HOST);    
    }elseif(str_contains($url,'linkslice.io')){
        $url = "https://u1323-liio.quins.us/?overrideSession=$url";
        $r = Run($url);
        $host1 = parse_url($url, PHP_URL_HOST);       
    }
    $enjoy = explode('"', explode('window.location.href = "', $r)[1])[0];
    if(str_contains($enjoy, 'playonpc') || str_contains($enjoy,'quins.us')){
     $enjoy = "$enjoy/";
     $r = Run1($enjoy);
     $ref = "https://www.google.com/";
}
    
    while(true){

    $r = Run($enjoy,["Host: $host1", "referer: $ref"]);
    $name = explode('";', explode('el.name = "', $r)[1])[0];
    $sitekey = explode('"', explode('data-sitekey="', $r)[1])[0];
    $step = explode(': ";', explode('"Step: ', $r)[1])[0];
    if ($step == ""){return ["success" => false, "message" => "Unstable Internet"];}
    
    if($sitekey){
        $cap = captcha($enjoy, $sitekey, $apihost, $apikey, 'hcaptcha');
        if(empty($cap)){
            return ["success" => false, "message" => "Unstable Internet"];
        }
          $req = "g-recaptcha-response=$cap&h-captcha-response=$cap&$name=true";
    }else{
        $req = "no-recaptcha-noresponse=true&$name=true";
        countdown(15, "[$host] Link Step ($step)");
    }

    $ref = $enjoy;
    $r = Run($enjoy,["host: $host1","referer: $ref"],$req);
    $enjoy = explode('");', explode('<script>window.location.replace("', $r)[1])[0];
    $link = explode('"</script>', explode('<script>window.location.href = "', $r)[1])[0];
    if(str_contains($enjoy, 'playonpc') || str_contains($enjoy, 'quins.us')){
        $enjoy = "$enjoy/";
    }
    if($link){
        if (str_contains($link, 'insurancexblog.blogspot.com')) {
            $link = str_replace('https://insurancexblog.blogspot.com/?url=', '', str_replace('tk', 'token', str_replace('&', '?', $link))); 
        }
        break;
    }
       }
    
       $header = ["accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7","content-type: application/x-www-form-urlencoded", "referer: https://$host1/"];
        $headers = ["accept: application/json,text/javascript, */*; q=0.01", "x-requested-with:XMLHttpRequest", "content-type: application/x-www-form-urlencoded; charset=UTF-8"];
        $r = Run($link, $header);
        $ad_form = rawurlencode(explode('"', explode('<input type="hidden" name="ad_form_data" value="', $r)[1])[0]);
        $csrf = rawurlencode(explode('"', explode('<input type="hidden" name="_csrfToken" autocomplete="off" value="', $r)[1])[0]);
        $token = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[fields]" autocomplete="off" value="', $r)[1])[0]);
        $token_unlock = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[unlocked]" autocomplete="off" value="', $r)[1])[0]);
        countdown(10, "[$host] Please Wait");
        $url = "https://{$host}/links/go";
        $request = "_method=POST&_csrfToken={$csrf}&ad_form_data={$ad_form}&_Token%5Bfields%5D={$token}&_Token%5Bunlocked%5D={$token_unlock}";
        $data = json_decode(Run($url, $headers, $request), true);
       $status = $data['message'];
       if ($status == "You have reached your views/IP limit. Please come back in 24 hours."){
        return ["error" => true, "message" => "Shortlink limit"];
       }
       if (isset($data['url'])) {
        if (str_contains($data['url'], 'limit.php')) {
            return ["error" => true, "message" => "Shortlink limit"];
        }
        if (str_contains($data['url'], 'bypass2.php')) {
            return ["error" => true, "message" => "Bypass detect"];
        } else if ($data['url'] == '/links/go') {
            return ["error" => true, "message" => "unexpected error"];
        } else {
            return ["success" => true, "message" => "Shortlink bypass", "original_url" => $data['url']];
        }
    }
    return ["error" => true, "message" => "Invalid url provided/Bypass failed"];
}


function rev($url){
    global $l, $apihost, $apikey;
    $r = Run($url);
    $nm = 0;
    $host = parse_url($url, PHP_URL_HOST);
    $go = explode('";', explode('window.location.href = "', $r)[1])[0];
    
    $r = Run($go, ["referer: $url"]);
    $req = ['humanverification', 'secondtimer', 'onetimer', 'twotimer', 'threetimer', 'fourtimer'];
    foreach($req as $val){
    $safelink = urlencode(explode('">', explode('<input type="hidden" name="newwpsafelink" value="', $r)[1])[0]);
    if(empty($safelink)){
        return ["success" => false, "message" => "Unstable Internet"];
    }
    $fake = explode('"', explode('action="', $r)[1])[0];
    $sitekey = explode('"', explode('data-sitekey="', $r)[1])[0];
    if($sitekey){
        $captchas = captcha($fake, $sitekey, $apihost, $apikey, 'userrecaptcha');
        if(empty($captchas)){
            return ["success" => false, "message" => "Unstable Internet"];
        }
        $request = "newwpsafelink={$safelink}&{$val}=1&g-recaptcha-response=$captchas";
    }else{
        countdown(10, "[$host] STEP [$nm]");
        $request = "newwpsafelink={$safelink}&{$val}=1";
        $nm++;    
    }
        $r = Run($fake, ["referer: $fake"], $request);
        while(empty($r)){
            $r = Run($fake, ["referer: $fake"], $request);
        }
        $real = explode("', '_self')", explode("window.open('", $r)[1])[0];
        if($real){
            $r = Run1($real);
            $enjoy = $r['info']['redirect_url'];
            $host = parse_url($enjoy, PHP_URL_HOST);
            break;
        }
    }
    $header = ["accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7","content-type: application/x-www-form-urlencoded", "referer: $real"];
    $headers = ["accept: application/json,text/javascript, */*; q=0.01", "x-requested-with:XMLHttpRequest", "content-type: application/x-www-form-urlencoded; charset=UTF-8",]; 
    $r = Run($enjoy, $header);
    $ad_form = rawurlencode(explode('"', explode('<input type="hidden" name="ad_form_data" value="', $r)[1])[0]);
    if(empty($ad_form)){
        $r = Run($enjoy, $header);
        $ad_form = rawurlencode(explode('"', explode('<input type="hidden" name="ad_form_data" value="', $r)[1])[0]); 
    }    
    $csrf = rawurlencode(explode('"', explode('<input type="hidden" name="_csrfToken" autocomplete="off" value="', $r)[1])[0]);
        $token = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[fields]" autocomplete="off" value="', $r)[1])[0]);
        $token_unlock = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[unlocked]" autocomplete="off" value="', $r)[1])[0]);
countdown(10, "Getting Link!");
$url = "https://$host/links/go";
$request = "_method=POST&_csrfToken={$csrf}&ad_form_data={$ad_form}&_Token%5Bfields%5D={$token}&_Token%5Bunlocked%5D={$token_unlock}";    
$data = json_decode(Run($url, $headers, $request), true);
if(isset($data['url'])){
    if(str_contains($data['url'], 'limit.php')){
        return ["success" => false, "message" => "Shortlink limit"];
    }
    if(str_contains($data['url'], 'bypass2.php')){
        return ["success" => false, "message" => "Bypass detect"];
    }else if($data['url'] == '/links/go'){
        return ["success" => false, "message" => "unexpected error"];
    }else{
        return ["success" => true, "message" => "Shortlink bypass", "original_url" => $data['url']];
        }
   }
}
function exe($url){
    global $apikey, $apihost;
    $r = Run1($url);
    $host = parse_url($url, PHP_URL_HOST);
    $redirect = $r['info']['redirect_url'];
    $r = Run($redirect);
    $csrf = rawurlencode(explode('"', explode('<input type="hidden" name="_csrfToken" autocomplete="off" value="', $r)[1])[0]);
        $token = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[fields]" autocomplete="off" value="', $r)[1])[0]);
        $token_unlock = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[unlocked]" autocomplete="off" value="', $r)[1])[0]);
        $f_n = explode('"', explode('<input type="hidden" name="f_n" value="', $r)[1])[0];
        $request = "_method=POST&_csrfToken={$csrf}&f_n={$f_n}&_Token%5Bfields%5D={$token}&_Token%5Bunlocked%5D={$token_unlock}&extraPage=";
        $r = Run($redirect, ["Content-Type: application/x-www-form-urlencoded"], $request);
        $sitekey = explode('"', explode('"invisible_reCAPTCHA_site_key":"', $r)[1])[0];
        $cap = captcha($redirect, $sitekey, $apihost, $apikey, 'userrecaptcha');
        $csrf = rawurlencode(explode('"', explode('<input type="hidden" name="_csrfToken" autocomplete="off" value="', $r)[1])[0]);
        $token = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[fields]" autocomplete="off" value="', $r)[1])[0]);
        $token_unlock = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[unlocked]" autocomplete="off" value="', $r)[1])[0]);
        $f_n = explode('"', explode('<input type="hidden" name="f_n" value="', $r)[1])[0];
        $request = "_method=POST&_csrfToken={$csrf}&f_n={$f_n}&_Token%5Bfields%5D={$token}&_Token%5Bunlocked%5D={$token_unlock}&g-recaptcha-response=$cap&ref=";
        $r = Run($redirect, ["Content-Type: application/x-www-form-urlencoded"], $request);
        $headers = ["accept: application/json,text/javascript, */*; q=0.01", "x-requested-with:XMLHttpRequest", "content-type: application/x-www-form-urlencoded; charset=UTF-8",]; 
        $ad_form = rawurlencode(explode('"', explode('<input type="hidden" name="ad_form_data" value="', $r)[1])[0]); 
        $csrf = rawurlencode(explode('"', explode('<input type="hidden" name="_csrfToken" autocomplete="off" value="', $r)[1])[0]);
        $token = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[fields]" autocomplete="off" value="', $r)[1])[0]);
        $token_unlock = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[unlocked]" autocomplete="off" value="', $r)[1])[0]);
countdown(10, "[$host] Getting Link!");
$url = "https://falpus.com/links/go";
$request = "_method=POST&_csrfToken={$csrf}&ad_form_data={$ad_form}&_Token%5Bfields%5D={$token}&_Token%5Bunlocked%5D={$token_unlock}";    
$data = json_decode(Run($url, $headers, $request), true);
if (isset($data['url'])) {
    if (str_contains($data['url'], 'limit.php')) {
        return ["error" => true, "message" => "Shortlink limit"];
    }
    if (str_contains($data['url'], 'bypass2.php')) {
        return ["error" => true, "message" => "Bypass detect"];
    } else if ($data['url'] == '/links/go') {
        return ["error" => true, "message" => "unexpected error"];
    } else {
        return ["success" => true, "message" => "Shortlink bypass", "original_url" => $data['url']];
    }
}
return ["error" => true, "message" => "Invalid url provided/Bypass failed"];

    }
function clkwiki($url){
    global $apikey, $apihost;
    $host1 = parse_url($url, PHP_URL_HOST);
    $r = Run1($url);
    $redirect = $r['info']['redirect_url'];
    $r = Run($redirect);
    $url = explode('"', explode('<input name="url" type="hidden" value="', $r)[1])[0];
    $r = Run1($url);
    $redirect = $r['info']['redirect_url'];
    $r = Run($redirect);
$url = explode('"', explode('<input name="url" type="hidden" value="', $r)[1])[0];
$token = explode('"', explode('<input type="hidden" name="token" value="', $r)[1])[0];
$c_d = explode('"', explode('<input type="hidden" name="c_d" value="', $r)[1])[0];
$c_t = explode('"', explode('<input type="hidden" name="c_t" value="', $r)[1])[0];
$uid = explode('"', explode('<input type="hidden" name="uid" value="', $r)[1])[0];
$alias = explode('"', explode('<input type="hidden" name="alias" value="', $r)[1])[0];
$sitekey = explode('"', explode('sitekey="', $r)[1])[0];
$action = explode('"', explode('<form action="', $r)[1])[0];
$host = parse_url($action, PHP_URL_HOST);
$cap = captcha($redirect, $sitekey, $apihost, $apikey, 'hcaptcha');
$data = "alias=$alias&c_d=$c_d&c_t=$c_t&g-recaptcha-response=$cap&h-captcha-response=$cap&pid=1&ref=&submit=&token=$token&uid=$uid&url=$url";
$r = Run($action, ["referer: $url"], $data);
$timer = explode(';', explode('timeSec =', $r)[1])[0];
countdown($timer, "Last Step");
$getlink = explode("';", explode(".href = '", $r)[1])[0];
$header = ["accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7","content-type: application/x-www-form-urlencoded", "referer: https://$host/"];
$data = "alias=$alias&c_d=$c_d&c_t=$c_t&h-captcha-response=$cap&pid=1&ref=https://$host/&submit=&token=$token&uid=$uid&url=$url";
$r = Run($getlink, $header, $data);
$headers = ["accept: application/json,text/javascript, */*; q=0.01", "x-requested-with:XMLHttpRequest", "content-type: application/x-www-form-urlencoded; charset=UTF-8",]; 
$ad_form = rawurlencode(explode('"', explode('<input type="hidden" name="ad_form_data" value="', $r)[1])[0]); 
countdown(20, "[$host1] Getting Link!");
$url = "https://clk.kim/links/go";
$request = "_method=POST&ad_form_data={$ad_form}";    
$data = json_decode(Run($url, $headers, $request), true);
if (isset($data['url'])) {
    if (str_contains($data['url'], 'limit.php')) {
        return ["error" => true, "message" => "Shortlink limit"];
    }
    if (str_contains($data['url'], 'bypass2.php')) {
        return ["error" => true, "message" => "Bypass detect"];
    } else if ($data['url'] == '/links/go') {
        return ["error" => true, "message" => "unexpected error"];
    } else {
        return ["success" => true, "message" => "Shortlink bypass", "original_url" => $data['url']];
    }
}
return ["error" => true, "message" => "Invalid url provided/Bypass failed"];
}


function curl($url, $post = 0, $httpheader = 0, $proxy = 0){$ch = curl_init();curl_setopt($ch, CURLOPT_URL, $url);curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);curl_setopt($ch, CURLOPT_TIMEOUT, 60);curl_setopt($ch, CURLOPT_COOKIE,TRUE);if($post){curl_setopt($ch, CURLOPT_POST, true);curl_setopt($ch, CURLOPT_POSTFIELDS, $post);}if($httpheader){$httpheader[] = 'Host: '.parse_url($url)['host'];curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);}if($proxy){curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);curl_setopt($ch, CURLOPT_PROXY, $proxy);}curl_setopt($ch, CURLOPT_HEADER, true);$response = curl_exec($ch);$httpcode = curl_getinfo($ch);if(!$httpcode) return "Curl Error : ".curl_error($ch); else{$header = substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));$body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));curl_close($ch);return array($header, $body);}}
function fly($url){ 
    $fly = ["linksfly.me", "shortsfly.me", "wefly.me", "urlsfly.me", "clicksfly.me"]; 
    $parsed = parse_url($url); 
    if (in_array($parsed["host"], $fly)) { 
        $link = "https://" . $parsed["host"] . "/my-5link-pro" . $parsed["path"]; 
        $header = []; 
        $bypass = Run1($link, $header); 
        $data = $bypass['body']; 
        if(str_contains($data, '<h2>Not Found</h2>') or str_contains($data, 'Why have I been blocked?') or str_contains($bypass['info']['redirect_url'], 'bypass')){ 
            $header = ["referer: https://advertisingexcel.com/"]; 
            $bypass = Run1($link, $header); 
            $data = $bypass['body']; 
        } 
        countdown(10,"Wait for"); 
        if(str_contains($data, "links/go")){ 
                $ad_form = rawurlencode(explode('"', explode('<input type="hidden" name="ad_form_data" value="', $data)[1])[0]);
                $csrf = rawurlencode(explode('"', explode('<input type="hidden" name="_csrfToken" autocomplete="off" value="', $data)[1])[0]);
    $token = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[fields]" autocomplete="off" value="', $data)[1])[0]);
    $token_unlock = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[unlocked]" autocomplete="off" value="', $data)[1])[0]);
           $headers = ["accept: application/json,text/javascript, */*; q=0.01", "x-requested-with:XMLHttpRequest", "content-type: application/x-www-form-urlencoded; charset=UTF-8", "referer: https://advertisingexcel.com/landing/"];        
                $link = "https://".$parsed["host"]."/flyinc./links/go"; 
                $request = "_method=POST&_csrfToken={$csrf}&ad_form_data={$ad_form}&_Token%5Bfields%5D={$token}&_Token%5Bunlocked%5D={$token_unlock}"; 
                $data = json_decode(Run($link, $headers, $request), true);
                if(isset($data['url']) && $data['url'] != '/links/go'){ 
                    return ["success" => true, "message" => "Shortlink bypass", "original_url" => $data['url']];  
                } 
            } 
            if(str_contains("bypass", $bypass['info']['redirect_url'])){ 
                return ["error" => true, "message" => "Invalid url provided"]; 
            } 
            return ["success" => true, "message" => "Shortlink bypass", "original_url" => $bypass['info']['redirect_url']]; 
        } 
        return ["error" => true, "message" => "Invalid url provided"]; 
    }

function ref($ref = ''){
    return $header = [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
        'upgrade-insecure-requests: 1',
        'sec-fetch-site: cross-site',
        'sec-fetch-mode: navigate',
        'sec-fetch-user: ?1',
        'sec-fetch-dest: document',
        'sec-ch-ua: "Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
        'sec-ch-ua-mobile: ?0',
        'sec-ch-ua-platform: "Windows"',
        'referer: ' . $ref,
        'accept-language: en-GB,en-US;q=0.9,en;q=0.8',
        'priority: u=0, i'
    ];
} 

function finish($url){ // finish. 
    global $type;
    $headers = ["accept: application/json,text/javascript, */*; q=0.01", "x-requested-with:XMLHttpRequest", "content-type: application/x-www-form-urlencoded; charset=UTF-8",]; 
    // [shortlink domain, real domain, referer for real domain]
    $pair = [
        "m.addurl.biz" => ["finish.addurl.biz", "https://adcrypto.net/"],
        "m.wdu.info" => ["finish.wdu.info", "https://oncoin.info/"],
        "m.tinygo.co" => ["thanks.tinygo.co", "https://wpcheap.net/"],
        "m.viewfr.com" => ["thanks.viewfr.com", "https://cryptfaucet.com/"],
        "m.wez.info" => ["thanks.wez.info", "https://izseo.net/"]
    ];
    $parsedUrl = parse_url($url);
    $domain = $parsedUrl['host'];
    if (empty($pair[$domain])) {
        return ["error" => true, "message" => "Shortlink not supported"];
    }
    $url = str_replace($domain, $pair[$domain][0], $url);
    $r = Run($url, ref($pair[$domain][1]));
    $ad_form = rawurlencode(explode('"', explode('<input type="hidden" name="ad_form_data" value="', $r)[1])[0]);
    $csrf = rawurlencode(explode('"', explode('<input type="hidden" name="_csrfToken" autocomplete="off" value="', $r)[1])[0]);
    $token = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[fields]" autocomplete="off" value="', $r)[1])[0]);
    $token_unlock = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[unlocked]" autocomplete="off" value="', $r)[1])[0]);
    countdown(10, "[$domain] Wait");
    $request = "_method=POST&_csrfToken={$csrf}&action=continue&page=2&_Token%5Bfields%5D={$token}&_Token%5Bunlocked%5D={$token_unlock}";
    $r = Run($url, ref(''), $request);
    $ad_form = rawurlencode(explode('"', explode('<input type="hidden" name="ad_form_data" value="', $r)[1])[0]);
    $csrf = rawurlencode(explode('"', explode('<input type="hidden" name="_csrfToken" autocomplete="off" value="', $r)[1])[0]);
    $token = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[fields]" autocomplete="off" value="', $r)[1])[0]);
    $token_unlock = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[unlocked]" autocomplete="off" value="', $r)[1])[0]);
    countdown(10, "[$domain] Wait");
    $url = "https://" . $pair[$domain][0] . "/links/go";
    $request = "_method=POST&_csrfToken={$csrf}&ad_form_data={$ad_form}&_Token%5Bfields%5D={$token}&_Token%5Bunlocked%5D={$token_unlock}";
    $data = json_decode(Run($url, $headers, $request), true);
    if (isset($data['url']) && $data['url'] != '/links/go') {
        return ["success" => true, "message" => "Shortlink bypass", "original_url" => $data['url']];
        if($type == "Y"){
            countdown(rand(150,180), "Bypassing SL");
        }else{
            countdown(rand(15,20), "Bypassing SL");
        }
    }
    return ["error" => true, "message" => "Invalid url provided/Bypass failed"];
}

function template($url){
    global $type;
    $pair = [
        "tii.la" => ["tii.la", "https://techbixby.com/"],
        "tvi.la" => ["tvi.la", "https://techbixby.com/"],
        "tpi.li" => ["tpi.li", "https://blogmystt.com/"],
        "iir.la" => ["iir.la", "https://lawyex.co/can"],
        "oii.la" => ["oii.la", "https://lawyex.co/can"]
    ];
    $parsedUrl = parse_url($url);
    $domain = $parsedUrl['host'];
    if (empty($pair[$domain])) {
        return ["error" => true, "message" => "Shortlink not supported"];
    }
    $header = ["accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7","content-type: application/x-www-form-urlencoded", "referer: ".$pair[$domain][1]];
    $headers = ["accept: application/json,text/javascript, */*; q=0.01", "x-requested-with:XMLHttpRequest", "content-type: application/x-www-form-urlencoded; charset=UTF-8",];       
    $url = str_replace($domain, $pair[$domain][0], $url);
    $data = Run($url, $header);
    $req['token'] = explode('"', explode('<input type="hidden" name="token" value="', $data)[1])[0];
    $req['c_d'] = explode('"', explode('<input type="hidden" name="c_d" value="', $data)[1])[0];
    $req['c_t'] = explode('"', explode('<input type="hidden" name="c_t" value="', $data)[1])[0];
    $req['alias'] = explode('"', explode('<input type="hidden" name="alias" value="', $data)[1])[0];
    $data = Run($url, $header, http_build_query($req));
    $adForm = rawurlencode(explode('"', explode('<input type="hidden" name="ad_form_data" value="', $data)[1])[0]);
    countdown(15, ["[".$pair[$domain][0]."] Please Wait"]);
    $link = "https://".$pair[$domain][0]."/links/go";
    $request = "_method=POST&ad_form_data=$adForm";
    $data = json_decode(Run($link, $headers, $request), true);
    if(isset($data['url']) && $data['url'] != '/links/go'){
        if($type == "Y"){
            countdown(rand(150,180), "Bypassing SL");
        }else{
            countdown(rand(15,20), "Bypassing SL");
        }
        return ["success" => true, "message" => "Shortlink bypass", "original_url" => $data['url']]; 
    }
    return ["error" => true, "message" => "Invalid url provided/Bypass failed"];
}


function xyz($url){
global $revchoice,$type,$choice;
$headers = ["accept: application/json,text/javascript, */*; q=0.01", "x-requested-with:XMLHttpRequest", "content-type: application/x-www-form-urlencoded; charset=UTF-8",];       
    
    $pair = [
        "151989.xyz" => ["cutlink.xyz", "https://writeprofit.org/"],
        "131989.xyz" => ["urlcut.pro", "https://trendzilla.club/"],
        "120898.xyz" => ["c2g.at", ""],
        "link.msfaucet.xyz" => ["msfaucet.xyz", ""],
        "droplink.co" => ["droplink.co", "https://tech5s.co/"],
        "link.adlink.click" => ["blog.adlink.click", "https://www.diudemy.com/"],
        "https://shortyfi.link/" => ["https://shortyfi.in/R/", "https://allnewspoint.in/"],
        "go.shortsme.in" => ["shortsme.in", "https://gameblog.in/"],
        "yorurl.com" => ["go.yorurl.com", "https://financebolo.com/"],
        "mdiskshortner.link" => ["mdiskshortner.link", "https://indstu.com/"],
        "adrev.link" => ["adrev.link", "https://indstu.com/"],
        "shrinkme.vip" => ["en.mrproblogger.com", "https://themezon.net/"],
        "shrinkme.ink" => ["en.mrproblogger.com", "https://themezon.net/"],
        "shrinkme.dev" => ["en.mrproblogger.com", "https://themezon.net/"],
        "linkpay.top" => ["linkpay.top", "https://blogsward.com/"],
        "linksly.co" => ["go.linksly.co", "https://mrproblogger.com/"],
        "link.pocolinks.com" => ["techsumy.xyz/blog", "https://links.supermodsmenus.com/"],
    ];

if(str_contains($url, 'tii.la') || str_contains($url, 'tvi.la') || str_contains($url, 'tpi.li') || str_contains($url, 'iir.la') || str_contains($url, 'oii.la')){
return template($url);
}elseif(str_contains($url, 'm.addurl.biz') || str_contains($url, 'm.wdu.info') || str_contains($url, 'm.tinygo.co') || str_contains($url, 'm.viewfr.com') || str_contains($url, 'm.wez.info')){
return finish($url);
}elseif(str_contains($url, 'zshort.io') || str_contains($url, 'lkfms.pro') || str_contains($url, 'btcut.io') || str_contains($url, "chainfo.xyz") || str_contains($url,'easycut.io') || str_contains($url, 'linkslice.io')){
return cutio($url);
}elseif(str_contains($url, 'exe.io')){
return exe($url);
}elseif(str_contains($url, 'clk.kim') || str_contains($url, 'clk.asia') || str_contains($url, 'clk.wiki')){
return clkwiki($url);
}

    $parsedUrl = parse_url($url);
    $domain = $parsedUrl['host'];
    if (empty($pair[$domain])) {
        return ["error" => true, "message" => "Shortlink not supported"];
    }
    if($domain == 'chainfo.xyz'){
        $url = str_replace("/next", '', $url);
    }
    $url = str_replace($domain, $pair[$domain][0], $url);
    $r = Run($url, ref($pair[$domain][1]));
    $ad_form = rawurlencode(explode('"', explode('<input type="hidden" name="ad_form_data" value="', $r)[1])[0]);
    if(empty($ad_form)){$r = Run($url, ref($pair[$domain][1])); }
    $ad_form = rawurlencode(explode('"', explode('<input type="hidden" name="ad_form_data" value="', $r)[1])[0]);
    if(empty($ad_form)){
        return ["error" => true, "message" => "Refer Change"];
}
    $csrf = rawurlencode(explode('"', explode('<input type="hidden" name="_csrfToken" autocomplete="off" value="', $r)[1])[0]);
    $token = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[fields]" autocomplete="off" value="', $r)[1])[0]);
    $token_unlock = rawurlencode(explode('"', explode('<input type="hidden" name="_Token[unlocked]" autocomplete="off" value="', $r)[1])[0]);
    countdown(15, "[" . $pair[$domain][0] . "] Please Wait");
    $url = "https://" . $pair[$domain][0] . "/links/go";
    $request = "_method=POST&_csrfToken={$csrf}&ad_form_data={$ad_form}&_Token%5Bfields%5D={$token}&_Token%5Bunlocked%5D={$token_unlock}";
    $data = json_decode(Run($url, $headers, $request), true);
    if (isset($data['url'])) {
        if (str_contains($data['url'], 'limit.php')) {
            return ["error" => true, "message" => "Shortlink limit"];
        }
        if (str_contains($data['url'], 'bypass2.php')) {
            return ["error" => true, "message" => "Bypass detect"];
        } else if ($data['url'] == '/links/go') {
            return ["error" => true, "message" => "unexpected error"];
        } else {
            if($type == "Y"){
                countdown(rand(150,180), "Bypassing SL");
            }else{
                countdown(rand(15,20), "Bypassing SL");
            }
            return ["success" => true, "message" => "Shortlink bypass", "original_url" => $data['url']];
        }
    }
    return ["error" => true, "message" => "Invalid url provided/Bypass failed"];
}

function backlink($enjoy){
    global $type1, $apikey, $apihost, $choice, $type1;
    if (str_contains($enjoy, "urlsfly.me") || str_contains($enjoy, "wefly.me") || str_contains($enjoy, "shortsfly.me") || str_contains($enjoy, 'clicksfly.me') || str_contains($enjoy,'linksfly.me')){
       return fly($enjoy);
   
}else{
return xyz($enjoy);
}
}
