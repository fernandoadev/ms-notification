<?php

namespace App\DataTransferObjects;

use App\Enums\CommunicationStatusEnum;

class CommunicationDTO
{
    public function __construct(
        public readonly string $recipient,
        public readonly string $channel,
        public readonly ?string $subject,
        public readonly string $message,
        public readonly string $origin_system,
        public readonly string $status = CommunicationStatusEnum::CREATED->value,
        public readonly int $attempts = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            recipient: $data['recipient'],
            channel: $data['channel'],
            subject: $data['subject'] ?? null,
            message: $data['message'],
            origin_system: $data['origin_system'],
            status: $data['status'] ?? CommunicationStatusEnum::CREATED->value,
            attempts: $data['attempts'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'recipient' => $this->recipient,
            'channel' => $this->channel,
            'subject' => $this->subject,
            'message' => $this->message,
            'origin_system' => $this->origin_system,
            'status' => $this->status,
            'attempts' => $this->attempts,
        ];
    }
}
