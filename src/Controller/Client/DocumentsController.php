<?php

namespace App\Controller\Client;

use App\Controller\AppController;
use Cake\Http\Response;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\Client;

class DocumentsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function create()
    {
        if ($this->request->is('post')) {
            // Process the data from the POST request
            $data = $this->request->getData();

            // Convert the data to JSON format
            $jsonData = json_encode($data, JSON_PRETTY_PRINT);

            // Specify the path to the file where you want to store the data
            $filePath = WWW_ROOT . 'data.json'; // Change the filename and path as needed

            // Write the JSON data to the file
            file_put_contents($filePath, $jsonData);

            // Make an HTTP request to the specified URL
            $this->makeHttpRequest('http://127.0.0.1:5000/readfile');

            // Return a response, possibly indicating success or failure
            return $this->response->withStatus(201)->withType('application/json');
        } else {
            throw new MethodNotAllowedException();
        }
    }

    // Function to make an HTTP request
    private function makeHttpRequest($url)
    {
        $http = new Client();
        $response = $http->get($url);

        // You can process the response if needed
        $responseBody = $response->getStringBody();

        // Here you could log or further process the response
    }
}
