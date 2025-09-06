<?php

namespace App\Enums\Types;

enum ApprovalTypeEnum: int
{
    case approved = 1;
    case pending = 2;
    case rejected = 3;
}
