<?php

namespace App\Enums;

enum CrawlStatus: string
{
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case ERROR = 'error';
}