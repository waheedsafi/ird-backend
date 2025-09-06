<?php

namespace App\Http\Controllers\v1\auth;

use App\Enums\Permissions\PermissionEnum;
use App\Models\Email;
use Sway\Utils\StringUtils;
use Illuminate\Http\Request;
use App\Traits\LogHelperTrait;
use App\Enums\Statuses\StatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\v1\auth\LoginRequest;
use App\Repositories\User\UserRepositoryInterface;
use App\Http\Requests\v1\auth\UpdateProfilePasswordRequest;
use App\Models\Organization;

class OrganizationAuthController extends Controller
{
    use LogHelperTrait;
    protected $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }
    public function user(Request $request)
    {
        $organization = $request->user();
        $accessToken = request()->cookie('access_token',  null);
        $locale = App::getLocale();
        $organizationStatus = DB::table('organization_statuses as us')
            ->where("us.organization_id", $organization->id)
            ->where('is_active', true)
            ->select('us.status_id')
            ->first();
        if ($organizationStatus->status_id == StatusEnum::block->value) {
            return response()->json([
                'message' => __('app_translation.account_is_block'),
            ], 401, [], JSON_UNESCAPED_UNICODE);
        } else if (
            $organizationStatus->status_id == StatusEnum::pending->value ||
            $organizationStatus->status_id == StatusEnum::rejected->value
        ) {
            return response()->json([
                'message' => __('app_translation.your_account_un_app'),
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }

        $authOrganization =  DB::table('organizations as n')
            ->where('n.id', $organization->id)
            ->join('emails as e', function ($join) {
                $join->on('n.email_id', '=', 'e.id');
            })
            ->join('contacts as c', function ($join) {
                $join->on('n.contact_id', '=', 'c.id');
            })
            ->join('role_trans as rt', function ($join) use (&$locale) {
                $join->on('n.role_id', '=', 'rt.role_id')
                    ->where('rt.language_name', $locale);
            })
            ->join('agreements as ag', function ($join) {
                $join->on('ag.organization_id', '=', 'n.id')
                    ->whereRaw('ag.id = (select max(ns2.id) from agreements as ns2 where ns2.organization_id = n.id)');
            })
            ->join('agreement_statuses as ags', function ($join) {
                $join->on('ags.agreement_id', '=', 'ag.id')
                    ->where('ags.is_active', true);
            })
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('st.status_id', '=', 'ags.status_id')
                    ->where('st.language_name', $locale);
            })
            ->select(
                "n.id",
                "n.profile",
                "n.username",
                "n.role_id",
                "e.value as email",
                "c.value as contact",
                "n.is_editable",
                "n.created_at",
                "rt.value as role_name",
                'st.name as agreement_status',
                'st.status_id as agreement_status_id',
            )->first();

        $excludedPermissionIds = [];
        if (!$organization->approved) {
            array_push($excludedPermissionIds, PermissionEnum::projects->value);
        }

        $permssions = $this->userRepository->userAuthFormattedPermissions($authOrganization->role_id, $excludedPermissionIds);
        // 1. Store permissions in redis

        return response()->json(
            [
                "user" => [
                    "id" => $authOrganization->id,
                    "profile" => $authOrganization->profile,
                    "username" => $authOrganization->username,
                    "email" => $authOrganization->email,
                    "contact" => $authOrganization->contact,
                    "is_editable" => $authOrganization->is_editable,
                    "created_at" => $authOrganization->created_at,
                    "role" => ["role" => $authOrganization->role_id, "name" => $authOrganization->role_name],
                    "status_id" => $organizationStatus->status_id,
                    "agreement_status_id" => $authOrganization->agreement_status_id,
                    "agreement_status" => $authOrganization->agreement_status,
                ],
                "permissions" => $permssions['permissions'],
                'access_token' => $accessToken
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $email = Email::where('value', '=', $credentials['email'])->first();
        if (!$email) {
            return response()->json([
                'message' => __('app_translation.email_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $loggedIn = Auth::guard('organization:api')->attempt([
            "email_id" => $email->id,
            "password" => $request->password,
        ]);

        if ($loggedIn) {
            // Get the auth user
            $organization = $loggedIn['user'];
            $organizationStatus = DB::table('organization_statuses as os')
                ->where("os.organization_id", $organization->id)
                ->where('is_active', true)
                ->select('os.status_id')
                ->first();
            if ($organizationStatus->status_id == StatusEnum::block->value) {
                return response()->json([
                    'message' => __('app_translation.account_is_block'),
                ], 401, [], JSON_UNESCAPED_UNICODE);
            } else if (
                $organizationStatus->status_id == StatusEnum::pending->value ||
                $organizationStatus->status_id == StatusEnum::rejected->value
            ) {
                return response()->json([
                    'message' => __('app_translation.your_account_un_app'),
                ], 403, [], JSON_UNESCAPED_UNICODE);
            } else if (!$organization->is_logged_in) {
                $organization->is_logged_in = true;
                $organization->save();
            }

            $locale = App::getLocale();
            $authOrganization =  DB::table('organizations as n')
                ->where('n.id', $organization->id)
                ->join('contacts as c', function ($join) {
                    $join->on('n.contact_id', '=', 'c.id');
                })
                ->join('role_trans as rt', function ($join) use (&$locale) {
                    $join->on('n.role_id', '=', 'rt.role_id')
                        ->where('rt.language_name', $locale);
                })
                ->join('agreements as ag', function ($join) {
                    $join->on('ag.organization_id', '=', 'n.id')
                        ->whereRaw('ag.id = (select max(ns2.id) from agreements as ns2 where ns2.organization_id = n.id)');
                })
                ->join('agreement_statuses as ags', function ($join) {
                    $join->on('ags.agreement_id', '=', 'ag.id')
                        ->where('ags.is_active', true);
                })
                ->join('status_trans as st', function ($join) use ($locale) {
                    $join->on('st.status_id', '=', 'ags.status_id')
                        ->where('st.language_name', $locale);
                })
                ->select(
                    "n.id",
                    "n.profile",
                    "n.username",
                    "c.value as contact",
                    "n.role_id",
                    "n.is_editable",
                    "n.created_at",
                    "rt.value as role_name",
                    'st.name as agreement_status',
                    'st.status_id as agreement_status_id',
                )->first();


            $this->storeUserLog($authOrganization->id, StringUtils::getModelName(Organization::class), "Login");
            $cookie = cookie(
                'access_token',
                $loggedIn['access_token'],
                60 * 24 * 30,
                '/',
                null,                          // null: use current domain
                true,                 // secure only in production
                true,                         // httpOnly
                false,                         // raw
                'None' // for dev, use 'None' to allow cross-origin if needed
            );
            $excludedPermissionIds = [];
            if (!$organization->approved) {
                array_push($excludedPermissionIds, PermissionEnum::projects->value);
            }

            $permssions = $this->userRepository->userAuthFormattedPermissions($authOrganization->role_id, $excludedPermissionIds);
            // 1. Store permissions in redis
            return response()->json(
                [
                    "permissions" => $permssions['permissions'],
                    "user" => [
                        "id" => $authOrganization->id,
                        "profile" => $authOrganization->profile,
                        "username" => $authOrganization->username,
                        "email" => $credentials['email'],
                        "contact" => $authOrganization->contact,
                        "is_editable" => $authOrganization->is_editable,
                        "created_at" => $authOrganization->created_at,
                        "role" => ["role" => $authOrganization->role_id, "name" => $authOrganization->role_name],
                        "status_id" => $organizationStatus->status_id,
                        "agreement_status_id" => $authOrganization->agreement_status_id,
                        "agreement_status" => $authOrganization->agreement_status,
                    ],
                    'access_token' => $loggedIn['access_token']
                ],
                200,
                [],
                JSON_UNESCAPED_UNICODE
            )->cookie($cookie);
        } else {
            return response()->json([
                'message' => __('app_translation.invalid_user_or_pass'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function logout(Request $request)
    {
        $this->storeUserLog($request->user()->id, StringUtils::getModelName(Organization::class), "Logout");

        $request->user()->invalidateToken(); // Calls the invalidateToken method defined in the trait
        return response()->json([
            'message' => __('app_translation.user_logged_out_success')
        ], 204, [], JSON_UNESCAPED_UNICODE);
    }
    public function changePassword(UpdateProfilePasswordRequest $request)
    {
        $request->validated();
        $authUser = $request->user();
        DB::beginTransaction();
        $request->validate([
            "old_password" => ["required", "min:8", "max:45"],
        ]);
        if (!Hash::check($request->old_password, $authUser->password)) {
            return response()->json([
                'errors' => ['old_password' => [__('app_translation.incorrect_password')]],
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
}
