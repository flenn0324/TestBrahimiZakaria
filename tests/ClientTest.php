<?php
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {
        require_once __DIR__ . '/../Client.php'; 
        $this->client = new Client(null); 
    }

    public function testCalculateScore()
    {
        $clientData = [
            'bounce_rate' => 10,
            'open_rate' => 50,
            'unsubscription_rate' => 5,
            'complaint_rate' => 2
        ];

        $expectedScore = 67.845; 

        $calculatedScore = $this->client->calculateScore($clientData);

        $this->assertEquals($expectedScore, $calculatedScore); 
    }
}