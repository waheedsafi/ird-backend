<?php

namespace App\Enums\Types;

enum OrganizationTypeEnum: int
{
    case International = 1;
    case Intergovernmental = 2;
    case Domestic = 3;
}
