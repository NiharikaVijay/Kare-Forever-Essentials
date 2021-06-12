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

    public function getAllProducts()
    {
        $sql = 'SELECT p.discount, m.path, p.pdid,
        p.pdname, IFNULL(p.30ml,100000),IFNULL(p.50ml,100000),IFNULL(p.100ml,100000),IFNULL(p.250ml, 100000)
        FROM product p LEFT OUTER JOIN
        (SELECT pdid,path FROM media WHERE isimage=TRUE AND isdefault=TRUE) m ON m.pdid=p.pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
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

    public function getProductsByCategory($catid)
    {
        $sql = 'SELECT p.discount, m.path, p.pdid,
        p.pdname, IFNULL(p.30ml,100000),IFNULL(p.50ml,100000),IFNULL(p.100ml,100000),IFNULL(p.250ml, 100000)
        FROM prodcat pc LEFT OUTER JOIN product p on pc.pdid=p.pdid
        LEFT OUTER JOIN (SELECT pdid,path FROM media WHERE isimage=TRUE AND isdefault=TRUE) m ON m.pdid=pc.pdid 
        WHERE pc.catid= :catid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['catid' => $catid]);
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
        p.ingredients, p.benefits, p.application,p.description,
        p.30ml, p.50ml, p.100ml, p.250ml, 
        p.pdid,
        pct.categories,
        pcn.concerns,
        ROUND(p.30ml*(100-p.discount)/100,2), ROUND(p.50ml*(100-p.discount)/100, 2), ROUND(p.100ml*(100-p.discount)/100,2), ROUND(p.250ml*(100-p.discount)/100,2),
        p.discount, p.tags
        FROM product p LEFT OUTER JOIN (SELECT pdid, round(avg(rating),1) AS rtg, count(rating) AS rtgcount FROM reviews GROUP BY pdid) r ON r.pdid=p.pdid
        LEFT OUTER JOIN (SELECT p.pdid, group_concat(p.name) AS concerns FROM  (SELECT pc.pdid AS pdid, c.concname AS name FROM prodconc pc LEFT OUTER JOIN concern c ON pc.concid=c.concid) p GROUP BY p.pdid) pcn ON p.pdid=pcn.pdid
        LEFT OUTER JOIN (SELECT p.pdid, group_concat(p.name) AS categories FROM  (SELECT pc.pdid AS pdid, c.catname AS name FROM prodcat pc LEFT OUTER JOIN category c ON pc.catid=c.catid) p GROUP BY p.pdid) pct ON p.pdid=pct.pdid WHERE p.pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $product = $prep->fetchAll();

        $product[0][4] = explode("|",$product[0][4]);

        $sql = 'SELECT path FROM media WHERE pdid= :pdid ORDER BY isdefault DESC;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $media = $prep->fetchAll();

        return [
            'details' => $product[0],
            'media' => $media,
        ];
    }
}
