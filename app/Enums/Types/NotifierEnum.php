<?php

namespace App\Enums\Types;

enum NotifierEnum: int
{
    case confirm_adding_user = 1;
    case confirm_signed_registration_form = 2;
    case confirm_signed_project_form = 3;
    case signed_register_form_accepted = 4;
    case project_scheduled_for_presentation = 5;
    case project_recieved_comment = 6;
}
