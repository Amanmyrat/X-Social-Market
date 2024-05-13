<?php

namespace App\Enum;

enum ErrorMessage: string
{
    case GENERAL_ERROR = 'general_error';
    case USER_PRIVATE_ERROR = 'user_private_error';
    case USER_BLOCKED_ERROR = 'user_blocked_error';
    case ACCOUNT_BLOCKED_ERROR = 'account_blocked_error';
    case ACCOUNT_DISABLED_ERROR = 'account_disabled_error';
    case OTP_DID_NOT_SENT_ERROR = 'otp_did_not_sent_error';
    case OTP_TIMEOUT_ERROR = 'otp_timeout_error';
    case OTP_DID_NOT_MATCH_ERROR = 'otp_did_not_match_error';
    case UNAUTHORIZED_ACCESS_ERROR = 'unauthorized_access_error';
}
