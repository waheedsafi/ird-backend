<?php

namespace App\Enums\Permissions;

enum SubPermissionEnum: int
{
    // User
    case user_information = 1;
    case user_password = 2;
    case user_account_status = 3;
    public const USERS = [
        1 => ['label' => "account_information", 'is_category' => false],
        2 => ['label' => "account_password", 'is_category' => false],
        3 => ['label' => "account_status", 'is_category' => false],
    ];

        // Configurations
    case configurations_job = 31;
    case configurations_checklist = 32;
    case configurations_division = 33;
    case configurations_role = 34;
    case configurations_application = 35;
    public const CONFIGURATIONS = [
        31 => ['label' => "job", 'is_category' => true],
        32 => ['label' => "checklist", 'is_category' => true],
        33 => ['label' => "division", 'is_category' => true],
        34 => ['label' => "role", 'is_category' => true],
        35 => ['label' => "application", 'is_category' => true],
    ];

        // Approval
    case user_approval = 51;
    case organization_approval = 52;
    case donor_approval = 53;
    public const APPROVALS = [
        51 => ['label' => "user", 'is_category' => false],
        52 => ['label' => "organization", 'is_category' => false],
        53 => ['label' => "donor", 'is_category' => false],
    ];

        // Activity
    case activity_user_activity = 71;
    public const ACTIVITY = [
        71 => ['label' => "user_activity", 'is_category' => true],
    ];

        // About
    case about_director = 91;
    case about_manager = 92;
    case about_office = 93;
    case about_technical = 94;
    case about_slideshow = 95;
    case about_faqs = 96;
    case about_faqs_type = 97;
    case about_news = 98;
    case about_news_type = 99;
    public const ABOUT = [
        91 => ['label' => "director", 'is_category' => true],
        92 => ['label' => "manager", 'is_category' => true],
        93 => ['label' => "office", 'is_category' => true],
        94 => ['label' => "technical_sup", 'is_category' => true],
        95 => ['label' => "slideshow", 'is_category' => true],
        96 => ['label' => "faqs", 'is_category' => true],
        97 => ['label' => "faqs_type", 'is_category' => true],
        98 => ['label' => "news", 'is_category' => true],
        99 => ['label' => "news_type", 'is_category' => true],
    ];
        // App
    case organizations_information = 62;
    case organizations_director_information = 63;
    case organizations_agreement = 64;
    case organizations_agreement_status = 65;
    case organizations_more_information = 66;
    case organizations_status = 67;
    case organizations_representative = 68;
    case organizations_account_password = 69;
    public const ORGANIZATIONS = [
        62 => ['label' => "account_information", 'is_category' => false],
        63 => ['label' => "director_information", 'is_category' => false],
        64 => ['label' => "agreement_checklist", 'is_category' => false],
        65 => ['label' => "agreement_status", 'is_category' => false],
        66 => ['label' => "more_information", 'is_category' => false],
        67 => ['label' => "status", 'is_category' => false],
        68 => ['label' => "representative", 'is_category' => false],
        69 => ['label' => "account_password", 'is_category' => false],
    ];
    case project_detail = 121;
    case project_center_budget = 122;
    case project_organization_structure = 123;
    case project_checklist = 124;
    public const PROJECT = [
        121 => ['label' => "detail", 'is_category' => false],
        122 => ['label' => "center_budget", 'is_category' => false],
        123 => ['label' => "organ_structure", 'is_category' => false],
        124 => ['label' => "checklist", 'is_category' => false],
    ];

    case donor_information = 141;
    case donor_status = 142;
    public const DONOR = [
        141 => ['label' => "donor_information", 'is_category' => false],
        142 => ['label' => "donor_status", 'is_category' => false],
    ];

    public static function getMetaById(int $id): ?array
    {
        $all = self::USERS
            + self::CONFIGURATIONS
            + self::APPROVALS
            + self::ACTIVITY
            + self::ABOUT;
        +self::ORGANIZATIONS;
        +self::PROJECT;
        +self::DONOR;

        return $all[$id] ?? null;
    }
}
