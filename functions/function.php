
<?php
define("RED", "\033[1;31;40m");
define("GREEN", "\033[1;32;40m");
define("YELLOW", "\033[1;33;40m");
define("BLUE", "\033[1;34;40m");
define("PURPLE", "\033[1;35;40m");
define("CYAN", "\033[1;36;40m");
define("GREY", "\033[1;30;40m");
define("MONO", "\033[2;37;40m");
define("EMONO", "\033[0;37;40m");
define("ITALIC", "\033[3;37;40m");
define("NEWLINE", "\n");
define("WHITE", "\033[1;37m");
define("UNDERLINE_CYAN", "\033[4;36m");  
define("BLINKING_GREEN", "\033[5;32m");  
define("GLOWING_PURPLE", "\033[1;35;5m"); 
define("GLOWING_WHITE", "\033[1;37;5m"); 
define("BOLD_YELLOW", "\033[1;33m");
define("RESET", WHITE);
define("BG_RED", "\033[41m");  // Red background
define("BG_GREEN", "\033[42m");  // Green background
define("BG_BLUE", "\033[44m");  // Blue background
define("BG_END", "\033[0m");   // Reset background color


function saveData($folder, $filename) {
    $base = __DIR__ . "/../configs/{$folder}-config";
    if (!is_dir($base)) mkdir($base, 0777, true);

    $path = $base . "/$filename";
    if (file_exists($path)) {
        return file_get_contents($path);
    } else {
        $data = readline("Input $filename: ");
        file_put_contents($path, $data);
        return $data;
    }
}

function saveBase64Image($base64String, $filename) {
$folder = __DIR__ . "/../AB-images";
    if (!is_dir($folder)) mkdir($folder, 0777, true);


    // Remove data URL scheme if present
    if (strpos($base64String, 'base64,') !== false) {
        $base64String = explode('base64,', $base64String)[1];
    }

    $imageData = base64_decode($base64String);
    if ($imageData === false) {
        echo "Failed to decode base64 image.";
        return false;
    }

    $filePath = $folder . '/' . $filename;
    if (file_put_contents($filePath, $imageData)) {
        return $filePath;
    } else {
        echo "Failed to save image.";
        return false;
    }
}

function cf_bypass($host){
    
        $host0 = parse_url($host, PHP_URL_HOST);
    $configDir = __DIR__ . "/../configs/{$host0}-config";
    if (!is_dir($configDir)) {
        mkdir($configDir, 0777, true);
    }

    while (true) {
        $bots = exec("python cf.py $host");
        $bot = json_decode($bots, true);

        if (empty($bot['cf_clearance'])) {
            continue;
        }
        $host = parse_url($url, PHP_URL_HOST);
        // Save user-agent
        file_put_contents("$configDir/user_Agent", $bot['user-agent']);

        // Load old cookie
        $cookieFile = saveData($host, 'cookie');

        $newCfClearance = "cf_clearance=" . $bot['cf_clearance'] . ";";
        $cf = explode('cf_clearance=', $cookieFile)[1] ?? null;

        if ($cf) {
            $cf0 = explode(';', $cf)[1];
            $back = explode('cf_clearance=', $cookieFile)[0];
            $makenewcookie = $back . $newCfClearance . $cf0;
        } else {
            $makenewcookie = $newCfClearance . $cookieFile;
        }

        // Save updated cookie
        file_put_contents("$configDir/cookie", $makenewcookie);
        return;
    }
}

