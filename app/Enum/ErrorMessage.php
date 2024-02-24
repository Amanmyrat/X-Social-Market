<?php

namespace App\Enum;

enum ErrorMessage: string
{
    case USER_PRIVATE_ERROR = 'user_private_error';
    case ACCOUNT_BLOCKED_ERROR = 'account_blocked_error';
}
