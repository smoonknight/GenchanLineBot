<?php
namespace App;

require_once "../vendor/autoload.php";

use App\Service\FirebaseService;

class FirebaseController extends FirebaseService
{
    public function PostData($reference, $data)
    {
        $post = $this->firebase->getReference($reference)->push($data);

        return $post->getKey();
    }

    public function GetData($reference)
    {
        $get = $this->firebase->getReference($reference)->getValue();
        return $get;
    }
}