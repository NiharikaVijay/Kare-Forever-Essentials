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
        $sql = 'SELECT * FROM customer;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $result = $prep->fetchAll();
        return $result;
    }
}
