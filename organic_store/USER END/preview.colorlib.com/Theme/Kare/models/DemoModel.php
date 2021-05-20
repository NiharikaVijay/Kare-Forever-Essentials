<?php

class DemoModel
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllOrders()
    {
        $sql = 'SELECT * FROM customer;';   #Replace this with your own SQL query
        $prep = $this->db->prepare($sql);   
        $prep->execute();                   #If there is anything dynamic in your query then you change this line. Refer the admin side models for directions 
        $result = $prep->fetchAll();
        return $result;
    }
}
