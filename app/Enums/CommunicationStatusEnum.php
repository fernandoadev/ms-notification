<?php

namespace App\Enums;

enum CommunicationStatusEnum: string
{
    case CREATED = 'created';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}