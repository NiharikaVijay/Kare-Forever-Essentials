<?php

class ShopModel
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllProducts()
    {
        $sql = 'SELECT
        m.path,
        p.pdid, p.pdname, 
        FROM product p
        LEFT OUTER JOIN media m ON m.pdid = p.pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $result = $prep->fetchAll();
        return $result;
    }
    // public function getPDetails(String $cxid)
    // {
    //     $sql = 'SELECT cx.cxname, cx.cxemail, cx.cxphone,
    //     IFNULL(o.cnt, 0) AS ordercount, 
    //     IFNULL(c.cnt,0) AS cartcount, 
    //     IFNULL(w.cnt, 0 ) AS wishcount,
    //     IFNULL(o.revenue, 0) AS revenue
    //     FROM customer cx 
    //     LEFT OUTER JOIN ( SELECT cxid, count(cxid) AS cnt FROM wishlist GROUP BY cxid) w ON w.cxid = cx.cxid 
    //     LEFT OUTER JOIN ( SELECT cxid, count(cxid) AS cnt FROM cart GROUP BY cxid) c ON c.cxid = cx.cxid 
    //     LEFT OUTER JOIN (SELECT cxid, count(cxid) AS cnt , SUM(amount) AS revenue FROM orders GROUP BY cxid) o ON o.cxid = cx.cxid
    //     WHERE cx.cxid= :cxid;';
    //     $prep = $this->db->prepare($sql);
    //     $prep->execute(['cxid' => $cxid]);
    //     $details = $prep->fetchAll();
    //     return $details[0];
    // }

    // public function getCustomerOrders(String $cxid)
    // {
    //     $sql = 'SELECT o.ordid, o.timestamp, 
    //     a.line1, a.line2, a.city,a.pincode, a.state,
    //     o.amount, o.status 
    //     FROM orders AS o LEFT OUTER JOIN address AS a
    //     ON a.cxid=o.cxid
    //     where o.cxid= :cxid ';
    //     $prep = $this->db->prepare($sql);
    //     $prep->execute(['cxid' => $cxid]);
    //     $orders = $prep->fetchAll();

    //     return $orders;
    // }
}
