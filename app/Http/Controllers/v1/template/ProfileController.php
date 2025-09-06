<?php

namespace App\Http\Controllers\v1\template;

use App\Models\Email;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Traits\FileHelperTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\v1\profile\ProfileUpdateRequest;
use App\Http\Requests\v1\auth\UpdateProfilePasswordRequest;
use App\Http\Requests\v1\profile\OrganizationProfileUpdateRequest;

class ProfileController extends Controller
{
    use FileHelperTrait;
    public function update(ProfileUpdateRequest $request)
    {
        $request->validated();
        $authUser = $request->user();
        // Begin transaction
        DB::beginTransaction();
        // 2. Get Email
        $email = Email::where('value', $request->email)
            ->select('id')->first();
        // Email Is taken by someone
        if ($email) {
            if ($email->id == $authUser->email_id) {
                $email->value = $request->email;
                $email->save();
            } else {
                return response()->json([
                    'message' => __('app_translation.email_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            }
        } else if ($request->email) {
            $email = Email::where('id', $authUser->email_id)->first();
            $email->value = $request->email;
            $email->save();
        }
        $contact = Contact::where('value', $request->contact)
            ->select('id')->first();
        if ($contact) {
            if ($contact->id == $authUser->contact_id) {
                $contact->value = $request->contact;
                $contact->save();
            } else {
                return response()->json([
                    'message' => __('app_translation.contact_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            }
        } else if ($request->contact) {
            $contact = Contact::where('id', $authUser->contact_id)->first();
            $contact->value = $request->contact;
            $contact->save();
        }
        $authUser->full_name = $request->full_name;
        $authUser->username = $request->username;
        $authUser->save();
        DB::commit();

        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function delete(Request $request)
    {
        $authUser = $request->user();
        // 1. delete old profile
        $this->deleteDocument($this->transformToPrivate($authUser->profile));
        // 2. Update the profile
        $authUser->profile = null;
        $authUser->save();
        return response()->json([
            'message' => __('app_translation.success')
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function updatePicture(Request $request)
    {
        $request->validate([
            'profile' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);
        return $this->savePicture($request, 'user-profile', 'profile');
    }

    public function savePicture(Request $request, $dynamic_path, $profile)
    {
        $authUser = $request->user();
        $path = $this->storeProfile($request, $authUser->id, $dynamic_path, $profile);
        if ($path != null) {
            // 1. delete old profile
            $this->deleteDocument($this->deleteDocument($this->transformToPrivate($authUser->profile)));
            // 2. Update the profile
            $authUser->profile = $path;
        }
        $authUser->save();
        return response()->json([
            'message' => __('app_translation.success'),
            "profile" => $authUser->profile
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function changePassword(UpdateProfilePasswordRequest $request)
    {
        $request->validated();
        $authUser = $request->user();
        DB::beginTransaction();
        if (!Hash::check($request->old_password, $authUser->password)) {
            return response()->json([
                'message' => __('app_translation.incorrect_password'),
            ], 422, [], JSON_UNESCAPED_UNICODE);
        } else {
            $authUser->password = Hash::make($request->new_password);
            $authUser->save();
        }
        DB::commit();
        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    // Organization
    public function updateOrganization(OrganizationProfileUpdateRequest $request)
    {
        $request->validated();
        $authUser = $request->user();
        // Begin transaction
        DB::beginTransaction();
        // 2. Get Email
        $email = Email::where('value', $request->email)
            ->select('id')->first();
        // Email Is taken by someone
        if ($email) {
            if ($email->id == $authUser->email_id) {
                $email->value = $request->email;
                $email->save();
            } else {
                return response()->json([
                    'message' => __('app_translation.email_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            }
        } else if ($request->email) {
            $email = Email::where('id', $authUser->email_id)->first();
            $email->value = $request->email;
            $email->save();
        }
        $contact = Contact::where('value', $request->contact)
            ->select('id')->first();
        if ($contact) {
            if ($contact->id == $authUser->contact_id) {
                $contact->value = $request->contact;
                $contact->save();
            } else {
                return response()->json([
                    'message' => __('app_translation.contact_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            }
        } else if ($request->contact) {
            $contact = Contact::where('id', $authUser->contact_id)->first();
            $contact->value = $request->contact;
            $contact->save();
        }
        $authUser->username = $request->username;
        $authUser->save();
        DB::commit();

        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function updateOrganizationPicture(Request $request)
    {
        $request->validate([
            'profile' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);
        return $this->savePicture($request, 'organization-profile', 'profile');
    }
    // Donor

    public function updateDonorPicture(Request $request)
    {
        $request->validate([
            'profile' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);
        return $this->savePicture($request, 'donor-profile', 'profile');
    }
}
