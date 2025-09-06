<?php

namespace App\Http\Controllers\v1\app\organization;


use App\Models\Email;
use App\Models\Address;
use App\Models\Contact;
use App\Models\OrganizationTran;
use App\Models\AddressTran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Enums\Languages\LanguageEnum;
use App\Http\Requests\v1\organization\OrganizationInfoUpdateRequest;
use App\Http\Requests\v1\organization\OrganizationUpdatedMoreInformationRequest;
use App\Models\Organization;

class EditesOrganizationController extends Controller
{
    public function updateDetails(OrganizationInfoUpdateRequest $request)
    {
        $request->validated();
        $id = $request->id;
        // 1. Get director
        $organization = Organization::find($id);
        if (!$organization) {
            return response()->json([
                'message' => __('app_translation.organization_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        // Begin transaction
        DB::beginTransaction();
        $email = Email::where('value', $request->email)
            ->select('id')->first();
        // Email Is taken by someone
        if ($email) {
            if ($email->id == $organization->email_id) {
                $email->value = $request->email;
                $email->save();
            } else {
                return response()->json([
                    'message' => __('app_translation.email_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            $email = Email::where('id', $organization->email_id)->first();
            $email->value = $request->email;
            $email->save();
        }
        $contact = Contact::where('value', $request->contact)
            ->select('id')->first();
        if ($contact) {
            if ($contact->id == $organization->contact_id) {
                $contact->value = $request->contact;
                $contact->save();
            } else {
                return response()->json([
                    'message' => __('app_translation.contact_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            $contact = Contact::where('id', $organization->contact_id)->first();
            $contact->value = $request->contact;
            $contact->save();
        }


        $address = Address::where('id', $organization->address_id)
            ->select("district_id", "id", "province_id")
            ->first();
        if (!$address) {
            return response()->json([
                'message' => __('app_translation.address_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        // 4. Update organization information
        $organization->abbr = $request->abbr;
        $organization->date_of_establishment = $request->establishment_date;
        $address->province_id = $request->province['id'];
        $address->district_id = $request->district['id'];

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
            ]);
        }

        // 5. Completed
        $organization->save();
        $address->save();

        DB::commit();
        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function UpdateMoreInformation(OrganizationUpdatedMoreInformationRequest $request)
    {
        $request->validated();
        $id = $request->id;
        // 1. Get organization
        $organization = Organization::find($id);
        if (!$organization) {
            return response()->json([
                'message' => __('app_translation.organization_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        // 2. Find translations
        $organizationTrans = OrganizationTran::where('organization_id', $id)->get();
        if (!$organizationTrans) {
            return response()->json([
                'message' => __('app_translation.organization_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        // 3. Transaction
        DB::beginTransaction();

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran =  $organizationTrans->where('language_name', $code)->first();
            $tran->vision = $request["vision_{$name}"];
            $tran->mission = $request["mission_{$name}"];
            $tran->general_objective = $request["general_objes_{$name}"];
            $tran->objective = $request["objes_in_afg_{$name}"];
            $tran->save();
        }


        DB::commit();
        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            "confirm_password" => ["required", "min:8", "max:45"],
            "new_password" => ["required", "min:8", "max:45"],
            "your_account_password" => ["required"]
        ]);
        if (!Hash::check($request->your_account_password, $request->user()->password)) {
            return response()->json([
                'message' => __('app_translation.your_pass_incorrect'),
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }
        $organization = Organization::where('id', $request->organization_id)->first();
        if (!$organization) {
            return response()->json([
                'message' => __('app_translation.organization_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        DB::beginTransaction();

        $organization->password = Hash::make($request->new_password);
        $organization->save();
        DB::commit();
        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
