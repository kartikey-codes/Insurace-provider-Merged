<?php

// Assuming the controller file is named UserPermissionController.php
// Place this file in your CakePHP controllers directory

namespace App\Controller\Client;
// Assuming the controller file is named UserPermissionController.php
// Place this file in your CakePHP controllers directory


use App\Controller\AppController;
use Cake\Controller\Controller;
use Cake\Http\Exception\NotFoundException;

class UserPermissionController extends AppController
{
    public function index()
    {
    $this->autoRender = false; // Disable view rendering

    
    $user = $this->Authentication->getIdentity();
    $permission = $user['client_admin'];

        
        $this->response = $this->response->withType('application/json')
                                         ->withStringBody(json_encode(['success' => $permission]));
        
        return $this->response;
    }
}

?>