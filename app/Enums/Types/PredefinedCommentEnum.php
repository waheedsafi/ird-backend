<?php

namespace App\Enums\Types;

enum PredefinedCommentEnum: int
{
    case organization_user_created = 1;
    case waiting_for_document_upload = 2;
    case document_pending_for_approval = 3;
    case signed_documents_are_rejected = 4;
    case signed_documents_are_approved = 5;
}
