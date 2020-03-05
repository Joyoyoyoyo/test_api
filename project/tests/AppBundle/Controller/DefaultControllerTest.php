<?php
namespace AppBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;

class DefaultControllerTest extends TestCase
{
    public function testPost()
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:90',
                  ]);
        $data = json_encode([
    		"contact"=> [
		        "id"=> 2
		    ],
		    "product"=> [
		        "id"=> 1
		    ],
		    "begin_date"=> "2020-03-04T19:14:50+00:00",
		    "end_date"=> "2020-03-04T19:14:50+00:00"
		]);
        
        $response = $client->post('/subscription', [
            'body' => $data
        ]);
        
        $this->assertEquals(201, $response->getStatusCode());

    }
}