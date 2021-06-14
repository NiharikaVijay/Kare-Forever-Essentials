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
        ROUND(p.30ml*(100-p.discount)/100,2), ROUND(p.50ml*(100-p.discount)/100, 2), ROUND(p.100ml*(100-p.discount)/100,2), ROUND(p.250ml*(100-p.discount)/100,2), p.isfeatured
        FROM product p LEFT OUTER JOIN (SELECT pdid, path FROM media WHERE isimage=true AND isdefault=true) m ON m.pdid=p.pdid
        LEFT OUTER JOIN (SELECT pdid, round(avg(rating),1) AS rtg, count(rating) AS rtgcount FROM reviews GROUP BY pdid) r ON r.pdid=p.pdid
        LEFT OUTER JOIN (SELECT p.pdid, group_concat(p.name) AS concerns FROM  (SELECT pc.pdid AS pdid, c.concname AS name FROM prodconc pc LEFT OUTER JOIN concern c ON pc.concid=c.concid) p GROUP BY p.pdid) pcn ON p.pdid=pcn.pdid
        LEFT OUTER JOIN (SELECT p.pdid, group_concat(p.name) AS categories FROM  (SELECT pc.pdid AS pdid, c.catname AS name FROM prodcat pc LEFT OUTER JOIN category c ON pc.catid=c.catid) p GROUP BY p.pdid) pct ON p.pdid=pct.pdid ORDER BY p.isfeatured DESC;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $details = $prep->fetchAll();

        return $details;
    }

    public function getProductDetails($pdid)
    {
        $sql = 'SELECT p.pdname,
        r.rtg, r.rtgcount,
        p.description, p.benefits, p.application,
        p.30ml, p.50ml, p.100ml, p.250ml, 
        p.pdid,
        pct.categories,
        pcn.concerns,
        IFNULL(o.itemcount,0), IFNULL(o.ordcount,0),
        ROUND(p.30ml*(100-p.discount)/100,2), ROUND(p.50ml*(100-p.discount)/100, 2), ROUND(p.100ml*(100-p.discount)/100,2), ROUND(p.250ml*(100-p.discount)/100,2),
        p.discount, p.tags, p.isfeatured, pdi.ingredients
        FROM product p LEFT OUTER JOIN (SELECT pdid, round(avg(rating),1) AS rtg, count(rating) AS rtgcount FROM reviews GROUP BY pdid) r ON r.pdid=p.pdid
        LEFT OUTER JOIN (SELECT p.pdid, group_concat(p.name) AS concerns FROM  (SELECT pc.pdid AS pdid, c.concname AS name FROM prodconc pc LEFT OUTER JOIN concern c ON pc.concid=c.concid) p GROUP BY p.pdid) pcn ON p.pdid=pcn.pdid
        LEFT OUTER JOIN (SELECT p.pdid, group_concat(p.name) AS categories FROM  (SELECT pc.pdid AS pdid, c.catname AS name FROM prodcat pc LEFT OUTER JOIN category c ON pc.catid=c.catid) p GROUP BY p.pdid) pct ON p.pdid=pct.pdid
        LEFT OUTER JOIN (SELECT p.pdid, group_concat(p.name) AS ingredients FROM  (SELECT pc.pdid AS pdid, c.name AS name FROM proding pc LEFT OUTER JOIN ingredients c ON pc.ingid=c.ingid) p GROUP BY p.pdid) pdi ON p.pdid=pdi.pdid
        LEFT OUTER JOIN (SELECT pdid, SUM(pdqty) AS itemcount, COUNT(*) AS ordcount from orderproducts GROUP BY pdid) o ON p.pdid=o.pdid WHERE p.pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $details = $prep->fetchAll();

        $sql = 'SELECT path,isimage FROM media WHERE pdid= :pdid ORDER BY isdefault DESC;';
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

    public function getCatConIngs()
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

        $sql = 'SELECT ingid, name
        FROM ingredients;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $ings = $prep->fetchAll();
        return [
            'cat' => $cat,
            'conc' => $conc,
            'ings' => $ings
        ];
    }

    public function addProduct(
        $pdname,
        $desc,
        $ings,
        $ben,
        $app,
        $tags,
        $p30,
        $p50,
        $p100,
        $p250,
        $cat,
        $conc,
        $discount,
        $isfeatured,
        $media
    ) {
        $pdid = $this->generateRandomString(5);
        $sql = 'INSERT INTO product VALUES("' . $pdid . '", 
        "' . $pdname . '",
        "' . $ben . '",
        "' . $app . '",
        "' . $tags . '",
        "' . $isfeatured . '",
        TRUE,
        ' . $p30 . ',
        ' . $p50 . ',
        ' . $p100 . ',
        ' . $p250 . ',
        ' . $discount . ',
        ' . $desc . ');';
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

        foreach ($ings as $i) {
            $sql = 'INSERT INTO proding VALUES("' . $pdid . '", 
            "' . $i . '");';
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
                "' . substr($target_file, 2) . '",
                TRUE,
                "' . $pdid . '",
                TRUE
                );';
            } else {
                $sql = 'INSERT INTO media VALUES("' . $mdid . '", 
                "' . substr($target_file, 2) . '",
                TRUE,coupon
                "' . $pdid . '",
                FALSE
                );';
            }
            $prep = $this->db->prepare($sql);
            $prep->execute();
        }
        return;
    }

    public function changeState($isActive, $pdid)
    {
        $sql = 'UPDATE product set isactive= :isactive WHERE pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'isactive' => $isActive,
            'pdid' => $pdid
        ]);
    }

    public function deleteProduct(String $pdid)
    {
        $sql = "DELETE FROM product where pdid= :pdid;";
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'pdid' => $pdid
        ]);
    }

    public function getEditProductDetails($pdid)
    {
        $sql = 'SELECT pdname,
        description, benefits, application,
        IFNULL(30ml,0), IFNULL(50ml,0), IFNULL(100ml,0), IFNULL(250ml,0),
        pdid, tags, discount, isfeatured
        FROM product  WHERE pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $general = $prep->fetchAll();

        $sql = 'SELECT path,isimage FROM media WHERE pdid= :pdid ORDER BY isdefault DESC;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $media = $prep->fetchAll();

        $sql = 'SELECT catid, catname FROM category WHERE catid IN ( SELECT catid FROM prodcat WHERE pdid= :pdid);';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $cat = $prep->fetchAll();

        $sql = 'SELECT catid, catname FROM category WHERE catid NOT IN ( SELECT catid FROM prodcat WHERE pdid= :pdid);';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $notcat = $prep->fetchAll();

        $sql = 'SELECT concid, concname FROM concern WHERE concid IN ( SELECT concid FROM prodconc WHERE pdid= :pdid);';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $conc = $prep->fetchAll();

        $sql = 'SELECT concid, concname FROM concern WHERE concid NOT IN ( SELECT concid FROM prodconc WHERE pdid= :pdid);';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $notconc = $prep->fetchAll();

        $sql = 'SELECT ingid, name FROM ingredients WHERE ingid IN ( SELECT ingid FROM proding WHERE pdid= :pdid);';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $ings = $prep->fetchAll();

        $sql = 'SELECT ingid, name FROM ingredients WHERE ingid NOT IN ( SELECT ingid FROM proding WHERE pdid= :pdid);';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);
        $notings = $prep->fetchAll();

        return [
            'general' => $general[0],
            'media' => $media,
            'cat' => $cat,
            'notcat' => $notcat,
            'conc' => $conc,
            'notconc' => $notconc,
            'ings' => $ings,
            'notings' => $notings
        ];
    }

    public function editProduct(
        $pdid,
        $pdname,
        $desc,
        $ings,
        $ben,
        $app,
        $tags,
        $p30,
        $p50,
        $p100,
        $p250,
        $cat,
        $conc,
        $discount,
        $isfeatured,
        $media
    ) {
        $sql = 'UPDATE product SET
        pdname= :pdname,
        ingredients= :ing,
        benefits= :ben,
        application= :app,
        tags= :tags,
        30ml= :p30,
        50ml= :p50,
        100ml= :p100,
        250ml= :p250,
        discount= :discount,
        isfeatured= :isfeatured,
        description= :desc WHERE pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'pdname' => $pdname,
            'ing' => $ing,
            'ben' => $ben,
            'app' => $app,
            'tags' => $tags,
            'p30' => $p30,
            'p50' => $p50,
            'p100' => $p100,
            'p250' => $p250,
            'discount' => $discount,
            'pdid' => $pdid,
            'isfeatured' => $isfeatured,
            'desc' => $desc
        ]);

        $sql = 'DELETE FROM prodcat WHERE pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);

        foreach ($cat as $c) {
            $sql = 'INSERT INTO prodcat VALUES("' . $pdid . '", 
            "' . $c . '");';
            $prep = $this->db->prepare($sql);
            $prep->execute();
        }

        $sql = 'DELETE FROM prodconc WHERE pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);

        foreach ($conc as $c) {
            $sql = 'INSERT INTO prodconc VALUES("' . $pdid . '", 
        "' . $c . '");';
            $prep = $this->db->prepare($sql);
            $prep->execute();
        }

        $sql = 'DELETE FROM proding WHERE pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['pdid' => $pdid]);

        foreach ($ings as $i) {
            $sql = 'INSERT INTO proding VALUES("' . $pdid . '", 
        "' . $i . '");';
            $prep = $this->db->prepare($sql);
            $prep->execute();
        }

        if (sizeof($media) > 0) {
            $sql = 'DELETE FROM media WHERE pdid= :pdid;';
            $prep = $this->db->prepare($sql);
            $prep->execute(['pdid' => $pdid]);

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
                "' . substr($target_file, 2) . '",
                TRUE,
                "' . $pdid . '",
                TRUE
                );';
                } else {
                    $sql = 'INSERT INTO media VALUES("' . $mdid . '", 
                "' . substr($target_file, 2) . '",
                TRUE,
                "' . $pdid . '",
                FALSE
                );';
                }
                $prep = $this->db->prepare($sql);
                $prep->execute();
            }
        }
        return;
    }

    public function getAllIngredients()
    {
        $sql = 'SELECT * FROM ingredients;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $ings = $prep->fetchAll();

        return $ings;
    }

    public function addIngredient($name, $description, $image){
        $ingid = $this->generateRandomString(5);

        $target_dir = '../media/ingredients/';
        $target_file = $target_dir . $ingid . '-' . basename($image['name']);

        if (!move_uploaded_file($image["tmp_name"], $target_file)) {
            die();
        }

        $sql = 'INSERT INTO ingredients VALUES(:ingid, :imgpath, :name, :description);';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'ingid' => $ingid,
            'imgpath' => '/media/ingredients/' . $ingid . '-' . basename($image['name']),
            'name' => $name,
            'description' => $description
        ]);
    }

    public function deleteIngredient($ingid){
        $sql = 'DELETE FROM ingredients WHERE ingid= :ingid;';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'ingid' => $ingid
        ]);
    }
}
