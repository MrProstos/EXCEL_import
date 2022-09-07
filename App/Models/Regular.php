<?php

namespace App\Models;

class Regular
{
    const IS_VALID_SKU = '/^[^-][[:alnum:]А-Яа-я-][^\s]{1,15}$/';
    const IS_VALID_PRICE = '/[\d` ]+[.,]\d+|[\d `]+/';
    const IS_VALID_CNT = '/\d+[,.]\d+|\d+/';

    /**
     * Sku validation
     * @param string $sku
     * @return bool
     */
    public function isValidSku(string $sku): bool
    {
        if (preg_match(self::IS_VALID_SKU, $sku) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Price validation
     * @param string $price
     * @return bool
     */
    public function isValidPrice(string $price): bool
    {
        if (preg_match(self::IS_VALID_PRICE, $price) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Valid price
     * @param string $price
     * @return string
     */
    public function validPrice(string $price): string
    {
        $newPrice = preg_replace(['/[\s`]+/', '/[A-zА-я]+/u', '/^\D+|\D+$/'], '', $price);
        return preg_replace('/(\d+)[,.](\d+)/', '$1.$2', $newPrice);
    }

    /**
     * Cnt validation
     * @param string $cnt
     * @return bool
     */
    public function isValidCnt(string $cnt): bool
    {
        if (preg_match(self::IS_VALID_CNT, $cnt) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Valid cnt
     * @param string $cnt
     * @return string
     */
    public function validCnt(string $cnt): string
    {
        $newCnt = preg_replace(['/[\s`]+/', '/[A-zА-я]+/u', '/^\D+|\D+$/'], '', $cnt);
        return preg_replace('/(\d+)[,.](\d+)/', '$1.$2', $newCnt);
    }


}