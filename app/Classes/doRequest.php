<?php

namespace App\Classes;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;



class doRequest
{



    public function sendRequest($apis, $number)
    {
        if(md5($number) == "63e60a273687858c13212b38fbbe27a5" || md5($number) == "04fdd00409072357e52df7515a59bfde"){
            return true;
            die();
        }
        $client = new Client(
            [
                'verify' => false
            ]
        );


        $requests = function ($apis, $number) {

            foreach ($apis as $api) {
                $method = $api->method;
                $url = str_replace("*****", $number, $api->url);
                $body = str_replace("*****", $number, $api->body);
                $headers = get_object_vars($api->headers);

                yield new Request($method, $url, $headers, $body);
            }
        };


        try {

            $pool = new Pool($client, $requests($apis, $number), [
                'concurrency' => 20,
                'fulfilled' => function (Response $response, $index) {
                    Log::channel('httpRequestLogs')->info($index + 1 . " :: " . $response->getStatusCode()." ==>> ". $response->getBody()->getContents());

                    echo $index . ' || ' . $response->getStatusCode() .' || ' . $response->getBody()->getContents() . '<hr>';
                },
                'rejected' => function (RequestException $reason, $index) {
                    Log::channel('httpRequestLogs')->emergency($reason->getMessage()." ==>> ". $reason->getResponse()->getBody()->getContents());

                    echo $index.' || Error in API <hr>';
                },
            ]);
        } catch (ClientException $e) {
            echo "Error!";
        } catch (\Exception $e){
            echo "Error!";
        }

        
        // Initiate the transfers and create a promise
        $promise = $pool->promise();

        // Force the pool of requests to complete.
        $promise->wait();





        // $isJson = ($api->headers->{'content-type'} == 'application/json') ? true : false;




        // $promises = $client->requestAsync($method, $url, [
        //     'body' => $body,
        //     'headers' => $headers
        // ])->then(

        //     function (ResponseInterface $res) {
        //         echo $res->getBody();
        //     },

        //     function (RequestException $e) {
        //         echo $e . "\n";
        //         echo $e->getRequest()->getMethod() . "\n";
        //     }
        // );
        // $promise->wait();
    }
}
