<?php

namespace App\Infrastructure\Type;

enum Flash: string {
    case INFO = 'info';
    case SUCCESS = 'success';
    case NOTICE = 'notice';
    case ERROR = 'error';
}
