<?php

namespace ISend\SMS\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use ISend\SMS\ISend;
use ISend\SMS\Exceptions\ISendException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Mockery;

class ISendTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_send_a_simple_message()
    {
        // Create mock handler and client
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'SMS sent successfully',
                'data' => [
                    'uid' => 'sms-123456',
                ],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        
        // Create isend instance with the mocked client
        $isend = new ISend('test-token', 'https://isend.com.ly', '/api/v3', 'TestSender');
        $reflectionClass = new \ReflectionClass($isend);
        $property = $reflectionClass->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($isend, $client);
        
        // Send the message
        $result = $isend->to('218914143421')->message('Test message')->send();
        
        // Assert the message was sent and the ID was set
        $this->assertInstanceOf(ISend::class, $result);
        $this->assertEquals('sms-123456', $result->getId());
    }

    /** @test */
    public function it_throws_exception_on_api_error()
    {
        // Create mock handler and client
        $mock = new MockHandler([
            new Response(400, [], json_encode([
                'status' => 'error',
                'message' => 'Invalid recipient',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        
        // Create isend instance with the mocked client
        $isend = new ISend('test-token', 'https://isend.com.ly', '/api/v3', 'TestSender');
        $reflectionClass = new \ReflectionClass($isend);
        $property = $reflectionClass->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($isend, $client);
        
        // Expect an exception
        $this->expectException(ISendException::class);
        $this->expectExceptionMessage('Invalid recipient');
        
        // Send the message with invalid recipient
        $isend->to('invalid')->message('Test message')->send();
    }

    /** @test */
    public function it_can_check_message_status()
    {
        // Create mock handler and client
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'SMS status retrieved',
                'data' => [
                    'uid' => 'sms-123456',
                    'status' => 'delivered',
                    'recipient' => '218914143421',
                    'sent_at' => '2023-05-01 12:34:56',
                ],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        
        // Create isend instance with the mocked client
        $isend = new ISend('test-token', 'https://isend.com.ly', '/api/v3', 'TestSender');
        $reflectionClass = new \ReflectionClass($isend);
        $property = $reflectionClass->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($isend, $client);
        
        // Get the message status
        $status = $isend->getStatus('sms-123456');
        
        // Assert the status was retrieved
        $this->assertEquals('success', $status['status']);
        $this->assertEquals('delivered', $status['data']['status']);
    }

    /** @test */
    public function it_can_list_messages()
    {
        // Create mock handler and client
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'Messages retrieved',
                'data' => [
                    [
                        'uid' => 'sms-123456',
                        'status' => 'delivered',
                        'recipient' => '218914143421',
                        'sent_at' => '2023-05-01 12:34:56',
                    ],
                    [
                        'uid' => 'sms-123457',
                        'status' => 'pending',
                        'recipient' => '218929000836',
                        'sent_at' => '2023-05-01 12:35:00',
                    ],
                ],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        
        // Create isend instance with the mocked client
        $isend = new ISend('test-token', 'https://isend.com.ly', '/api/v3', 'TestSender');
        $reflectionClass = new \ReflectionClass($isend);
        $property = $reflectionClass->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($isend, $client);
        
        // List messages
        $messages = $isend->listMessages();
        
        // Assert the messages were retrieved
        $this->assertEquals('success', $messages['status']);
        $this->assertCount(2, $messages['data']);
        $this->assertEquals('sms-123456', $messages['data'][0]['uid']);
    }

    /** @test */
    public function it_can_send_campaign()
    {
        // Create mock handler and client
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'Campaign scheduled successfully',
                'data' => [
                    'uid' => 'campaign-123456',
                ],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        
        // Create isend instance with the mocked client
        $isend = new ISend('test-token', 'https://isend.com.ly', '/api/v3', 'TestSender');
        $reflectionClass = new \ReflectionClass($isend);
        $property = $reflectionClass->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($isend, $client);
        
        // Send campaign
        $campaign = $isend->sendCampaign('list-123', 'Campaign message');
        
        // Assert the campaign was sent
        $this->assertEquals('success', $campaign['status']);
        $this->assertEquals('campaign-123456', $campaign['data']['uid']);
    }

    /** @test */
    public function it_can_check_balance()
    {
        // Create mock handler and client
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'Balance retrieved',
                'data' => [
                    'balance' => 500,
                    'currency' => 'USD',
                ],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        
        // Create isend instance with the mocked client
        $isend = new ISend('test-token', 'https://isend.com.ly', '/api/v3', 'TestSender');
        $reflectionClass = new \ReflectionClass($isend);
        $property = $reflectionClass->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($isend, $client);
        
        // Check balance
        $balance = $isend->checkBalance();
        
        // Assert the balance was retrieved
        $this->assertEquals('success', $balance['status']);
        $this->assertEquals(500, $balance['data']['balance']);
    }
}