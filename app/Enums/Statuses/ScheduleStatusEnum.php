<?php

namespace App\Enums\Statuses;

enum ScheduleStatusEnum: int
{
    // General
    case Scheduled = 1;
    case Cancelled = 2;
    case Postponed = 3;
    case Completed = 4;
}
