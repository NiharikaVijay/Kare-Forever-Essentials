<?php

class CustomerModel
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllCustomers()
    {
        $sql = 'SELECT cx.cxid, cx.cxname, cx.cxemail, cx.cxphone,
        IFNULL(o.cnt, 0) AS ordercount, 
        IFNULL(c.cnt,0) AS cartcount, 
        IFNULL(w.cnt, 0 ) AS wishcount,
        cx.lpts
        FROM customer cx 
        LEFT OUTER JOIN ( SELECT cxid, count(cxid) AS cnt FROM wishlist GROUP BY cxid) w ON w.cxid = cx.cxid 
        LEFT OUTER JOIN ( SELECT cxid, count(cxid) AS cnt FROM cart GROUP BY cxid) c ON c.cxid = cx.cxid 
        LEFT OUTER JOIN (SELECT cxid, count(cxid) AS cnt FROM orders GROUP BY cxid) o ON o.cxid = cx.cxid;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $result = $prep->fetchAll();
        return $result;
    }
    public function getCustomerDetails(String $cxid)
    {
        $sql = 'SELECT cx.cxname, cx.cxemail, cx.cxphone,
        IFNULL(o.cnt, 0) AS ordercount, 
        IFNULL(c.cnt,0) AS cartcount, 
        IFNULL(w.cnt, 0 ) AS wishcount,
        IFNULL(o.revenue, 0) AS revenue,
        cx.lpts, cx.referral
        FROM customer cx 
        LEFT OUTER JOIN ( SELECT cxid, count(cxid) AS cnt FROM wishlist GROUP BY cxid) w ON w.cxid = cx.cxid 
        LEFT OUTER JOIN ( SELECT cxid, count(cxid) AS cnt FROM cart GROUP BY cxid) c ON c.cxid = cx.cxid 
        LEFT OUTER JOIN (SELECT cxid, count(cxid) AS cnt , SUM(finamt) AS revenue FROM orders GROUP BY cxid) o ON o.cxid = cx.cxid
        WHERE cx.cxid= :cxid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['cxid' => $cxid]);
        $details = $prep->fetchAll();
        return $details[0];
    }

    public function getCustomerOrders(String $cxid)
    {
        $sql = 'SELECT o.ordid, o.timestamp, 
        a.line1, a.line2, a.city,a.pincode, a.state,
        o.finamt, o.status 
        FROM orders AS o LEFT OUTER JOIN address AS a
        ON a.cxid=o.cxid
        where o.cxid= :cxid ;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['cxid' => $cxid]);
        $orders = $prep->fetchAll();

        return $orders;
    }

    public function getCustomerCart($cxid){
        $sql = 'SELECT m.path,
        p.pdname, c.pdvolume, p.30ml,
        p.50ml, p.100ml, p.250ml, c.pdqty
        FROM cart c  LEFT OUTER JOIN product p ON c.pdid=p.pdid
        LEFT OUTER JOIN (SELECT pdid, path FROM media WHERE isimage=TRUE AND isdefault=TRUE) m ON m.pdid=c.pdid WHERE c.cxid= :cxid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['cxid' => $cxid]);
        $cart = $prep->fetchAll();

        return $cart;
    }

    public function getCustomerWishlist($cxid){
        $sql = 'SELECT m.path,
        p.pdname
        FROM wishlist w  LEFT OUTER JOIN product p ON w.pdid=p.pdid
        LEFT OUTER JOIN (SELECT pdid, path FROM media WHERE isimage=TRUE AND isdefault=TRUE) m ON m.pdid=w.pdid WHERE w.cxid= :cxid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['cxid' => $cxid]);
        $wishlist = $prep->fetchAll();

        return $wishlist;
    }

}
