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
        $sql = 'SELECT p.pdname, p.isactive,
        m.path,
        IFNULL(r.rtg,5) , IFNULL(r.rtgcount, 0),
        pct.categories,
        pcn.concerns,
        p.pdid, p.30ml, p.50ml, p.100ml, p.250ml,
        ROUND(p.30ml*(100-p.discount)/100,2), ROUND(p.50ml*(100-p.discount)/100, 2), ROUND(p.100ml*(100-p.discount)/100,2), ROUND(p.250ml*(100-p.discount)/100,2)
        FROM product p LEFT OUTER JOIN (SELECT pdid, path FROM media WHERE isimage=true AND isdefault=true) m ON m.pdid=p.pdid
        LEFT OUTER JOIN (SELECT pdid, round(avg(rating),1) AS rtg, count(rating) AS rtgcount FROM reviews GROUP BY pdid) r ON r.pdid=p.pdid
        LEFT OUTER JOIN (SELECT p.pdid, group_concat(p.name) AS concerns FROM  (SELECT pc.pdid AS pdid, c.concname AS name FROM prodconc pc LEFT OUTER JOIN concern c ON pc.concid=c.concid) p GROUP BY p.pdid) pcn ON p.pdid=pcn.pdid
        LEFT OUTER JOIN (SELECT p.pdid, group_concat(p.name) AS categories FROM  (SELECT pc.pdid AS pdid, c.catname AS name FROM prodcat pc LEFT OUTER JOIN category c ON pc.catid=c.catid) p GROUP BY p.pdid) pct ON p.pdid=pct.pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $details = $prep->fetchAll();

        return $details;
    }

    public function getProductDetails($pdid)
    {
        $sql = 'SELECT p.pdname,
        r.rtg, r.rtgcount,
        p.ingredients, p.benefits, p.application,
        p.30ml, p.50ml, p.100ml, p.250ml, 
        p.pdid,
        pct.categories,
        pcn.concerns,
        IFNULL(o.itemcount,0), IFNULL(o.ordcount,0),
        ROUND(p.30ml*(100-p.discount)/100,2), ROUND(p.50ml*(100-p.discount)/100, 2), ROUND(p.100ml*(100-p.discount)/100,2), ROUND(p.250ml*(100-p.discount)/100,2),
        p.discount
        FROM product p LEFT OUTER JOIN (SELECT pdid, round(avg(rating),1) AS rtg, count(rating) AS rtgcount FROM reviews GROUP BY pdid) r ON r.pdid=p.pdid
        LEFT OUTER JOIN (SELECT p.pdid, group_concat(p.name) AS concerns FROM  (SELECT pc.pdid AS pdid, c.concname AS name FROM prodconc pc LEFT OUTER JOIN concern c ON pc.concid=c.concid) p GROUP BY p.pdid) pcn ON p.pdid=pcn.pdid
        LEFT OUTER JOIN (SELECT p.pdid, group_concat(p.name) AS categories FROM  (SELECT pc.pdid AS pdid, c.catname AS name FROM prodcat pc LEFT OUTER JOIN category c ON pc.catid=c.catid) p GROUP BY p.pdid) pct ON p.pdid=pct.pdid
        LEFT OUTER JOIN (SELECT pdid, SUM(pdqty) AS itemcount, COUNT(*) AS ordcount from orderproducts GROUP BY pdid) o ON p.pdid=o.pdid WHERE p.pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $details = $prep->fetchAll();

        $sql = 'SELECT path,isimage FROM media WHERE pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $media = $prep->fetchAll();

        $sql = 'SELECT  c.cxname,
        r.rating, r.message
        FROM reviews r LEFT OUTER JOIN customer c ON c.cxid=r.cxid WHERE pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $reviews = $prep->fetchAll();

        return [
            'details' => $details[0],
            'media' => $media,
            'reviews' => $reviews

        ];
    }

    public function getCatCon()
    {
        $sql = 'SELECT catid, catname
        FROM category;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $cat = $prep->fetchAll();

        $sql = 'SELECT concid, concname
        FROM concern;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $conc = $prep->fetchAll();

        return [
            'cat' => $cat,
            'conc' => $conc
        ];
    }

    public function addProduct(
        $pdname,
        $ing,
        $ben,
        $app,
        $tags,
        $p30,
        $p50,
        $p100,
        $p250,
        $cat,
        $conc,
        $media
    ) {
        $pdid = $this->generateRandomString(5);
        $sql = 'INSERT INTO product VALUES("' . $pdid . '", 
        "' . $pdname . '",
        "' . $ing . '",
        "' . $ben . '",
        "' . $app . '",
        "' . $tags . '",
        FALSE,
        TRUE,
        ' . $p30 . ',
        ' . $p50 . ',
        ' . $p100 . ',
        ' . $p250 . ');';
        $prep = $this->db->prepare($sql);
        $prep->execute();

        foreach ($cat as $c) {
            $sql = 'INSERT INTO prodcat VALUES("' . $pdid . '", 
            "' . $c . '");';
            $prep = $this->db->prepare($sql);
            $prep->execute();
        }

        foreach ($conc as $c) {
            $sql = 'INSERT INTO prodconc VALUES("' . $pdid . '", 
            "' . $c . '");';
            $prep = $this->db->prepare($sql);
            $prep->execute();
        }

        $target_dir = '../media/products/';
        $nimages = sizeof($media['name']);
        for ($i = 0; $i < $nimages; $i++) {
            $mdid = $this->generateRandomString(6);
            $target_file = $target_dir . $pdid . '-' . $mdid . '-' . basename($media['name'][$i]);

            if (!move_uploaded_file($media["tmp_name"][$i], $target_file)) {
                die();
            }

            if ($i == 0) {
                $sql = 'INSERT INTO media VALUES("' . $mdid . '", 
                "' . $target_file . '",
                TRUE,
                "' . $pdid . '",
                TRUE
                );';
            } else {
                $sql = 'INSERT INTO media VALUES("' . $mdid . '", 
                "' . $target_file . '",
                TRUE,
                "' . $pdid . '",
                FALSE
                );';
            }
            $prep = $this->db->prepare($sql);
            $prep->execute();
        }
        return;
    }
}