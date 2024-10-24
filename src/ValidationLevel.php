<?php

namespace SmartPanel\Mjml;

enum ValidationLevel: string
{
    case Strict = 'strict';
    case Soft = 'soft';
    case Skip = 'skip';
}
