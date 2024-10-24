<?php

namespace App\Enums;

enum OrderTypeEnum: string
{
    case PINDING = 'pinding';
    case PROCESSING = 'processing';
    CASE COMPLETED = 'completed';
    CASE DECLINED = 'declined';
}