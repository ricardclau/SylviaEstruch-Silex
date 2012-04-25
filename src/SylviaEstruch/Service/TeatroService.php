<?php

namespace SylviaEstruch\Service;

use Silex\Application;

class TeatroService
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $db;

    public function __construct($db = null)
    {
        if (null !== $db) {
            $this->db = $db;
        }
    }

    public function setDb($db)
    {
        $this->db = $db;
    }

    public function getDb()
    {
        return $this->db;
    }

    public function getCategories()
    {
        $sql = 'SELECT * FROM categorias_teatro';

        return $this->db->fetchAll($sql);
    }

    public function getCategoryPerformances($catId)
    {
        $sql = 'SELECT * FROM teatros where categorias_teatro_id = :catId';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('catId', $catId);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}