function autoFilterCaptcha($base64Image, $outputImageName) {
$folder = DIR . "/../AB-images";

$inputImagePath = $folder . '/temp_input.png';  
$outputImagePath = $folder . '/' . $outputImageName;  

$base64Data = explode(',', $base64Image);  
$imageData = base64_decode(end($base64Data));  
file_put_contents($inputImagePath, $imageData);  

$img = imagecreatefrompng($inputImagePath);  
if (!$img) die("Failed to open image.\n");  

$width = imagesx($img);  
$height = imagesy($img);  
$clean = imagecreatetruecolor($width, $height);  

$white = imagecolorallocate($clean, 255, 255, 255);  
$black = imagecolorallocate($clean, 0, 0, 0);  
imagefill($clean, 0, 0, $white);  

$totalGray = 0;  
$pixelCount = 0;  

// First pass to calculate average grayscale  
for ($x = 0; $x < $width; $x++) {  
    for ($y = 0; $y < $height; $y++) {  
        $rgba = imagecolorat($img, $x, $y);  
        $alpha = ($rgba & 0x7F000000) >> 24;  
        $r = ($rgba >> 16) & 0xFF;  
        $g = ($rgba >> 8) & 0xFF;  
        $b = $rgba & 0xFF;  

        if ($alpha >= 100) $r = $g = $b = 255;  

        $gray = ($r + $g + $b) / 3;  
        $totalGray += $gray;  
        $pixelCount++;  
    }  
}  

$avgGray = $totalGray / $pixelCount;  
$threshold = $avgGray * 0.8; // aggressive cutoff  

// Second pass to binarize  
for ($x = 1; $x < $width - 1; $x++) {  
    for ($y = 1; $y < $height - 1; $y++) {  
        $rgba = imagecolorat($img, $x, $y);  
        $alpha = ($rgba & 0x7F000000) >> 24;  
        $r = ($rgba >> 16) & 0xFF;  
        $g = ($rgba >> 8) & 0xFF;  
        $b = $rgba & 0xFF;  

        if ($alpha >= 100) $r = $g = $b = 255;  

        $gray = ($r + $g + $b) / 3;  

        if ($gray < $threshold) {  
            // Check surrounding pixels to keep only clustered dark points  
            $neighbors = 0;  
            for ($i = -1; $i <= 1; $i++) {  
                for ($j = -1; $j <= 1; $j++) {  
                    if ($i === 0 && $j === 0) continue;  
                    $nx = $x + $i;  
                    $ny = $y + $j;  
                    $nrgba = imagecolorat($img, $nx, $ny);  
                    $na = ($nrgba & 0x7F000000) >> 24;  
                    $nr = ($nrgba >> 16) & 0xFF;  
                    $ng = ($nrgba >> 8) & 0xFF;  
                    $nb = $nrgba & 0xFF;  

                    if ($na >= 100) $nr = $ng = $nb = 255;  

                    $ngray = ($nr + $ng + $nb) / 3;  
                    if ($ngray < $threshold) $neighbors++;  
                }  
            }  

            if ($neighbors >= 3) {  
                imagesetpixel($clean, $x, $y, $black);  
            }  
        }  
    }  
}  

// Optional: Strengthen text color to darker gray (OCR friendly)  
for ($x = 0; $x < $width; $x++) {  
    for ($y = 0; $y < $height; $y++) {  
        $color = imagecolorat($clean, $x, $y);  
        $r = ($color >> 16) & 0xFF;  
        $g = ($color >> 8) & 0xFF;  
        $b = $color & 0xFF;  

        if ($r === 0 && $g === 0 && $b === 0) {  
            $r = $g = $b = 50;  
            imagesetpixel($clean, $x, $y, imagecolorallocate($clean, $r, $g, $b));  
        }  
    }  
}  

imagepng($clean, $outputImagePath);  
imagedestroy($img);  
imagedestroy($clean);  
unlink($inputImagePath);

}
function extractText($imageName) {
    $folder = __DIR__ . "/../AB-images";
    $imagePath = $folder . '/' . $imageName;
    $outputFile = $folder . '/result';

    $command = "tesseract \"$imagePath\" \"$outputFile\" 2>&1";
    exec($command, $output, $returnCode);

    if ($returnCode !== 0) {
        echo "Tesseract OCR failed:\n";
        echo implode("\n", $output);
        return '';
    }

    $text = @file_get_contents($outputFile . '.txt');
    if ($text === false) {
        echo "Failed to read OCR output.\n";
        return '';
    }

    return trim($text);
}


