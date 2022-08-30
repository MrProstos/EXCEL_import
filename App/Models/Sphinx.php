<?php

namespace App\Models;

use Core\Model;
use PDO;

class Sphinx extends Model
{
    /**
     * @var PDO Sphinx connection
     */
    static PDO $sphinxConn;
    /**
     * @var int User id
     */
    private int $idUser;

    /**
     * Get the PDO  sphinx connection
     * @return PDO
     */
    private function getSphinx(): PDO
    {
        if (!isset(self::$sphinxConn)) {
            self::$sphinxConn = new PDO('mysql:host=127.0.0.1:9306');
        }
        return self::$sphinxConn;
    }

    /**
     * Search for records
     * @param string $word A word or part of a word
     * @return array
     */
    public function searchProductName(string $word, int $page = 0): array
    {

        try {
            $page *= 5;
            $searchQuery = "@product_name ^$word*";
            $sql = $this->getDB();

            $result = $sql->prepare('SELECT id FROM reg_user WHERE passwd = ?');
            $result->execute([$_COOKIE['hash']]);

            $id = $result->fetch(PDO::FETCH_ASSOC);
            $this->idUser = $id['id'];

            $sphinx = $this->getSphinx()->prepare("SELECT * FROM price WHERE user_id = :id AND MATCH(:search)"); // IDE ругается - эт нормально

            $sphinx->bindParam(':id', $this->idUser, PDO::PARAM_INT);
            $sphinx->bindParam(':search', $searchQuery);
            $sphinx->execute();

            $id = [];
            foreach ($sphinx->fetchAll(PDO::FETCH_ASSOC) as $item) {
                $id[] = $item['id'];
            }

            if ($id === []) {
                return [];
            }

            $countId = count($id);
            $response = ['data' => [], 'nAllRow' => $countId / 5];
            $inString = str_repeat('?,', $countId - 1) . '?';

            $result = $sql->prepare("SELECT sku, product_name, supplier, price, cnt FROM price WHERE id IN ($inString)
                                            LIMIT 5 OFFSET ?");

            $result->bindValue($countId + 1, $page, PDO::PARAM_INT);
            for ($i = 0; $i < $countId; $i++) {
                $result->bindValue($i + 1, $id[$i]);
            }
            $result->execute();


            foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $value) {
                $response['data'][] = $value;
            }

            return $response;
        } catch (\PDOException) {
            return [];
        }

    }
}