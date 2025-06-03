<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Enums;

enum BodyType: string
{
    case NONE = 'none';
    case RAW_JSON = 'rawJson';
    case FORM = 'form';
    case FORM_URLENCODED = 'formUrlEncoded';
}
