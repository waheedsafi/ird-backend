<?php

namespace App\Enums\Types;

enum EmailInfoEnum: string
{
    case ORG_REGISTER_SUBJECT = "Account Registration";
    case ORG_REGISTER_APPROVE_BODY = "We are pleased to inform you that your registration form has been approved by the Directorate of International Relations, Coordination, and Aid Management, Ministry of Public Health (MOPH).

You may now proceed to register your projects through the official platform. Kindly ensure that all project details are submitted accurately and in accordance with the provided guidelines.

Should you require any assistance or further information, please do not hesitate to contact us.";
    case ORG_REGISTER_REJECTED_BODY = "We regret to inform you that your registration form submitted to the Directorate of International Relations, Coordination, and Aid Management, Ministry of Public Health (MOPH) has not been approved at this time.";
    case ORG_REGISTER_REJECTED_fOOTER_BODY = "You may review the feedback and, if applicable, submit a revised registration form for consideration. Should you require clarification or further assistance, please do not hesitate to contact us.";
}
