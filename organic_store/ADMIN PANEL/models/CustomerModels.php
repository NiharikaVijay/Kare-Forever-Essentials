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
        IFNULL(o.cnt, 0) as ordercount, 
        IFNULL(c.cnt,0) as cartcount, 
        IFNULL(w.cnt, 0 ) as wishcount 
        from customer cx 
        left outer join ( select cxid, count(cxid) as cnt from wishlist group by cxid) w on w.cxid = cx.cxid 
        left outer join ( select cxid, count(cxid) as cnt from cart group by cxid) c on c.cxid = cx.cxid 
        left outer join (select cxid, count(cxid) as cnt from orders group by cxid) o on o.cxid = cx.cxid;';
        // $sql = 'SELECT * FROM customer;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $result = $prep->fetchAll();
        return $result;
    }
}