function getimg(){
$folder = __DIR__ . "/../AB-images";
$images = glob($folder . '/*.jpg'); // Get all PNG images in the folder
return $images;
// Loop through and display the image file names
foreach ($images as $image) {
    echo "Found image: " . basename($image) . "\n";
}
}

function banner($sc){
    global $l;
fast($l);
echo YELLOW . "                    〔 ". GREEN . $sc . YELLOW . " 〕           \n";
fast($l);

}


//system('clear');
$l = str_repeat("━", 60) . GLOWING_WHITE . NEWLINE;
fast($l);
function fast($arr){
    $char = str_split($arr);
    foreach($char as $animated){
        echo $animated;
        usleep(5000);
    }
}




function first_part($message, $width, $wait) {
    // Pad the message to center it
    $animated_message = str_pad($message, $width, ' ', STR_PAD_BOTH);
    $msg_effect = "";
    
    // Create the animated effect
    for ($i = 0; $i < strlen($animated_message) - 1; $i++) {
        $msg_effect .= $animated_message[$i];
        // Output the current state of the animation
        echo "\r" . $msg_effect;
        flush(); // Force output to be displayed immediately
        usleep(30000); // 0.03 seconds
    }

    if ($wait) {
        sleep(1); // Wait for 1 second if the message is short
    }
}

function animation($message) {
    // Define the number of characters to display at once
    $width = 60;
    // Determine if the message fits within the width
    $msg_effect = substr($message, 0, $width);
    $wait = strlen($message) <= $width;

    // Print the first part of the message
    first_part($msg_effect, $width, $wait);

    // If the message is longer, scroll it horizontally
    if (strlen($message) > $width) {
        for ($i = $width; $i < strlen($message); $i++) {
            // Shift the message by removing the first character and adding the next
            $msg_effect = substr($msg_effect, 1) . $message[$i];
            echo "\r" . $msg_effect;
            flush(); // Ensure the updated message is printed immediately
            usleep(100000); // 0.1 seconds delay between scrolls
        }
    }
    sleep(1);
    echo "\r" . str_repeat(' ', $width) . "\r";
    flush();
 

}







function action($action) {
    // Define date and time in the required format
    $now = date("d/M/Y H:i:s");

    // Calculate the total length for spacing
    $total_length = strlen($action) + strlen($now) + 5;
    $space_count = 50 - $total_length;

    // Format the message
    $msg = strtoupper($action) . " " . $now . str_repeat(" ", max(0, $space_count));

    // Define color codes (these may vary based on the environment)
    $bg_red = "\033[41m";
    $white = "\033[97m";
    $res = "\033[0m";
    $red = "\033[31m";
    $end = "\033[0m";

    // Print the message with colors
    echo "{$bg_red} {$white}{$msg}  {$res}{$red}⫸{$res}{$end}\n";
}
function Run($url, $head = 0, $post = 0, $data = "data") {
    $host = parse_url($url, PHP_URL_HOST);
    $folder = "configs/{$host}-config";
    if (!is_dir($folder)) mkdir($folder, 0777, true);
    $cookieFile = "$folder/cookie.txt";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_3);
    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLS_AES_128_GCM_SHA256:ECDHE-RSA-AES128-GCM-SHA256');


    if ($post) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }

    if ($head && is_array($head)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    }

    curl_setopt($ch, CURLOPT_HEADER, true);
    $r = curl_exec($ch);
    if ($data == "info") return curl_getinfo($ch);

    if (!$r) return "Curl error: " . curl_error($ch);

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($r, $headerSize);
    curl_close($ch);
    return $body;
}


function Run1($url, $head = 0, $post = 0) {
    $host = parse_url($url, PHP_URL_HOST);
    $folder = "configs/{$host}-config";
    if (!is_dir($folder)) mkdir($folder, 0777, true);
    $cookieFile = "$folder/cookie.txt";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_3);
    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLS_AES_128_GCM_SHA256:ECDHE-RSA-AES128-GCM-SHA256');


    if ($post) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }

    if ($head && is_array($head)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    }

    curl_setopt($ch, CURLOPT_HEADER, true);
    $r = curl_exec($ch);

    if (!$r) return "Curl error: " . curl_error($ch);

    $info = curl_getinfo($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($r, 0, $headerSize);
    $body = substr($r, $headerSize);
    curl_close($ch);
    return ['header' => $header, 'body' => $body, 'info' => $info];
}


