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

    public function getCart(String $cxid)
    {
        $sql = 'SELECT m.path,
        p.pdname, p.ingredients, p.pdid,
        c.pdvolume, c.pdqty,
        ROUND(p.30ml*(100-p.discount)/100,2), ROUND(p.50ml*(100-p.discount)/100, 2), ROUND(p.100ml*(100-p.discount)/100,2), ROUND(p.250ml*(100-p.discount)/100,2)   
        FROM cart c LEFT OUTER JOIN product p ON c.pdid=p.pdid
        LEFT OUTER JOIN (SELECT pdid, path FROM media WHERE isimage=true AND isdefault=true) m
        ON m.pdid=c.pdid WHERE c.cxid= :cxid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['cxid' => $cxid]);
        $cart = $prep->fetchAll();

        $subtotal = 0;
        $volume = 0;
        for ($i = 0; $i < sizeof($cart); $i++) {
            if ($cart[$i][4] == '30ml') {
                $volume += 30;
            } else if ($cart[$i][4] == '50ml') {
                $cart[$i][6] = $cart[$i][7];
                $volume += 50;
            } else if ($cart[$i][4] == '100ml') {
                $cart[$i][6] = $cart[$i][8];
                $volume += 100;
            } else if ($cart[$i][4] == '250ml') {
                $cart[$i][6] = $cart[$i][9];
                $volume += 250;
            }
            $cart[$i][7] = $cart[$i][6] * $cart[$i][5];
            $subtotal += $cart[$i][7];
            unset($cart[$i][8]);
            unset($cart[$i][9]);
        }

        $delivery = 150 * ceil($volume / 500);
        return [
            'items' => $cart,
            'subtotal' => $subtotal,
            'delivery' => $delivery,
        ];
    }

    public function removeFromCart($cxid, $pdid)
    {
        $sql = 'DELETE FROM cart WHERE c.cxid= :cxid AND pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'cxid' => $cxid,
            'pdid' => $pdid
        ]);
    }
}
