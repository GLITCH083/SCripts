<?php
function wordnumber() {
    return array_change_key_case(['ONE' => '1', 'TWO' => '2', 'THREE' => '3', 'FOUR' => '4','FIVE' => '5', 'SIX' => '6', 'SEVEN' => '7', 'EIGHT' => '8','NINE' => '9', 'TEN' => '10'], CASE_UPPER);
}

function numberword() {
    return array_change_key_case(['1' => 'ONE', '2' => 'TWO', '3' => 'THREE', '4' => 'FOUR', '5' => 'FIVE', '6' => 'SIX', '7' => 'SEVEN', '8' => 'EIGHT', '9' => 'NINE', '10' => 'TEN'], CASE_UPPER);
}

function numberroman() {
    return array_change_key_case(['1' => 'I', '2' => 'II', '3' => 'III', '4' => 'IV','5' => 'V', '6' => 'VI', '7' => 'VII', '8' => 'VIII','9' => 'IX', '10' => 'X'], CASE_UPPER);
}

function romannumber() {
    return array_change_key_case(['I' => '1', 'II' => '2', 'III' => '3', 'IV' => '4','V' => '5', 'VI' => '6', 'VII' => '7', 'Vill' => '7', 'VIII' => '8','IX' => '9', 'X' => '10'], CASE_UPPER);
}

function mathans() {
    return array_change_key_case(['2-1' => '1', '1+1' => '2', '141' => '2', '1+2' => '3', '2+2' => '4', '242' => '4','3+2' => '5', '342' => '5', '2+4' => '6', '244' => '6', '3+4' => '7', '344' => '7', '4+4' => '8', '444' => '8','1+8' => '9', '148' => '9', '5+6' => '11'], CASE_UPPER);
}

function ansmath() {
    return array_change_key_case(['1' => '3-2', '2' => '8-6', '3' => '1+2', '4' => '3+1','5' => '9-4', '6' => '3+3', '7' => '6+1', '8' => '2*4','9' => '3+6', '10' => '2+8'], CASE_UPPER);
}

function oox() {
    return array_change_key_case(['--x' => 'OOX', '-x-' => 'OXO', 'x--' => 'XOO','xx-' => 'XXO', '-xx' => 'OXX', 'x-x' => 'XOX','---' => 'OOO', 'xxx' => 'XXX', 'x-x-' => 'XOXO','-x-x' => 'OXOX'], CASE_UPPER);
}

function xxx() {
    return array_change_key_case(['--x' => '--+', '-x-' => '-+-', 'x--' => '+--','xx-' => '++-', '-xx' => '-++', 'x-x' => '+-+','---' => '---', 'xxx' => '+++', 'x-x-' => '+-+-','-x-x' => '-+-+'], CASE_UPPER);
}

function xox() {
    return array_change_key_case(['--x' => 'oo+', '-x-' => 'o+o', 'x--' => '+oo','xx-' => '++o', '-xx' => 'o++', 'x-x' => '+o+','---' => 'ooo', 'xxx' => '+++', 'x-x-' => '+o+o','-x-x' => 'o+o+'], CASE_UPPER);
}

function oxo() {
    return array_change_key_case(['oox' => '--+', 'oxo' => '-+-', 'xoo' => '+--','xxo' => '++-', 'oxx' => '-++', 'xox' => '+-+','ooo' => '---', 'xxx' => '+++', 'xoxo' => '+-+-','oxox' => '-+-+'], CASE_UPPER);
}

function zoo() {
    return array_change_key_case(['zoo' => '200', 'ozo' => '020', 'ooz' => '002','soo' => '500', 'oso' => '050', 'oos' => '005','lol' => '101', 'sos' => '505', 'zoz' => '202','lll' => '111'], CASE_UPPER);
}

function ooz() {
    return array_change_key_case(['200' => 'ZOO', '020' => 'OZO', '002' => 'OOZ','500' => 'SOO', '050' => 'OSO', '005' => 'OOS','101' => 'LOL', '505' => 'SOS', '202' => 'ZOZ','111' => 'LLL'], CASE_UPPER);
}

