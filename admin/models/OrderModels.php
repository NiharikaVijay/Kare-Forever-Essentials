<?php

class OrderModel
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllOrders()
    {
        $sql = 'SELECT  o.ordid, DATE_FORMAT(o.timestamp, "%e %M %Y, %r"),
        c.cxname,
        a.line1, a.line2, a.city, a.pincode, a.state,
        o.finamt, o.status
        FROM orders o LEFT OUTER JOIN customer c ON c.cxid=o.cxid LEFT OUTER JOIN address a ON a.cxid=c.cxid;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $result = $prep->fetchAll();
        return $result;
    }
    public function getOrderDetails(String $ordid)
    {
        $sql = 'SELECT  c.cxname, c.cxemail, c.cxphone,
        DATE_FORMAT(o.timestamp, "%e %M %Y, %r"), o.notes,
        a.line1, a.line2, a.city, a.pincode, a.state,
        o.finamt, o.status, o.lptsused
        FROM orders o LEFT OUTER JOIN customer c ON c.cxid=o.cxid LEFT OUTER JOIN address a ON a.cxid=c.cxid WHERE o.ordid= :ordid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['ordid' => $ordid]);
        $details = $prep->fetchAll();

        $sql = 'SELECT m.path, 
        p.pdname, 
        o.pdvolume, o.pdprice, o.pdqty, o.pdqty*o.pdprice AS Total
        FROM orderproducts o LEFT OUTER JOIN product p ON o.pdid=p.pdid
        LEFT OUTER JOIN (SELECT path, pdid FROM media WHERE isimage=TRUE AND isdefault=TRUE) m ON m.pdid=p.pdid WHERE o.orderid= :ordid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['ordid' => $ordid]);
        $products = $prep->fetchAll();

        $sql = 'SELECT cpid, amount
        FROM couponsused WHERE ordid= :ordid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['ordid' => $ordid]);
        $coupon = $prep->fetchAll();

        return [
            "details" => $details[0],
            "products" => $products,
            "coupon" => $coupon[0],
            "total" => $details[0][10] + $coupon[0][1] + $details[0][12]
        ];
    }

    public function updateOrderStatus(String $ordid, String $status)
    {
        if ($status == 'acp') {
            $status = 'Accepted';
        } elseif ($status == 'ofd') {
            $status = 'Out';
        } elseif ($status == 'dvd') {
            $status = 'Delivered';
        } elseif ($status == 'can') {
            $status = 'Cancelled';
        }

        $sql = 'UPDATE orders SET status=:status WHERE ordid= :ordid;';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'ordid' => $ordid,
            'status' => $status
        ]);
        $prep->fetchAll();
    }
}
