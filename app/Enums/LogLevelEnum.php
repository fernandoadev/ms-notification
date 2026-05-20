<?php

namespace App\Enums;

enum LogLevelEnum: string
{
    case INFO = 'info';
    case ERROR = 'error';
    case WARNING = 'warning';
}