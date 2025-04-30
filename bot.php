<?php
include_once __DIR__ . "/functions/function.php";
include_once __DIR__ . "/functions/captcha.php";
include_once __DIR__ . "/functions/sl.php";

while (true) {
    clear();
    showBannerBox();
    echo WHITE . "[" . CYAN . '1' . WHITE . "]" . CYAN . " Show Script List" . PHP_EOL;
    echo WHITE . "[" . CYAN . '2' . WHITE . "]" . CYAN . " Update Scripts" . PHP_EOL;
    echo WHITE . "[" . CYAN . '3' . WHITE . "]" . CYAN . " Exit" . WHITE . PHP_EOL;
    echo fast($l);
    echo "Enter option: ";
    $handle = fopen("php://stdin", "r");
    $option = trim(fgets($handle));
   echo fast($l);
    switch ($option) {
        case '1':
            showScriptList();
            break;
        case '2':
            updateRepository();
            break;
        case '0':
            echo "Goodbye!" . PHP_EOL;
            exit;
        default:
            echo "Invalid option. Please try again." . PHP_EOL;
            sleep(1);
    }
}

// Show list and run selected script
function showScriptList() {
    global $l;
    $dir = 'scritps';
    $folders = array_values(array_filter(glob($dir . '/*'), 'is_dir'));

    if (empty($folders)) {
        echo "No script folders found." . PHP_EOL;
        sleep(2);
        return;
    }

    while (true) {
        clear();
        showBannerBox();
        foreach ($folders as $index => $folder) {
            echo WHITE . "[" . CYAN . "$index" . WHITE . "] " . CYAN . basename($folder) . PHP_EOL;
        }
        echo WHITE . "[" . CYAN . "X" . WHITE . "]" . CYAN . " Back" . PHP_EOL;
        echo fast($l);
        echo "Enter folder number: ";
        $handle = fopen("php://stdin", "r");
        $input = trim(fgets($handle));
        echo fast($l);
        if (strtolower($input) === 'x') return;

        if (is_numeric($input) && isset($folders[$input])) {
            $selectedFolder = basename($folders[$input]);
            echo "You selected: $selectedFolder" . PHP_EOL;
            sleep(1);
            runScript($selectedFolder);
            return;
        } else {
            echo "Invalid selection. Please try again." . PHP_EOL;
            sleep(1);
        }
    }
}

// Run bot.php inside selected folder
function runScript($folderName) {
    $configMain = __DIR__ . "/configs";
    if (!is_dir($configMain)) {
        mkdir($configMain);
    }

    $botPath = __DIR__ . "/scritps/$folderName/bot.php";
    if (file_exists($botPath)) {
        include_once $botPath;
    } else {
        echo "bot.php not found in '$folderName'." . PHP_EOL;
        sleep(2);
    }
}

// Git update function
function updateRepository() {
    $repoPath = __DIR__;

    if (!is_dir($repoPath . '/.git')) {
        echo "No Git repository found. Initializing..." . PHP_EOL;
        exec("cd \"$repoPath\" && git init && git remote add origin https://github.com/GLITCH083/SCripts.git");
    }

    echo "Updating repository..." . PHP_EOL;
    exec("cd \"$repoPath\" && git pull 2>&1", $output, $returnCode);

    if ($returnCode === 0) {
        echo GREEN . "Repository updated successfully!" . WHITE . PHP_EOL;
    } else {
        echo RED . "Failed to update repository:" . WHITE . PHP_EOL;
        foreach ($output as $line) {
            echo $line . PHP_EOL;
        }
    }

    echo "Press Enter to return to menu...";
    fgets(STDIN);
}


function showBannerBox() {
    $cyan = "\e[96m";
    $white = "\e[97m";
    $reset = "\e[0m";

    $lines = [
        "Script made by @glitch_394",
        "Thanks To @scpwhite",
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