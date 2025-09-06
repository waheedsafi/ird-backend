<?php

namespace App\Enums\Checklist;

enum ChecklistEnum: int
{
    case user = 1;
    case director_nid = 2;
    case director_work_permit = 3;
    case organization_representor_letter = 4;
    case ministry_of_economy_work_permit = 5;
    case articles_of_association = 6;
    case organization_register_form_en = 8;
    case organization_register_form_ps = 9;
    case organization_register_form_fa = 10;

        // project checklist 
    case moe_project_introduction_letter = 11;              // Project introduction letter from Ministry of Economy
    case project_articles_of_association = 12;
    case project_presentation = 13;              // Project Presentation
    case organization_and_donor_contract = 14;         // organization & Donor Contract Letter
    case mou_en = 15;                        // Memorandum of Understanding (English)
    case mou_fa = 16;                          // Memorandum of Understanding (Farsi)
    case mou_ps = 17;
    case project_ministry_of_economy_work_permit = 18;


        // Schedule
    case schedule_deputy_document = 19;
}
