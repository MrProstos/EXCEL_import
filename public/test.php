<?php
$divisor = 0;
try {
    if ($divisor == 0) {
        trigger_error("Не могу поделить на ноль", E_USER_ERROR);
    }
} catch (Exception) {
    echo 'hello';
}
