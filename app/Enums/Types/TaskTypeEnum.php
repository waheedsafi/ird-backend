<?php

namespace App\Enums\Types;

enum TaskTypeEnum: int
{
    case organization_registeration = 1;
    case project_registeration = 2;
    case organization_agreement_extend = 3;
    case project_extend = 4;
    case scheduling = 5;
}
