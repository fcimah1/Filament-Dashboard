<?php

namespace App\Enums;

enum OrderTypeEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    CASE COMPLETED = 'completed';
    CASE DECLINED = 'declined';
}