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

    public function getCart(String $cxid, String $coupon = '')
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

        $discount = null;
        // Change this 
        if ($coupon) {
            $discount = 100;
        }

        $delivery = 150 * ceil($volume / 500);

        $data = [
            'items' => $cart,
            'subtotal' => $subtotal,
            'delivery' => $delivery
        ];

        if ($discount) {
            $data += array(
                'discount' => $discount
            );
        }
        return $data;
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
    public function removeFromWishlist($cxid, $pdid)
    {
        $sql = 'DELETE FROM wishlist WHERE c.cxid= :cxid AND pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'cxid' => $cxid,
            'pdid' => $pdid
        ]);
    }

    public function cartUpdate($cxid, $data)
    {
        foreach ($data as $key => $value) {
            if ($key != 'submit_type' and $key != 'form-total' and $key != 'coupon') {
                $sql = 'UPDATE cart SET pdqty= :pdqty WHERE cxid= :cxid AND pdid= :pdid;';
                $prep = $this->db->prepare($sql);
                $prep->execute([
                    'pdqty' => $value,
                    'cxid' => $cxid,
                    'pdid' => $key
                ]);
            }
        }
    }

    public function moveToCart($cxid, $data)
    {
        $sql = 'INSERT into cart values(:cxid, :pdid, :pdvolume, :pdqty);';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'cxid' => $cxid,
            'pdid' => $data['pdid'],
            'pdvolume' => $data['volume'],
            'pdqty' => $data['qty']
        ]);

        $sql = 'DELETE FROM wishlist WHERE c.cxid= :cxid AND pdid= :pdid;';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'cxid' => $cxid,
            'pdid' => $data['pdid']
        ]);
    }

    public function setOTP($email, $tel, $cxid)
    {
        $sql = 'SELECT cxid, cxphone, cxemail FROM customer WHERE
        cxemail= :email OR
        cxphone= :tel;';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'email' => $email,
            'tel' => $tel
        ]);
        $cxdeets = $prep->fetchAll();
        //If customer is already present, reset cxid in session
        if (sizeof($cxdeets) > 0) {
            $_SESSION['cxid'] = $cxdeets[0][0];
            $cxid = $_SESSION['cxid'];
            $email = $cxdeets[0][2];
            $tel = $cxdeets[0][1];
        }

        //generate OTP
        $otp = rand(1000, 9999);

        //Insert into OTP Table
        $sql = 'REPLACE INTO otp VALUES(:cxid, :otp, NOW());';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'cxid' => $cxid,
            'otp' => $otp
        ]);

        //Deleting from OTP table where time has expired mroe than 5 mins
        $sql = 'DELETE FROM otp WHERE NOW()-timestamp>5*60;';
        $prep = $this->db->prepare($sql);
        $prep->execute();

        $recp = null;
        //Send mail or tel
        if ($email) {
            require dirname(dirname(dirname(__FILE__))) . '/models/user/HelperModels.php';

            $myfile = fopen(dirname(dirname(dirname(__FILE__))) . "/templates/mail/otp.html", "r") or die("Unable to open file!");
            $content = str_replace('otpgoeshere', $otp, fread($myfile, filesize(dirname(dirname(dirname(__FILE__))) . "/templates/mail/otp.html")));
            fclose($myfile);

            $subject = 'Kare Forever Essentials | OTP for logging in';

            $helperObject = new HelperModel();
            $helperObject->sendMail($content, $subject, $email);

            $recp = $email;
        }
        // elseif ($tel) {
        //     //Enter code to send OTP through sms
        // }
        return $recp;
    }

    public function verifyOTP($cxid, $otp)
    {
        $sql = 'SELECT c.cxid, IFNULL(c.cxname,"customer") FROM otp o LEFT OUTER JOIN  customer c on c.cxid=o.cxid
        WHERE o.cxid= :cxid AND o.otp= :otp AND NOW()-o.timestamp<=5*60;';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'cxid' => $cxid,
            'otp' => $otp
        ]);
        $customer = $prep->fetchAll();

        if (sizeof($customer) > 0) {
            return [
                'isValid' => true,
                'details' => $customer[0]
            ];
        } else {
            return [
                'isValid' => false
            ];
        }
    }
}
