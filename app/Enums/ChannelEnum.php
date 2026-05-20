<?php

namespace App\Enums;

enum ChannelEnum: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
    case PUSH_NOTIFICATION = 'push';
}