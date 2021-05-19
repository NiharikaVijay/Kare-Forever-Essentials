<?php

class CategoryModel
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllCategories()
    {
        $sql = 'SELECT  c.catid, c.catname, c.imgpath,
        p.count FROM category c LEFT OUTER JOIN
        (SELECT catid, COUNT(*) AS count FROM prodcat GROUP BY catid) p ON p.catid=c.catid;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $result = $prep->fetchAll();
        return $result;
    }
}