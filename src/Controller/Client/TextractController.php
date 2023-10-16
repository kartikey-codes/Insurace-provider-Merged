<?php

namespace App\Controller\Client;

use App\Controller\AppController;
use Cake\Http\Response;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Filesystem\Filesystem;
use Cake\Filesystem\File;

class TextractController extends AppController
{
    public function index()
    {
        // Set the path to the JSON file
        $jsonFilePath = WWW_ROOT . 'json\output_data.json';
        
        // Read the JSON file
        $jsonData = file_get_contents($jsonFilePath);

        
        // Decode the JSON data into an associative array
        $extractedData = json_decode($jsonData, true);
            
        // Create a JSON response with the extracted data
        $this->response = $this->response->withType('application/json')
                                         ->withStringBody(json_encode([$extractedData]));
        
        return $this->response;
            
    }
}
