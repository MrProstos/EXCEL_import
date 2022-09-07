<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use App\Models\Regular;

$reg = new Regular();

if ($reg->isValidSku('Abra-Cadabra')) {
    echo '<p>SKU TRUE</p>';
}

if ($reg->isValidPrice('руб. 1 asd256 000,56')) {
    echo '<p>PRICE TRUE</p>';
    print_r($reg->validPrice('руб. 1 256 00,056'));
}

if ($reg->isValidCnt('1200 000.78 позиций')) {
    echo '<p>CNT TRUE</p>';
    print_r($reg->validCnt('1200 000.78 позиций'));
}

