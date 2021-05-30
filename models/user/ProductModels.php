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
}
