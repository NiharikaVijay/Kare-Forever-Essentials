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
        p.pdname, p.30ml,p.50ml,p.100ml,p.250ml
        FROM product p LEFT OUTER JOIN
        (SELECT pdid,path FROM media WHERE isimage=TRUE AND isdefault=TRUE) m ON m.pdid=p.pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $products = $prep->fetchAll();

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
