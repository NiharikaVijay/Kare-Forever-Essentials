<?php

class CustomerModel
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

    public function getWishlist(String $cxid)
    {
        $sql = 'SELECT m.path,
        p.pdname, p.ingredients, p.pdid,
        ROUND(p.30ml*(100-p.discount)/100,2), ROUND(p.50ml*(100-p.discount)/100, 2), ROUND(p.100ml*(100-p.discount)/100,2), ROUND(p.250ml*(100-p.discount)/100,2)
        FROM wishlist w LEFT OUTER JOIN product p ON w.pdid=p.pdid
        LEFT OUTER JOIN (SELECT pdid, path FROM media WHERE isimage=true AND isdefault=true) m
        ON m.pdid=w.pdid WHERE w.cxid= :cxid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['cxid' => $cxid]);
        $wishlist = $prep->fetchAll();

        return $wishlist;
    }
}
