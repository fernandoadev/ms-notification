<?php

use App\DataTransferObjects\CommunicationDTO;
use App\Enums\ChannelEnum;
use App\Enums\CommunicationStatusEnum;
use App\Jobs\ProcessCommunicationJob;
use App\Models\Communication;
use App\Services\CommunicationService;
use Illuminate\Support\Facades\Queue;

it('should create and dispatch a communication successfully', function () {
    Queue::fake();

    $dto = new CommunicationDTO(
        recipient: 'user@example.com',
        channel: ChannelEnum::SMS->value,
        subject: 'Test subject',
        message: 'Test message',
        origin_system: 'test-system',
    );

    $result = app(CommunicationService::class)->createAndDispatch($dto);

    expect($result)
        ->toBeInstanceOf(Communication::class)
        ->and($result->recipient)->toBe('user@example.com')
        ->and($result->status)->toBe(CommunicationStatusEnum::CREATED);

    $this->assertDatabaseHas('communications', [
        'id' => $result->id,
        'recipient' => 'user@example.com',
        'channel' => ChannelEnum::SMS->value,
        'origin_system' => 'test-system',
    ]);

    $this->assertDatabaseHas('communication_logs', [
        'communication_id' => $result->id,
        'message' => 'Communication request created.',
    ]);

    Queue::assertPushed(
        ProcessCommunicationJob::class,
        fn (ProcessCommunicationJob $job) => $job->communicationId === $result->id
    );
});
