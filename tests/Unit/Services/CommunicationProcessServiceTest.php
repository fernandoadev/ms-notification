<?php

use App\Clients\SmsClient;
use App\Enums\ChannelEnum;
use App\Enums\CommunicationStatusEnum;
use App\Models\Communication;
use App\Services\CommunicationProcessService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery\MockInterface;

it('should process an sms communication successfully', function () {
    $communication = Communication::factory()->create([
        'channel' => ChannelEnum::SMS->value,
    ]);

    $this->mock(SmsClient::class, function (MockInterface $mock) use ($communication) {
        $mock->shouldReceive('send')
            ->once()
            ->withArgs(fn (Communication $arg) => $arg->id === $communication->id);
    });

    app(CommunicationProcessService::class)->process($communication->id, 1);

    $communication->refresh();

    expect($communication->status)->toBe(CommunicationStatusEnum::COMPLETED)
        ->and($communication->attempts)->toBe(1)
        ->and($communication->processed_at)->not->toBeNull();

    $this->assertDatabaseHas('communication_logs', [
        'communication_id' => $communication->id,
        'message' => 'Communication processed successfully.',
    ]);
});

it('should throw ModelNotFoundException when communication id does not exist', function () {
    expect(fn () => app(CommunicationProcessService::class)->process('non-existent-uuid', 1))
        ->toThrow(ModelNotFoundException::class);
});

it('should mark communication as failed when the client throws an error', function () {
    $communication = Communication::factory()->create([
        'channel' => ChannelEnum::SMS->value,
    ]);

    $this->mock(SmsClient::class, function (MockInterface $mock) {
        $mock->shouldReceive('send')
            ->once()
            ->andThrow(new RuntimeException('SMS provider unavailable.'));
    });

    expect(fn () => app(CommunicationProcessService::class)->process($communication->id, 1))
        ->toThrow(RuntimeException::class, 'SMS provider unavailable.');

    $communication->refresh();

    expect($communication->status)->toBe(CommunicationStatusEnum::FAILED)
        ->and($communication->attempts)->toBe(1)
        ->and($communication->failed_at)->not->toBeNull();

    $this->assertDatabaseHas('communication_logs', [
        'communication_id' => $communication->id,
        'message' => 'Communication processing failed.',
    ]);
});
