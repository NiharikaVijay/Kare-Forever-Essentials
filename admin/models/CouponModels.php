<?php

class CouponModel
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllCoupons()
    {
        $sql = 'SELECT * FROM coupon;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $coupons = $prep->fetchAll();

        return $coupons;
    }

    public function editCoupon(
        $cpid,
        $cpdesc,
        $minord,
        $maxdisc,
        $discount,
        $maxuse
    ) {
        $sql = 'UPDATE coupon SET
        cpid= :cpid, 
        cpdesc= :cpdesc,
        minord= :minord,
        maxdisc= :maxdisc,
        discount= :discount,
        maxuse= :maxuse WHERE cpid= :cpid2;';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'cpid' => $cpid,
            'cpdesc' => $cpdesc,
            'minord' => $minord,
            'maxdisc' => $maxdisc,
            'discount' => $discount,
            'maxuse' => $maxuse,
            'cpid2' => $cpid
        ]);
    }

    public function deleteCoupon($cpid)
    {
        $sql = 'DELETE FROM coupon WHERE cpid= :cpid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['cpid' => $cpid]);
    }

    public function addCoupon(
        $cpid,
        $cpdesc,
        $minord,
        $maxdisc,
        $discount,
        $maxuse
    ) {
        $sql = 'INSERT INTO coupon VALUES(
        :cpid, 
        :cpdesc,
        :minord,
        :maxdisc,
        :discount,
        :maxuse)';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'cpid' => $cpid,
            'cpdesc' => $cpdesc,
            'minord' => $minord,
            'maxdisc' => $maxdisc,
            'discount' => $discount,
            'maxuse' => $maxuse
        ]);
    }
}
