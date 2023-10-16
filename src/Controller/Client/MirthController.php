<?php
namespace App\Controller\Client;

use App\Controller\AppController;

class MirthController extends AppController
{
    public function index()
    {
        $filename = 'D:\revkeep\revkeep.txt'; // Replace with the actual path and filename of the file you want to read

        if (file_exists($filename)) {
            $fileContent = file_get_contents($filename);
            echo($fileContent);
            $this->viewBuilder()->setLayout('ajax');
            $this->set('file', $fileContent);
            
        } else {
            $this->Flash->error('File not found.');
           
        }
        $this->render('index');
        // echo h($fileContent);

    }
}

?>