<?php

use App\Enums\ChannelEnum;
use App\Helpers\LogHelper;
use App\Models\Communication;
use App\Services\CommunicationService;
use Exception;
use Mockery\MockInterface;

$validPayload = [
    'recipient' => 'user@example.com',
    'channel' => ChannelEnum::SMS->value,
    'subject' => 'Test subject',
    'message' => 'Test message',
    'origin_system' => 'test-system',
];

it('should return 201 with communication data when request is valid', function () use ($validPayload) {
    $communication = Communication::factory()->make();

    $this->mock(CommunicationService::class, function (MockInterface $mock) use ($communication) {
        $mock->shouldReceive('createAndDispatch')
            ->once()
            ->andReturn($communication);
    });

    $this->postJson('/api/communications', $validPayload)
        ->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'communication',
        ])
        ->assertJsonFragment([
            'message' => 'Communication request received and is being processed.',
        ]);
});

it('should return 500 when the service throws an unexpected exception', function () use ($validPayload) {
    $this->mock(CommunicationService::class, function (MockInterface $mock) {
        $mock->shouldReceive('createAndDispatch')
            ->once()
            ->andThrow(new Exception('Unexpected database error.'));
    });

    $this->mock(LogHelper::class, function (MockInterface $mock) {
        $mock->shouldReceive('create')->once();
    });

    $this->postJson('/api/communications', $validPayload)
        ->assertStatus(500)
        ->assertJson([
            'error' => 'Failed to process communication request.',
        ]);
});
