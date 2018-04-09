<?php

include_once "config.php";

/**
 * Чтение числа
 *
 * @param $handle
 * @return string
 */
function readNumber($handle) {
    return rtrim(fgets($handle), PHP_EOL);
}

/**
 * Запись числа
 *
 * @param $handle
 * @param $number
 */
function writeNumber($handle, $number) {
    if ($number !== '') {
        fputs($handle, $number . PHP_EOL);
    }
}

/**
 * Разбиение файла кусками размером segmentSize на два вспомогательных файла
 *
 * @param string $srcFilename
 * @param string $bFilename
 * @param string $cFilename
 * @param int $segmentSize
 *
 * @return int - сколько раз еще потрубется разбить файл для сортировки
 */
function splitFile(string $srcFilename, string $bFilename, string $cFilename, int $segmentSize) : int
{
    $srcHandle = fopen($srcFilename, "r");

    $bHandle = fopen($bFilename, "w");
    $cHandle = fopen($cFilename, "w");

    $bHandleNow = true;
    $parts = 0; // количество частей для разбивки
    while (!feof($srcHandle)) {
        $parts++;
        $handle = $bHandleNow ? $bHandle : $cHandle;
        $bHandleNow = !$bHandleNow;
        for($i=0; $i < $segmentSize; $i++) {
            writeNumber($handle, readNumber($srcHandle));
        }
    }

    fclose($srcHandle);
    fclose($bHandle);
    fclose($cHandle);

    return ceil(log($parts, 2)) - 1;
}

/**
 * Слияние дополнительных файлов с сортировкой отрезков размером segmentSize
 *
 * @param string $srcFilename
 * @param string $bFilename
 * @param string $cFilename
 * @param int $segmentSize
 */
function merge(string $srcFilename, string $bFilename, string $cFilename, int $segmentSize)
{
    $srcHandle = fopen($srcFilename, "w");
    $bHandle = fopen($bFilename, "r");
    $cHandle = fopen($cFilename, "r");

    while ( !(feof($bHandle) && feof($cHandle)) ) {
        $bCount = $cCount = 0;
        $bValue = $cValue = null;

        if (!feof($bHandle)) {
            $bValue = readNumber($bHandle);
            $bCount++;
        }

        if (!feof($cHandle)) {
            $cValue = readNumber($cHandle);
            $cCount++;
        }

        // слияние отрезков двух файлов с сортировкой
        while ($bCount + $cCount <= $segmentSize * 2) {
            if (isset($bValue) && isset($cValue)) {
                if ($bValue < $cValue) {
                    writeNumber($srcHandle, $bValue);
                    
                    if ($bCount < $segmentSize) {
                        $bValue = readNumber($bHandle);
                        $bCount++;
                    } else {
                        $bValue = null;
                    }
                } else {
                    writeNumber($srcHandle, $cValue);

                    if ($cCount < $segmentSize) {
                        $cValue = readNumber($cHandle);
                        $cCount++;
                    } else {
                        $cValue = null;
                    }
                }
            } elseif (isset($bValue)) {
                writeNumber($srcHandle, $bValue);

                if ($bCount < $segmentSize) {
                    $bValue = readNumber($bHandle);
                    $bCount++;
                } else {
                    $bValue = null;
                }
            } elseif (isset($cValue)) {
                writeNumber($srcHandle, $cValue);

                if ($cCount < $segmentSize) {
                    $cValue = readNumber($cHandle);
                    $cCount++;
                } else {
                    $cValue = null;
                }
            } else {
                break;
            }
        }
    };

    fclose($srcHandle);
    fclose($bHandle);
    fclose($cHandle);
}

/**
 * Этап сортировки слиянием
 *
 * @param string $inputFilename
 * @param string $srcFilename
 * @param string $bFilename
 * @param string $cFilename
 * @param int $segmentSize
 * @return int
 */
function mergeSort(string $inputFilename, string $srcFilename, string $bFilename, string $cFilename, int $segmentSize) {
    if ($segmentSize == 1) {
        $remainedStages = splitFile($inputFilename, $bFilename, $cFilename, $segmentSize);
    } else {
        $remainedStages = splitFile($srcFilename, $bFilename, $cFilename, $segmentSize);
    }
    merge($srcFilename, $bFilename, $cFilename, $segmentSize);

    return $remainedStages;
}


$remainedStages = $segmentSize = 1;

while ($remainedStages > 0) {
    $remainedStages = mergeSort(INPUT_FILE, RESULT_FILE, FIRST_TEMP_FILE, SECOND_TEMP_FILE, $segmentSize);

    echo "Remaned stages:" . $remainedStages .
        ' Buffer size:' . $segmentSize .
        ' Memory usage:' . memory_get_usage() . PHP_EOL;

    $segmentSize <<= 1;
}