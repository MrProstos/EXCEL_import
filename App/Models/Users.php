<?php

namespace App\Models;

use PDO;


class Users extends \Core\Model
{

    public function registrationUser(string $username, string $email, string $passwd): bool
    {
        try {
            $db = static::getDB();

            $result = $db->prepare("insert into reg_user (username, email, passwd, confirm_email) values (?,?,?,'no')");

            $hash = md5($email . $passwd);
            $result->execute([$username, $email, $hash]);

            return true;

        } catch (\PDOException $e) {

            echo $e->getMessage(); // TODO Потом удалить

            return false;
        }
    }


    public function isUser(string $hash): bool
    {
        $db = static::getDB();

        $result = $db->prepare("SELECT * FROM reg_user WHERE passwd = ?");
        $result->execute([$hash]);

        if ($result->rowCount() != 1) {

            return false;
        }

        return true;
    }


    public function checkUser(string $email, string $passwd): bool
    {
        try {
            $db = static::getDB();

            $result = $db->prepare("SELECT * FROM reg_user WHERE passwd = ? and confirm_email = 'yes'");

            $hash = md5($email . $passwd);
            $result->execute([$hash]);

            if ($result->rowCount() != 1) {

                return false;
            }
            return true;

        } catch (\PDOException $e) {

            echo $e->getMessage();  // TODO Потом удалить

            return false;
        }
    }


    public function confirmMail(string $hash): bool
    {
        try {
            $db = static::getDB();

            $result = $db->prepare("UPDATE reg_user SET confirm_email = 'yes' WHERE passwd = ?"); // TODO добавить проверку активации почты
            $result->execute([$hash]);

            if ($result->rowCount() != 1) {
                return false;
            }

            return true;

        } catch (\PDOException $e) {

            echo $e->getMessage();  // TODO Потом удалить
            return false;
        }
    }

    public function insertDataImport(array $data): bool
    {
        $db = static::getDB();

        for ($i = 0; $i < count($data["sku"]["value"]); $i++) {

            try {

                $sku = $data["sku"]["value"][$i];
                $product_name = $data["product_name"]["value"][$i];
                $supplier = $data["supplier"]["value"][$i];
                $price = $data["price"]["value"][$i];
                $cnt = $data["cnt"]["value"][$i];

                $result = $db->prepare("INSERT INTO prays (sku, product_name, supplier, price, cnt) values (?,?,?,?,?)");
                $result->execute([$sku, $product_name, $supplier, $price, $cnt]);

            } catch (\PDOException $e) {

                echo $e->getMessage();
                return false;
            }
        };
        return true;
    }

    public function showTablePrays($nRow = 0): array
    {
        try {

            $dataArr = [];
            $db = static::getDB();

            $result = $db->prepare("SELECT * FROM prays LIMIT 5 OFFSET :nRow");
            $result->bindParam(":nRow", $nRow, PDO::PARAM_INT);
            $result->execute();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $dataArr[] = $row;
            }

            return $dataArr;

        } catch (\PDOException $e) {

            echo $e->getMessage();
            return [];
        }

    }
}
