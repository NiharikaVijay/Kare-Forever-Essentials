<?php

class CarouselModel
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

    public function getAllImages()
    {
        $sql = 'SELECT  * FROM carousel;';
        $prep = $this->db->prepare($sql);
        $prep->execute();
        $images = $prep->fetchAll();

        return $images;
    }

    public function deleteImage($carid)
    {
        $sql = 'DELETE FROM carousel WHERE carid= :carid;';
        $prep = $this->db->prepare($sql);
        $prep->execute(['carid' => $carid]);
    }
    public function addImage($rank, $tagline, $link, $image)
    {
        $carid = $this->generateRandomString(5);


        $target_dir = '../media/carousel/';
        $target_file = $target_dir . $carid . '-' . basename($image['name']);

        if (!move_uploaded_file($image["tmp_name"], $target_file)) {
            die();
        }

        $sql = 'INSERT INTO carousel VALUES(:carid, :imgpath, :tagline, :link, :rank);';
        $prep = $this->db->prepare($sql);
        $prep->execute([
            'carid' => $carid,
            'imgpath' => '/media/carousel/' . $carid . '-' . basename($image['name']),
            'tagline' => $tagline,
            'link' => $link,
            'rank' => $rank
        ]);
    }
}
