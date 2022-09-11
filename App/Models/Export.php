<?php

namespace App\Models;

use PDO;

class Export extends \Core\Model
{
    /**
     * @param string $hash
     * @return int
     */
    public function getUserIdByHash(string $hash): int
    {
        $result = $this->getDB()->prepare('SELECT id FROM reg_user WHERE passwd = :hash');
        $result->execute([':hash' => $hash]);
        $row = $result->fetchAll(PDO::FETCH_ASSOC);
        return $row[0]['id'];
    }

    /**
     * @return array
     */
    public function export(): array
    {
        $id = $this->getUserIdByHash($_COOKIE['hash']);

        $result = $this->getDB()->prepare('SELECT sku, product_name, supplier, price, cnt FROM price 
                                               WHERE user_id = :id');
        $result->bindValue(':id', $id, PDO::PARAM_INT);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}