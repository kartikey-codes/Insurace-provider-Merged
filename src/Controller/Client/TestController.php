<?php
namespace App\Controller\Client;

use App\Controller\AppController;
use App\Policy\TestPolicy;
use Cake\Http\Exception\ForbiddenException;
use Cake\Datasource\FactoryLocator;

class TestController extends AppController
{
    public function index()
    {
        if ($this->request->is('get')) {
            
            $user = $this->Authentication->getIdentity();
            $datatype=gettype($user['client_admin']);
            debug($user['client_admin']);
            // debug($datatype);


            $this->viewBuilder()->setLayout('ajax');
            $this->render('index');
        }
        if ($this->request->is('post')) {
            $patientName = $this->request->getData('patient_name');
            
            // Perform any validation if needed
            
            if ($this->storePatientName($patientName)) {
                $this->Flash->success('Patient name stored successfully!');
            } else {
                $this->Flash->error('Failed to store the patient name.');
            }
            $this->viewBuilder()->setLayout('ajax');
            
            // return $this->redirect(['action' => 'index']);
            return $this->redirect(['controller' => 'Mirth', 'action' => 'index', 'prefix' => 'Client']);
        }
    }
    
    private function storePatientName($patientName)
    {
        $filePath = 'D:/revkeep/search.txt'; // Path to the file
    
        // Open the file in write mode (overwrite existing content)
        $file = fopen($filePath, 'w');
        
        if ($file) {
            // Write the patient name to the file
            if (fwrite($file, $patientName . PHP_EOL) !== false) {
                fclose($file);
                return true;
            } else {
                fclose($file);
                throw new InternalErrorException('Failed to write to the file.');
            }
        } else {
            throw new InternalErrorException('Failed to open the file for writing.');
        }
        
        return false;
    }
}
?>
