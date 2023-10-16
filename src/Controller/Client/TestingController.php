<?php

// Assuming the controller file is named UserPermissionController.php
// Place this file in your CakePHP controllers directory

namespace App\Controller\Client;
// Assuming the controller file is named UserPermissionController.php
// Place this file in your CakePHP controllers directory


use App\Controller\AppController;
use Cake\Controller\Controller;
use Cake\Http\Exception\NotFoundException;
use App\Model\Table\IncomingDocumentsTable;

class TestingController extends AppController {

    public function index(){
        if ($this->request->is('get')) {
        $incomingDocument = new IncomingDocumentsTable();

        // Define the data for the new entry
        $newDocument = $incomingDocument->newEntity([
            'original_name' => 'example.txt',
            'file_name' => ''
        ]);

        // Save the new entity
        if ($incomingDocument->save($newDocument)) {
           
            $response = [
                'success' => 'true',
                'message' => 'Document entry saved.',
            ];
        } else {
            
            $response = [
                'success' => 'false',
                'message' => 'Error saving document entry.',
            ];
        }

        // Redirect to another page or render a view as needed
        $this->response->withType('application/json');
        $this->response->withStringBody(json_encode($response));

        return $this->response;
    }

    }
}
