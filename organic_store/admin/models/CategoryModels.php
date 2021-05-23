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

    public function getProducts(String $catid)
    {
        $sql = 'SELECT p.pdname, p.isactive,
        m.path,
        r.rtg, r.rtgcount,
        pct.categories,
        pcn.concerns,
        p.pdid, p.30ml, p.50ml, p.100ml, p.250ml
        FROM product p LEFT OUTER JOIN (SELECT pdid, path FROM media WHERE isimage=true AND isdefault=true) m ON m.pdid=p.pdid
        LEFT OUTER JOIN (SELECT pdid, round(avg(rating),1) AS rtg, count(rating) AS rtgcount FROM reviews GROUP BY pdid) r ON r.pdid=p.pdid
        LEFT OUTER JOIN (SELECT p.pdid, group_concat(p.name) AS concerns FROM  (SELECT pc.pdid AS pdid, c.concname AS name FROM prodconc pc LEFT OUTER JOIN concern c ON pc.concid=c.concid) p GROUP BY p.pdid) pcn ON p.pdid=pcn.pdid
        LEFT OUTER JOIN (SELECT p.pdid, group_concat(p.name) AS categories FROM  (SELECT pc.pdid AS pdid, c.catname AS name FROM prodcat pc LEFT OUTER JOIN category c ON pc.catid=c.catid) p GROUP BY p.pdid) pct ON p.pdid=pct.pdid
        WHERE p.pdid IN (SELECT pdid from prodcat WHERE catid= :catid);';
        $prep = $this->db->prepare($sql);
        $prep->execute(['catid' => $catid]);
        $list = $prep->fetchAll();

        $sql = 'SELECT catname FROM category WHERE catid= :catid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['catid' => $catid]);
        $name = $prep->fetchAll();
        
        return [
            'category' => $name[0][0],
            'list' => $list
        ];
    }
}
