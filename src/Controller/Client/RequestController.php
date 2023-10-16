<?php


namespace App\Controller\Client;

use App\Controller\AppController;
use Cake\Http\Response;
use App\Model\Table\CaseRequestsTable; // Make sure to adjust the namespace

class RequestController extends AppController
{
    protected $CaseRequests;

    public function initialize(): void
    {
        parent::initialize();
        $this->CaseRequests = new CaseRequestsTable();
    }

    public function index()
    {
        // Query all the information from the case_requests table
        $caseRequests = $this->CaseRequests->find()->toArray();
        
        // Create a response object with JSON data
        $response = $this->response->withType('application/json');
        $response = $response->withStringBody(json_encode($caseRequests));
        
        return $response;
    }
}







