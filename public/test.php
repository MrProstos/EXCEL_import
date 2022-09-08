<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use App\Models\Regular;

$reg = new Regular();

if ($reg->isValidPrice('руб. 1 asd256 000,56')) {
    echo '<p>PRICE TRUE</p>';
    print_r($reg->validPrice('Руб 1`000000.43'));
}

if ($reg->isValidCnt('1200 000.78 позиций')) {
    echo '<p>CNT TRUE</p>';
    print_r($reg->validCnt('1200 000.78 позиций'));
}

