<?php

namespace SylviaEstruch\Service;

use Silex\Application;

class PinturaService
{
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

    public function getCategoryPaintings($catId)
    {
        $sql = 'SELECT * FROM pinturas where cat';
    }
}