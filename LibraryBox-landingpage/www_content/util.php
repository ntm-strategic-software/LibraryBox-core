<?php

function hexNums($num) {
    $chars = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f"];
    $str = "";
    for($i = 0; $i < $num; $i++) {
        $idx = rand(0, 15);
        $str = $str . $chars[$idx];
    }
    return $str;
}

function uuid() {

    // return hexNums(8) . "-" . hexNums(4) . "-" . hexNums(4) . "-" . hexNums(4) . "-" . hexNums(12);
    return hexNums(8) . hexNums(4) . hexNums(4) . hexNums(4) . hexNums(12);
}

function permissionsHash($str) {
    if(strlen($str) < 12) {
        $pArr = array();
        for($i = 0; $i < 12; $i++) {
            $pArr[$i] = 0;
        }
        return $pArr;
    } else {
        $pArr = array();
        for($i = 0; $i < 6; $i++) {
            $idx = $i * 2;
            $num = (int)substr($str, $idx + 1, 1);
            $pArr[$i] = $num;
        }
        return $pArr;
    }
}

?>
