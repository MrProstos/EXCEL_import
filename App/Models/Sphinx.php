<?php

namespace App\Models;

use Core\Model;
use PDO;

class Sphinx extends Model
{
    static PDO $sphinxConn;

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

    public function searchSku(string $word): array
    {
        $searchQuery = "@product_name ^$word*";
        try {
            $sql = $this->getDB()->prepare('SELECT id FROM reg_user WHERE passwd = ?');
            $sql->execute([$_COOKIE['hash']]);
            $id = $sql->fetch(PDO::FETCH_ASSOC);

            $sphinx = $this->getSphinx()->prepare("SELECT * FROM price WHERE user_id = :id AND MATCH(:search)"); // IDE ругается - эт нормально

            $sphinx->bindParam(':id', $id['id'], PDO::PARAM_INT);
            $sphinx->bindParam(':search', $searchQuery);
            $sphinx->execute();

            return $sphinx->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return [];
        }

    }
}