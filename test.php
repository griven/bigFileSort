<?php

include_once "config.php";

$howOffen = 1000*100;

$handle = fopen(RESULT_FILE, "r");

$value = null;
$i = 0;
$result = true;

while (!feof($handle)) {
    $lastValue = $value;
    $value = (int)fgets($handle);

    // информация о неотсортированных данных
    if ($lastValue !== null && $lastValue > $value && !feof($handle)) {
        echo 'bad ' . $i . PHP_EOL;
        echo $lastValue . '>' . $value . PHP_EOL;
        $result = false;
        break;
    }

    // прогресс-бар
    if (($i % $howOffen) === 0) {
        echo ($i / $howOffen) . '--' . $i . '=' . $value . PHP_EOL;
    }

    ++$i;
}

fclose($handle);
var_dump($result);