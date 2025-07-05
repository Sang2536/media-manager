<?php

namespace App\Enums;

enum TypeEnum: string
{
    //  services
    case SYSTEM = 'system';
    case WEB = 'web';
    case SERVER = 'server';
    case SERVICE = 'service';
    case GATEWAY = 'gateway';

    //  system
    case CRUD = 'crud';
    case API = 'api';

    //  error
    case NOT_FOUND = 'not_found';
    case NOT_ALLOWED = 'not_allowed';
    case NOT_IMPLEMENTED = 'not_implemented';
    case EXCEPTION = 'exception';
    case UNKNOWN = 'unknown';
    case REQUEST = 'request';
    case UNAUTHORIZED = 'unauthorized';
    case FORBIDDEN = 'forbidden';
}
