<?php

namespace App\Controller\Client;

use App\Controller\AppController;
use Cake\Http\Response;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\Client;

class DocumentPermissionController extends AppController{

    public function index()
    {
    $this->autoRender = false; // Disable view rendering

    
    $user = $this->Authentication->getIdentity();
    $permission = $user['id'];
    $client_admin = $user['client_admin'];

        
        $this->response = $this->response->withType('application/json')
                                         ->withStringBody(json_encode(['success' => $permission , 'client_admin' => $client_admin]));
        
        return $this->response;
    }

}