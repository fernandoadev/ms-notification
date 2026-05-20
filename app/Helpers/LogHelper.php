<?php

namespace App\Helpers;

use App\Enums\LogLevelEnum;
use App\Models\CommunicationLog;

class LogHelper
{
    public function create(
        ?string $communicationId,
        LogLevelEnum $level,
        string $message,
        ?array $context = null
    ): CommunicationLog {
        return CommunicationLog::query()->create([
            'communication_id' => $communicationId,
            'level' => $level->value,
            'message' => $message,
            'context' => $context,
        ]);
    }
}
