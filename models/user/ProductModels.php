<?php

class ProductModel
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function getAllProducts($concid = null)
    {
        if (!$concid) {
            $sql = 'SELECT p.discount, m.path, p.pdid,
            p.pdname, IFNULL(p.30ml,100000),IFNULL(p.50ml,100000),IFNULL(p.100ml,100000),IFNULL(p.250ml, 100000)
            FROM product p LEFT OUTER JOIN
            (SELECT pdid,path FROM media WHERE isimage=TRUE AND isdefault=TRUE) m ON m.pdid=p.pdid;';
            $prep = $this->db->prepare($sql);
            $prep->execute();
        } else {
            $sql = 'SELECT p.discount, m.path, p.pdid,
            p.pdname, IFNULL(p.30ml,100000),IFNULL(p.50ml,100000),IFNULL(p.100ml,100000),IFNULL(p.250ml, 100000)
            FROM product p LEFT OUTER JOIN
            (SELECT pdid,path FROM media WHERE isimage=TRUE AND isdefault=TRUE) m ON m.pdid=p.pdid
            WHERE p.pdid IN (SELECT DISTINCT(pdid) FROM prodconc WHERE concid= :concid);';
            $prep = $this->db->prepare($sql);
            $prep->execute(['concid' => $concid]);
        }
        $products = $prep->fetchAll();

        for ($i = 0; $i < sizeof($products); $i++) {
            $products[$i][4] = min($products[$i][4], $products[$i][5], $products[$i][6], $products[$i][7]);
            $products[$i][5] = $products[$i][4] * (100 - $products[$i][0]) / 100;
            unset($products[$i][6]);
            unset($products[$i][7]);
        }

        $sql = 'SELECT concid, concname FROM concern;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $concerns = $prep->fetchAll();

        return [
            'products' => $products,
            'concerns' => $concerns
        ];
    }

    public function getProductsByCategory($catid, $concid = null)
    {
        if (!$concid) {
            $sql = 'SELECT p.discount, m.path, p.pdid,
            p.pdname, IFNULL(p.30ml,100000),IFNULL(p.50ml,100000),IFNULL(p.100ml,100000),IFNULL(p.250ml, 100000)
            FROM prodcat pc LEFT OUTER JOIN product p on pc.pdid=p.pdid
            LEFT OUTER JOIN (SELECT pdid,path FROM media WHERE isimage=TRUE AND isdefault=TRUE) m ON m.pdid=pc.pdid 
            WHERE pc.catid= :catid;';
            $prep = $this->db->prepare($sql);
            $prep->execute(['catid' => $catid]);
        } else {
            $sql = 'SELECT p.discount, m.path, p.pdid,
            p.pdname, IFNULL(p.30ml,100000),IFNULL(p.50ml,100000),IFNULL(p.100ml,100000),IFNULL(p.250ml, 100000)
            FROM prodcat pc LEFT OUTER JOIN product p on pc.pdid=p.pdid
            LEFT OUTER JOIN (SELECT pdid,path FROM media WHERE isimage=TRUE AND isdefault=TRUE) m ON m.pdid=pc.pdid 
            WHERE pc.catid= :catid AND
            p.pdid IN (SELECT DISTINCT(pdid) FROM prodconc WHERE concid= :concid);';
            $prep = $this->db->prepare($sql);
            $prep->execute([
                'catid' => $catid,
                'concid' => $concid
            ]);
        }
        $products = $prep->fetchAll();

        for ($i = 0; $i < sizeof($products); $i++) {
            $products[$i][4] = min($products[$i][4], $products[$i][5], $products[$i][6], $products[$i][7]);
            $products[$i][5] = $products[$i][4] * (100 - $products[$i][0]) / 100;
            unset($products[$i][6]);
            unset($products[$i][7]);
        }

        $sql = 'SELECT c.concid, c.concname
        FROM concern c RIGHT OUTER JOIN
        (SELECT DISTINCT(concid) FROM prodconc WHERE pdid IN (SELECT pdid FROM prodcat WHERE catid= :catid)) pc ON pc.concid=c.concid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['catid' => $catid]);
        $concerns = $prep->fetchAll();

        return [
            'products' => $products,
            'concerns' => $concerns
        ];
    }

    public function getProduct(String $pdid)
    {
        $sql = 'SELECT p.pdname,
        r.rtg, r.rtgcount,
        p.pdid, p.benefits, p.application,p.description,
        p.30ml, p.50ml, p.100ml, p.250ml, 
        ROUND(p.30ml*(100-p.discount)/100,2), ROUND(p.50ml*(100-p.discount)/100, 2), ROUND(p.100ml*(100-p.discount)/100,2), ROUND(p.250ml*(100-p.discount)/100,2)
        FROM product p LEFT OUTER JOIN (SELECT pdid, round(avg(rating),1) AS rtg, count(rating) AS rtgcount FROM reviews GROUP BY pdid) r ON r.pdid=p.pdid WHERE p.pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $product = $prep->fetchAll();

        $product[0][4] = explode("|", $product[0][4]);

        $sql = 'SELECT path FROM media WHERE pdid= :pdid ORDER BY isdefault DESC;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $media = $prep->fetchAll();

        $sql = 'SELECT i.* FROM proding p LEFT OUTER JOIN ingredients i on i.ingid=p.ingid WHERE p.pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $ing = $prep->fetchAll();

        $sql = 'SELECT p.discount, m.path, p.pdid,
        p.pdname, IFNULL(p.30ml,100000),IFNULL(p.50ml,100000),IFNULL(p.100ml,100000),IFNULL(p.250ml, 100000)
        FROM prodconc pc LEFT OUTER JOIN product p on pc.pdid=p.pdid
        LEFT OUTER JOIN (SELECT pdid,path FROM media WHERE isimage=TRUE AND isdefault=TRUE) m ON m.pdid=pc.pdid 
        WHERE pc.concid IN (SELECT concid FROM prodconc WHERE pdid= :pdid);';

        $sql = 'SELECT m.path,
        p.pdid, p.discount, p.pdname,
        p.30ml, p.50ml, p.100ml, p.250ml,
        ROUND(p.30ml*(100-p.discount)/100,2), ROUND(p.50ml*(100-p.discount)/100, 2), ROUND(p.100ml*(100-p.discount)/100,2), ROUND(p.250ml*(100-p.discount)/100,2)
        FROM product p LEFT OUTER JOIN 
        (SELECT pdid,path FROM media WHERE isimage=TRUE AND isdefault=TRUE) m ON m.pdid=p.pdid 
        WHERE p.pdid IN ( SELECT DISTINCT(pdid) FROM prodconc WHERE concid IN (SELECT concid FROM prodconc WHERE pdid= :pdid) AND p.pdid!= :pdid2);';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'pdid' => $pdid,
            'pdid2' => $pdid
        ]);
        $related = $prep->fetchAll();

        return [
            'details' => $product[0],
            'media' => $media,
            'ing' => $ing,
            'related' => $related
        ];
    }
}
