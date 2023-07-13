<?php
namespace App;

require_once "../vendor/autoload.php";

use App\Service\FirebaseService;

class FirebaseController extends FirebaseService
{
    public function PostData()
    {
        $post = $this->firebase->getReference('debug')->push([
            'title' => "ini ujicoba",
            'body' => "menggunakan firebase"
        ]);

        return $post->getKey();
    }
}