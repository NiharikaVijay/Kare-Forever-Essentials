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
        $sql = 'SELECT  o.ordid, o.timestamp,
        c.cxname,
        a.line1, a.line2, a.city, a.pincode, a.state,
        o.amount, o.status
        FROM orders o LEFT OUTER JOIN customer c ON c.cxid=o.cxid LEFT OUTER JOIN address a ON a.cxid=c.cxid;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $result = $prep->fetchAll();
        return $result;
    }
    public function getOrderDetails(String $ordid)
    {
        $sql = 'SELECT  c.cxname, c.cxemail, c.cxphone,
        o.timestamp, o.notes,
        a.line1, a.line2, a.city, a.pincode, a.state,
        o.amount, o.status
        FROM orders o LEFT OUTER JOIN customer c ON c.cxid=o.cxid LEFT OUTER JOIN address a ON a.cxid=c.cxid WHERE o.ordid= :ordid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['ordid' => $ordid]);
        $details = $prep->fetchAll();

        $sql = 'SELECT  c.cxname, c.cxemail, c.cxphone,
        o.timestamp, o.notes,
        a.line1, a.line2, a.city, a.pincode, a.state,
        o.amount, o.status
        FROM orders o LEFT OUTER JOIN customer c ON c.cxid=o.cxid LEFT OUTER JOIN address a ON a.cxid=c.cxid WHERE o.ordid= :ordid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['ordid' => $ordid]);
        $details = $prep->fetchAll();
    }
}
