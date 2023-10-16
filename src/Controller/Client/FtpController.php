<?php

namespace App\Controller\Client;

use App\Model\Table\IncomingDocumentsTable;
use App\Controller\AppController;

use Cake\Http\Exception\MethodNotAllowedException;


class FtpController extends AppController
{
    public function index()
    {
        if ($this->request->is('post')) {

            // Process the data from the POST request

            $data = $this->request->getData();
            $ftp_server = $data["host"];
            $ftp_username = $data["username"];
            $ftp_password = $data["password"];
            $download_dir = "/htdocs/";
            $file_names = ["sample.txt","EDI835combined.pdf", "Sam_Smith.pdf", "Sandy_Doe.pdf"]; // Add more file names as needed

            $conn_id = ftp_connect($ftp_server);

            if ($conn_id) {
                $login_result = ftp_login($conn_id, $ftp_username, $ftp_password);

                if ($login_result) {
                    ftp_pasv($conn_id, true); // Enable passive mode
                    $incomingDocument = new IncomingDocumentsTable();
                    foreach ($file_names as $file_name) {
                        $time_stamp = time(); // Generate a timestamp
                        $timestamped_file_name = "835_" . $time_stamp . "_" . $file_name;
                        $remote_file_path = $download_dir . $file_name;
                        
                        if ($file_name === "sample.txt") {
                            $local_file_path = "../storage/incoming-documents/" . $timestamped_file_name;
                        } else {
                            $local_file_path = "../storage/incoming-documents/" . $timestamped_file_name;
                        }

                        $download_result = ftp_get($conn_id, $local_file_path, $remote_file_path, FTP_BINARY);

                        if ($download_result) {
                            echo "File '$file_name' downloaded successfully.<br>";
                        } else {
                            echo "Download of file '$file_name' failed.<br>";
                        }

                        $newDocument = $incomingDocument->newEntity([
                            'original_name' => $timestamped_file_name,
                            'file_name' => $timestamped_file_name
                        ]);
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
                    }
                } else {
                    echo "FTP login failed.";
                }

                ftp_close($conn_id);
            } else {
                echo "Could not connect to FTP server.";
            }
            return $this->response->withStatus(201)->withType('application/json');
        } else {
           echo("Some Problem Occured.");
        }
    }
}