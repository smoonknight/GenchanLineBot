<?php
namespace App\Service;

require 'vendor/autoload.php'; // Menyertakan autoloader Composer

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseService
{
    public $firebase;
	public function __construct()
    {
        // Path ke file kunci layanan Firebase
        $serviceAccount = json_decode(file_get_contents('../storage/token/firebase.token.json'));
    
        // Konfigurasi Firebase
        $this->firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://webchan-7c789-default-rtdb.asia-southeast1.firebasedatabase.app/');
    }
}