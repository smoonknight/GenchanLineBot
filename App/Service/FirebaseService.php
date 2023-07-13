<?php
namespace App\Service;

require_once "../vendor/autoload.php";

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseService
{
    public $firebase;
	public function __construct()
    {
        $this->firebase = (new Factory)
            ->withServiceAccount("../storage/token/firebase.token.json")
            ->withDatabaseUri('https://webchan-7c789-default-rtdb.asia-southeast1.firebasedatabase.app/')
            ->createDatabase();
    }
}