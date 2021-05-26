<?php

class AdminModel
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function verify(String $email, String $pwd)
    {
        $sql = 'SELECT * FROM admin WHERE admemail= :email;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['email' => $email]);
        $admin = $prep->fetchAll();

        $verified = FALSE;
        if (sizeof($admin) == 1) {
            if (password_verify($pwd, $admin[0][4])) {
                $verified = TRUE;
            }
        }
        return [
            'verified' => $verified,
            'details' => $admin
        ];
    }
}
