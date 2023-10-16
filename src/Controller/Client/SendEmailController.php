<?php

namespace App\Controller\Client;

use App\Controller\AppController;
use Cake\Http\Response;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\Client;
use Cake\Mailer\Mailer;
use Cake\Http\Exception\InternalErrorException; 
use Cake\ORM\TableRegistry;


class SendEmailController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function index()
    {   
        if ($this->request->is('post')) 
        {
            $data = $this->request->getData(); // Get POST data
            $id = $data['id'];
            $jsonData = json_encode($id);
            $filePath = WWW_ROOT . 'data.json'; // You can adjust the file path as needed
            // Write the JSON data to the file
            // file_put_contents($filePath, $jsonData);
            try{
            $users = TableRegistry::getTableLocator()->get('Users');
            $user = $users->get($id);
            $email = $user['email'];
            $first_name = $user['first_name'];
            $mailer = new Mailer();
            $mailer->setProfile('default');
            $mailer
                    ->setEmailFormat('html')
                    ->setTo($email)
                    ->setSubject('Document Assigned') 
                    ->viewBuilder()
                        ->setTemplate('document_assigned')
                        ->setVars(['fullname' => $first_name]);
            $mailer->deliver();
            }
            catch (\Exception $e) {

            }
            return $this->response->withStatus(201)->withType('application/json');
        }
         else {
            throw new MethodNotAllowedException();
        }
    }


}



// namespace App\Controller\Client;

// use App\Controller\AppController;
// use Cake\Http\Exception\MethodNotAllowedException;
// use Cake\ORM\TableRegistry;

// class SendEmailController extends AppController
// {
//     public function initialize(): void
//     {
//         parent::initialize();
//         $this->loadComponent('RequestHandler');
//     }

//     public function index()
//     {
//         if ($this->request->is('post')) {
//             $data = $this->request->getData(); // Get POST data
            
//             // Assuming the POST data contains an 'id' field
//             $id = $data;
            
//             // Fetch user data based on the retrieved id
//             $usersTable = TableRegistry::getTableLocator()->get('Users');
//             $user = $usersTable->get($id);

//             // Now you have the $user data, and you can perform actions with it
            
//             return $this->response->withStatus(201)->withType('application/json');
//         } else {
//             throw new MethodNotAllowedException();
//         }
//     }
// }
