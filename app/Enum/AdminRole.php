<?php

namespace App\Enum;

enum AdminRole: string
{
    case SUPER_ADMIN = 'super-admin';
    case Admin = 'admin';
}
