<?php

namespace SylviaEstruch\Service;

use Silex\Application;

class PinturaService
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
        $sql = 'SELECT * FROM categorias_pintura';

        return $this->db->fetchAll($sql);
    }

    public function getCategory($catId)
    {
        $sql = 'SELECT * FROM categorias_pintura where id = :catId';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('catId', $catId);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getCategoryPaintings($catId)
    {
        $sql = 'SELECT * FROM pinturas where categorias_pintura_id = :catId';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('catId', $catId);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}