function clear() {
    // Check if the PHP script is running on a Windows system
    if (stripos(PHP_OS, 'WIN') === 0) {
        // Clear the screen on Windows CMD
        pclose(popen('cls', 'w'));
    } else {
        // Use 'clear' command to clear the screen on Termux or other Unix-based terminals
        passthru('clear');
    }
}



function countdown($duration, $message) {
    $colors = [GREEN, WHITE, YELLOW, BLUE, PURPLE, CYAN];
    $colorIndex = 0;
    
    $totalDuration = $duration; // Store total duration for percentage calculation
    $startTime = microtime(true); // Start time for the countdown
    $arrowLength = 1; // Initial arrow length
    
    // Start the countdown loop with sub-second updates
    while (true) {
        $elapsedTime = microtime(true) - $startTime; // Elapsed time in seconds
        $remainingTime = $duration - $elapsedTime; // Remaining time

        if ($remainingTime <= 0) {
            $remainingTime = 0; // Prevent negative remaining time
            break; // Exit when the timer reaches 0
        }

        $hours = floor($remainingTime / 3600);
        $minutes = floor(($remainingTime % 3600) / 60);
        $seconds = $remainingTime % 60;

        // Calculate the percentage of the countdown (in milliseconds)
        $percentage = (($totalDuration - $remainingTime) / $totalDuration) * 100;

        // Generate the animated arrow sequence (only grows once per second)
// Update arrow length with reset every 5 seconds
if (floor($elapsedTime) >= $arrowLength) {
    $arrowLength = (floor($elapsedTime) % 5) + 1; // Loop from 1 to 5
}
$arrow = str_repeat('=', $arrowLength - 1) . '>';


        // Print the message, time, colored arrow, and the percentage on the opposite side
printf("\r" . $message . ' ' . WHITE . "%02d:%02d:%02d " . $arrow . " " . str_repeat(" ", 20) . "%.2f%%", $hours, $minutes, $seconds, $percentage);

        sleep(0.01); // Sleep for 10 milliseconds to create smooth percentage update
        $colorIndex++; // Change color for next cycle
    }

    // Ensure 100% is printed when the countdown hits 00:00
echo "\r                         \r";
}


function api_link($url_id, $api) {
    $url = 'https://tertuyul.my.id/apikey/';
    $pos = json_encode([
        "request" => "api_linkglitch",
        "apikey"  => $api,
        "url"     => $url_id
    ]);
    $r = json_decode(Run($url, 0, $pos), true);
    if (isset($r["error"]) && $r["error"]) {
        exit($r["error"] . "\n");
    }
    if (isset($r["fail"]) && $r["fail"]) {
        return ["fail" => $r["fail"]];
    }
    if (isset($r["msg"]) && $r["msg"]) {
        return ["error" => $r["msg"]];
    }
    $result = [];
    if (isset($r["url"])) {
        $result["url"] = $r["url"];
    }
    if (isset($r["balance"])) {
        $result["balance"] = $r["balance"];
    }
    return $result;
}

function displayBanner($text) {
    $width = 60;
    $border = str_repeat("━", $width);
    $padding = ($width - strlen($text)) / 2;
    $centeredText = str_repeat(" ", max(0, floor($padding))) . $text;

    echo "$border\n";
    echo "$centeredText\n";
    echo "$border\n";
}




