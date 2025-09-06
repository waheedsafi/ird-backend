<?php

namespace App\Enums\Types;

enum CheckListTypeEnum: int
{
    case user = 1;
    case organization_registeration = 2;
    case project_registeration = 3;
    case organization_agreement_extend = 4;
    case project_extend = 5;
    case scheduling = 6;
}
