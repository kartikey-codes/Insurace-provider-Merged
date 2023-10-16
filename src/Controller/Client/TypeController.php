<?php

namespace App\Controller\Client;

use App\Model\Table\InsuranceTypesTable;
use App\Controller\AppController;
use Cake\Network\Exception\MethodNotAllowedException;

class TypeController extends AppController
{
    public function index()
    {
        $this->autoRender = false; 
        $type = new InsuranceTypesTable();
        if ($this->request->is('post')) {
         $newType = $this->request->getData('newType');
         $new = $type->newEntity([

            'name' => $newType,
        ]);
        if ($type->save($new)) {
            $response = [

                'success' => 'true',

                'message' => 'Document entry saved.',

            ];

        }

    }
    
    }
}