function rewardbox($host, $details = []) {
    $topLeft = "╭";
    $topRight = "╮";
    $bottomLeft = "╰";
    $bottomRight = "╯";
    $horizontal = "─";
    $vertical = "│";

    $lines = [];
    $maxVisibleLength = 0;
    foreach ($details as $label => $value) {
        $line = YELLOW . "$label" . GREEN . " $value" . CYAN;
        $lines[] = $line;
        $visibleLength = strlen(stripColors($line));
        if ($visibleLength > $maxVisibleLength) {
            $maxVisibleLength = $visibleLength;
        }
    }

    $visibleHostLen = strlen(stripColors($host));
    $width = max($maxVisibleLength, $visibleHostLen) + 20;  // Increase width by 8 to make the box wider

    $topText = WHITE . " $host " . CYAN;
    $topPad = ($width - strlen(stripColors($topText)) - 2) / 2;
    $topLine = $topLeft . str_repeat($horizontal, floor($topPad)) . $topText . str_repeat($horizontal, ceil($topPad)) . $topRight;

    $middleLines = '';
    foreach ($lines as $line) {
        $visibleLen = strlen(stripColors($line));
        $middleLines .= $vertical . ' ' . $line . str_repeat(' ', $width - 3 - $visibleLen) . $vertical . PHP_EOL;
    }

    $bottomLine = $bottomLeft . str_repeat($horizontal, $width - 2) . $bottomRight;

    
    // Box contents
    echo CYAN . $topLine . PHP_EOL;
    echo $middleLines . CYAN;
    echo CYAN . $bottomLine . RESET . PHP_EOL;

}

function stripColors($text) {
    $result = '';
    $len = strlen($text);
    $i = 0;
    while ($i < $len) {
        if ($text[$i] === "\033" && isset($text[$i + 1]) && $text[$i + 1] === '[') {
            $i += 2;
            while ($i < $len && $text[$i] !== 'm') {
                $i++;
            }
            $i++; // Skip the 'm'
        } else {
            $result .= $text[$i];
            $i++;
        }
    }
    return $result;
}

function unlinkData($host, $key = null) {
    $folder = "configs/{$host}-config";
    if (!is_dir($folder)) return false;

    if ($key === null) {
        // Delete entire config folder and contents
        $files = glob("$folder/*");
        foreach ($files as $file) {
            if (is_file($file)) unlink($file);
        }
        return rmdir($folder);
    } else {
        // Delete only specific key file
        $file = "$folder/$key";
        if (file_exists($file)) {
            return unlink($file);
        }
    }
    return false;
}

   

function bannerBox($text) {
    $boxWidth = 60; // Fixed wide box
    $padding = floor(($boxWidth - strlen($text)) / 2);

    $top = "╔" . str_repeat("═", $boxWidth) . "╗\n";
    $middle = "║" . str_repeat(" ", $padding) . YELLOW . $text . WHITE . CYAN . str_repeat(" ", $boxWidth - strlen($text) - $padding) . "║\n";
    $bottom = "╚" . str_repeat("═", $boxWidth) . "╝\n";

    echo CYAN . $top . $middle . $bottom . WHITE;
}


function showBannerBox($host) {
    $cyan = "\e[96m";
    $white = "\e[97m";
    $reset = "\e[0m";

    $lines = [
        "Script made by @glitch_394",
        "Thanks To @scpwhite",
        "Script $host",
        "Note: premium script Not for sell."
    ];

    $width = 60;
    $border = str_repeat("═", $width - 2);

    echo $cyan . "╔" . $border . "╗" . $reset . PHP_EOL;

    foreach ($lines as $line) {
        $padding = ($width - 2 - strlen(strip_tags($line))) / 2;
        $left = floor($padding);
        $right = ceil($padding);
        echo $cyan . "║" . $reset;
        echo str_repeat(" ", $left) . $white . $line . $reset . str_repeat(" ", $right);
        echo $cyan . "║" . $reset . PHP_EOL;
    }

    echo $cyan . "╚" . $border . "╝" . $reset . PHP_EOL;
}


function styledNumberBox($number) {
    $cyan = "\e[96m";
    $bold = "\e[1m";
    $reset = "\e[0m";
    echo $cyan . $bold . "[" . $number . "]" . $reset;
}


