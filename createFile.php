<?php

include_once "config.php";

$bufferSize = 100*1000;
$countNumbers = 100*1000*1000;

$handle = fopen(INPUT_FILE, "w");

$res = [];
for ($i=0; $i<$countNumbers; ++$i) {
    $res[] = rand(0,PHP_INT_MAX);
    if ($i % $bufferSize == 0) {
        $str = implode(PHP_EOL, $res) . PHP_EOL;
        fwrite($handle, $str);
        $res = [];
    }

    // прогресс-бар)
    if ($i % $bufferSize === 0) {
        echo ($i / $countNumbers) * 100 . '%' . PHP_EOL;
    }
}

$str = implode(PHP_EOL, $res) . PHP_EOL;
fwrite($handle, $str);
$res = [];
echo "100%" . PHP_EOL;

fclose($handle);