function animals() {
    return array_change_key_case(['cat' => 'C@t', 'dog' => 'd0g', 'lion' => '1!0n','tiger' => 'T!g3r', 'monkey' => 'm0nk3y','elephant' => '31eph@nt', 'cow' => 'c0w','fox' => 'f0x', 'mouse' => 'm0us3', 'ant' => '@nt'], CASE_UPPER);
}
function compare($main, $options, $dictionaries){
    $atb = [];
    foreach($main as $key => $value){ 
        foreach($dictionaries as $indx => $val){
            foreach ($options as $index => $opt) {
                if($value == strtoupper((variation($opt, $indx)))){
                    $atb[] = $index;
                    break 2;
                } elseif($key == strtoupper((variation($opt, $indx)))){
                    $atb[] = $index;
                    break 2;
                }
            }
        }
    }
    return $atb;
}

function mainWordMatch($words, $dictionaries) {
    $found = [];
    foreach($words as $word){
        $word = preg_replace('/\s+/', '', $word);
        foreach ($dictionaries as $dickey => $dicval) {
            $varword = variation( $word, $dickey);
            foreach ($dicval as $key => $value) {
                if (strtoupper($varword) == strtoupper($value)) {
                    $found[strtoupper($varword)] = $key;
                    break 2;
                } elseif (strtoupper($varword) == strtoupper($key)) {
                    $found[strtoupper($varword)] = $value;
                    break 2;
                }
            }
        }
    }
    return $found;
}

function variation($word, $name) {
    if (in_array($name, ['numberword', 'mathans', 'ansmath'])) {
        $word = str_ireplace(['L', 'I', 'F'], '1', $word);
        $word = str_ireplace('¢', '6', $word);
        $word = str_ireplace('T', '+', $word);
        $word = str_ireplace('Z', '2', $word);
        $word = str_ireplace('}', '1', $word);
        $word = str_ireplace('34', '3', $word);
        $word = str_ireplace('Q', '7', $word);
        $word = str_ireplace('(4', '6', $word);
        $word = str_ireplace('#', '1+', $word);
        $word = str_ireplace('fen', 'ten', $word);
        $word = str_ireplace('|', '1', $word);
        $word = str_ireplace('4NREE', 'THREE', $word);
        $word = str_ireplace('142', '1+2', $word);
        $word = str_ireplace('343', '3+3', $word);
        $word = str_ireplace('SVEN', 'SEVEN', $word);
        $word = str_ireplace('give', 'five', $word);
        $word = str_ireplace('341', '3+1', $word);
        $word = str_ireplace('1+7', '1+1', $word);
        $word = str_ireplace('q', '9', $word);
        $word = str_ireplace('343', '3+3', $word);
        $word = str_ireplace('343', '3+3', $word);
        $word = str_ireplace('343', '3+4', $word);
        $word = str_ireplace('W2', '1+2', $word);
    }
    if (in_array($name, ['romannumber', 'numberroman'])) {
        $word = str_ireplace('U', 'V', $word);
        $word = str_ireplace('R', 'X', $word);
        $word = str_ireplace('1', 'I', $word);
        $word = str_ireplace('\\\\', 'VI', $word);
        $word = str_ireplace('\\', 'I', $word);
        $word = str_ireplace('|', 'I', $word);
        $word = str_ireplace('™', 'III', $word);
        $word = str_ireplace('xx', 'x', $word);
    }
    if (in_array($name, ['oxo', 'xox', 'xxx', 'oox'])) {
        $word = str_ireplace(['0', 'D'], 'O', $word);
        $word = str_ireplace(['T', 'F'], '+', $word);
    }
    if ($name === 'zoo') {
        $word = str_ireplace('0', 'O', $word);
        $word = str_ireplace('I', 'L', $word);
        $word = str_ireplace('8', 'S', $word);
        $word = str_ireplace('$', '5', $word);
        $word = str_ireplace('Z', 'Z', $word);
        $word = str_ireplace('|', 'l', $word);
        $word = str_ireplace('202', 'ZOZ', $word);
        $word = str_ireplace('S°S', 'SOS', $word);
    }
    if ($name === 'ooz') {
        $word = str_ireplace('O', '0', $word);
    }
    if ($name === 'animals') {
        $word = str_ireplace('ENT', 'ANT', $word);
        $word = str_ireplace('@RT', 'ANT', $word);
        $word = str_ireplace('TLG3R', 'TIGER', $word);
        $word = str_ireplace('ONT', 'ANT', $word);
        $word = str_ireplace('mouss', 'mouse', $word);
        $word = str_ireplace('cet', 'cat', $word);
        $word = str_ireplace('10', 'lion', $word);
        $word = str_ireplace(['0', '3', '!', '1', '@'], ['O', 'E', 'I', 'L', 'A'], $word);
    }
    return $word;
}

?>
