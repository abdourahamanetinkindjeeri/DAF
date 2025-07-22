<?php

namespace App\Validator;

enum ErrorMessage: string
{
    case REQUIRED = 'Le champ :field est requis.';
    case EMAIL = 'Le champ :field doit être un email valide.';
    case MIN = 'Le champ :field doit contenir au moins :param caractères.';
    case SAME = 'Le champ :field doit être identique au champ :param.';
}
