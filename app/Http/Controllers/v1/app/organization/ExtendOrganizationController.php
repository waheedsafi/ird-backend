<?php

namespace App\Http\Controllers\v1\app\organization;

use App\Models\Organization;
use App\Models\Email;
use App\Models\Address;
use App\Models\Contact;
use App\Models\OrganizationTran;
use App\Models\Document;
use App\Models\Agreement;
use App\Models\OrganizationStatus;
use App\Models\AddressTran;
use Illuminate\Support\Carbon;
use App\Enums\Types\CountryEnum;
use App\Enums\Types\TaskTypeEnum;
use App\Models\AgreementDirector;
use App\Models\AgreementDocument;
use App\Enums\Statuses\StatusEnum;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AgreementRepresenter;
use App\Enums\Languages\LanguageEnum;
use App\Enums\Checklist\ChecklistEnum;
use App\Http\Requests\v1\organization\ExtendOrganizationRequest;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\Director\DirectorRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;
use App\Repositories\Representative\RepresentativeRepositoryInterface;
use App\Traits\UtilHelperTrait;

class ExtendOrganizationController extends Controller
{
    use UtilHelperTrait;
    protected $pendingTaskRepository;
    protected $notificationRepository;
    protected $approvalRepository;
    protected $directorRepository;
    protected $representativeRepository;
    protected $storageRepository;

    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
        DirectorRepositoryInterface $directorRepository,
        RepresentativeRepositoryInterface $representativeRepository,
        StorageRepositoryInterface $storageRepository
    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;
        $this->directorRepository = $directorRepository;
        $this->representativeRepository = $representativeRepository;
        $this->storageRepository = $storageRepository;
    }

    public function extendOrganizationAgreement(ExtendOrganizationRequest $request)
    {
        // return $request;
        $organization_id = $request->organization_id;
        $request->validated();
        $authUser = $request->user();
        // Step.1
        // Email and Contact
        $organization = Organization::find($organization_id);
        if (!$organization) {
            return response()->json(
                [
                    'message' => __('app_translation.organization_not_found'),
                ],
                200,
                [],
                JSON_UNESCAPED_UNICODE,
            );
        }
        DB::beginTransaction();
        $email = Email::where('value', $request->email)->select('id')->first();
        // Email Is taken by someone
        if ($email) {
            if ($email->id == $organization->email_id) {
                $email->value = $request->email;
                $email->save();
            } else {
                return response()->json(
                    [
                        'message' => __('app_translation.email_exist'),
                    ],
                    409,
                    [],
                    JSON_UNESCAPED_UNICODE,
                );
            }
        } else {
            $email = Email::where('id', $organization->email_id)->first();
            $email->value = $request->email;
            $email->save();
        }
        $contact = Contact::where('value', $request->contact)->select('id')->first();
        if ($contact) {
            if ($contact->id == $organization->contact_id) {
                $contact->value = $request->contact;
                $contact->save();
            } else {
                return response()->json(
                    [
                        'message' => __('app_translation.contact_exist'),
                    ],
                    409,
                    [],
                    JSON_UNESCAPED_UNICODE,
                );
            }
        } else {
            $contact = Contact::where('id', $organization->contact_id)->first();
            $contact->value = $request->contact;
            $contact->save();
        }

        // Address and organization
        $address = Address::where('id', $organization->address_id)->select('district_id', 'id', 'province_id')->first();
        if (!$address) {
            return response()->json(
                [
                    'message' => __('app_translation.address_not_found'),
                ],
                404,
                [],
                JSON_UNESCAPED_UNICODE,
            );
        }
        $address->province_id = $request->province['id'];
        $address->district_id = $request->district['id'];
        // * Update organization information
        $organization->abbr = $request->abbr;
        $organization->moe_registration_no = $request->moe_registration_no;
        // * Translations
        $addressTrans = AddressTran::where('address_id', $address->id)->get();
        $organizationTrans = OrganizationTran::where('organization_id', $organization->id)->get();
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $addressTran = $addressTrans->where('language_name', $code)->first();
            $organizationTran = $organizationTrans->where('language_name', $code)->first();
            $addressTran->update([
                'area' => $request["area_{$name}"],
            ]);
            $organizationTran->update([
                'name' => $request["name_{$name}"],
                'vision' => $request["vision_{$name}"],
                'mission' => $request["mission_{$name}"],
                'general_objective' => $request["general_objes_{$name}"],
                'objective' => $request["objes_in_afg_{$name}"],
            ]);
        }
        $organization->save();
        $address->save();

        // Step.3
        // Agreement
        $agreement = Agreement::create([
            'organization_id' => $organization->id,
            'agreement_no' => '',
        ]);
        $agreement->agreement_no = 'AG' . '-' . Carbon::now()->year . '-' . $agreement->id;
        $agreement->save();
        OrganizationStatus::where('agreement_id', $agreement->id)->update(['is_active' => false]);
        $organizationStatus = OrganizationStatus::create([
            'agreement_id' => $agreement->id,
            'userable_id' => $authUser->id,
            'userable_type' => $this->getModelName(get_class($authUser)),
            'is_active' => true,
            'status_id' => StatusEnum::document_upload_required->value,
            'comment' => 'Extend Form Complete',
        ]);

        // Step.3 Ensure task exists before proceeding
        $task = $this->pendingTaskRepository->pendingTaskExist($request->user(), TaskTypeEnum::organization_agreement_extend->value, $organization_id);
        if (!$task) {
            return response()->json(
                [
                    'error' => __('app_translation.task_not_found'),
                ],
                404,
            );
        }
        // step.4 Check task exists
        $exclude = [CheckListEnum::organization_register_form_en->value, CheckListEnum::organization_register_form_fa->value, CheckListEnum::organization_register_form_ps->value];
        // If Directory Nationality is abroad ask for Work Permit
        if ($request->new_director == true) {
            if ($request->nationality['id'] == CountryEnum::afghanistan->value) {
                array_push($exclude, CheckListEnum::director_work_permit->value);
            }
        } else {
            array_push($exclude, ChecklistEnum::director_nid->value);
            if ($request->country['id'] == CountryEnum::afghanistan->value) {
                array_push($exclude, CheckListEnum::director_work_permit->value);
            }
        }
        if ($request->new_represent == false) {
            array_push($exclude, CheckListEnum::organization_representor_letter->value);
        }

        $directorDocumentsId = [];
        $representativeDocumentsId = [];
        $this->storageRepository->documentStore($agreement->id, $organization_id, $task->id, function ($documentData) use (&$directorDocumentsId, &$representativeDocumentsId) {
            $checklist_id = $documentData['check_list_id'];
            $document = Document::create([
                'actual_name' => $documentData['actual_name'],
                'size' => $documentData['size'],
                'path' => $documentData['path'],
                'type' => $documentData['type'],
                'check_list_id' => $checklist_id,
            ]);
            if ($checklist_id == CheckListEnum::director_work_permit->value || $checklist_id == CheckListEnum::director_nid->value) {
                array_push($directorDocumentsId, $document->id);
            } elseif ($checklist_id == CheckListEnum::organization_representor_letter->value) {
                array_push($representativeDocumentsId, $document->id);
            }

            AgreementDocument::create([
                'document_id' => $document->id,
                'agreement_id' => $documentData['agreement_id'],
            ]);
        });
        // Director with Agreement
        $director_id = null;
        if ($request->new_director == true) {
            // New director is assigned
            $director = $this->directorRepository->storeOrganizationDirector($request, $organization_id, $agreement->id, $directorDocumentsId, true, $authUser->id, $this->getModelName(get_class($authUser)));
            $director_id = $director->id;
        } else {
            $director_id = $request->prev_dire['id'];
        }
        AgreementDirector::create([
            'agreement_id' => $agreement->id,
            'director_id' => $director_id,
        ]);

        // Representative with agreement
        $representer_id = null;
        if ($request->new_represent == true) {
            // New representative is assigned
            $representer = $this->representativeRepository->storeRepresentative($request, $organization_id, $agreement->id, $representativeDocumentsId, true, $authUser->id, $this->getModelName(get_class($authUser)));
            $representer_id = $representer->id;
        } else {
            $representer_id = $request->prev_rep['id'];
        }
        AgreementRepresenter::create([
            'agreement_id' => $agreement->id,
            'representer_id' => $representer_id,
        ]);

        $this->pendingTaskRepository->destroyPendingTask($authUser, TaskTypeEnum::organization_agreement_extend->value, $organization_id);

        DB::commit();
        return response()->json(
            [
                'message' => __('app_translation.success'),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE,
        );
    }
}
