<?php

namespace App\Services;

use App\Clients\EmailClient;
use App\Clients\PushClient;
use App\Clients\SmsClient;
use App\Enums\ChannelEnum;
use App\Enums\CommunicationStatusEnum;
use App\Enums\LogLevelEnum;
use App\Helpers\LogHelper;
use App\Models\Communication;
use App\Models\CommunicationLog;
use InvalidArgumentException;
use Throwable;

class CommunicationProcessService
{
    public function __construct(
        private readonly Communication $model,
        private readonly CommunicationLog $logModel,
        private readonly LogHelper $logHelper,
        private readonly SmsClient $smsClient,
        private readonly EmailClient $emailClient,
        private readonly PushClient $pushClient,
    ) {}

    public function process(string $communicationId, int $attempts): void
    {
        $communication = $this->model->findOrFail($communicationId);

        try {
            $communication->update([
                'status' => CommunicationStatusEnum::PROCESSING->value,
                'attempts' => $attempts,
            ]);

            $this->logHelper->create(
                communicationId: $communication->id,
                level: LogLevelEnum::INFO,
                message: 'Communication processing started.',
                context: ['attempt' => $attempts],
            );

            $this->deliver($communication);

            $communication->update([
                'status' => CommunicationStatusEnum::COMPLETED->value,
                'attempts' => $attempts,
                'processed_at' => now(),
            ]);

            $this->logHelper->create(
                communicationId: $communication->id,
                level: LogLevelEnum::INFO,
                message: 'Communication processed successfully.',
                context: ['processed_at' => now()->toIso8601String()],
            );
        } catch (Throwable $e) {
            $communication->update([
                'status' => CommunicationStatusEnum::FAILED->value,
                'attempts' => $attempts,
                'failed_at' => now(),
            ]);

            $this->logHelper->create(
                communicationId: $communication->id,
                level: LogLevelEnum::ERROR,
                message: 'Communication processing failed.',
                context: [
                    'attempt' => $attempts,
                    'error' => $e->getMessage(),
                ],
            );

            throw $e;
        }

    }

    private function deliver(Communication $communication): void
    {
        match ($communication->channel) {
            ChannelEnum::SMS => $this->smsClient->send($communication),
            ChannelEnum::EMAIL => $this->emailClient->send($communication),
            ChannelEnum::PUSH_NOTIFICATION => $this->pushClient->send($communication),
            default => throw new InvalidArgumentException("Unsupported communication channel: {$communication->channel}"),
        };
    }
}
