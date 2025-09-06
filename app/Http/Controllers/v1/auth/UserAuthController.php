<?php

namespace App\Http\Controllers\v1\auth;

use App\Models\User;
use App\Models\Email;
use App\Models\UserStatus;
use Sway\Utils\StringUtils;
use Illuminate\Http\Request;
use App\Traits\LogHelperTrait;
use App\Enums\Statuses\StatusEnum;
use App\Enums\Types\NotifierEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\v1\auth\LoginRequest;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Redis\RedisRepositoryInterface;
use App\Http\Requests\v1\auth\UpdateProfilePasswordRequest;

class UserAuthController extends Controller
{
    use LogHelperTrait;
    protected $userRepository;
    protected $redisRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        RedisRepositoryInterface $redisRepository
    ) {
        $this->userRepository = $userRepository;
        $this->redisRepository = $redisRepository;
    }
    public function user(Request $request)
    {
        $locale = App::getLocale();
        $user = $request->user();
        $accessToken = request()->cookie('access_token',  null);
        $userStatus = DB::table('user_statuses as us')
            ->where("us.user_id", $user->id)
            ->where('is_active', true)
            ->select('us.status_id')
            ->first();
        if ($userStatus->status_id == StatusEnum::block->value) {
            return response()->json([
                'message' => __('app_translation.account_is_block'),
            ], 401, [], JSON_UNESCAPED_UNICODE);
        } else if (
            $userStatus->status_id == StatusEnum::pending->value ||
            $userStatus->status_id == StatusEnum::rejected->value
        ) {
            return response()->json([
                'message' => __('app_translation.your_account_un_app'),
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }

        $user = DB::table('users as u')
            ->where('u.id', $user->id)
            ->join('model_job_trans as mjt', function ($join) use ($locale) {
                $join->on('mjt.model_job_id', '=', 'u.job_id')
                    ->where('mjt.language_name', $locale);
            })
            ->leftJoin('contacts as c', 'c.id', '=', 'u.contact_id')
            ->join('emails as e', 'e.id', '=', 'u.email_id')
            ->join('role_trans as rt', function ($join) use ($locale) {
                $join->on('rt.role_id', '=', 'u.role_id')
                    ->where('rt.language_name', $locale);
            })
            ->join('division_trans as dt', function ($join) use ($locale) {
                $join->on('u.division_id', '=', 'dt.division_id')
                    ->where('dt.language_name', $locale);
            })
            ->select(
                'u.id',
                "u.profile",
                'u.full_name',
                'u.username',
                'c.value as contact',
                'u.contact_id',
                'rt.value as role_name',
                'rt.role_id',
                "mjt.value as job",
                "e.value as email",
                "u.created_at",
                "dt.value as division",
            )
            ->first();


        $permssions = $this->userRepository->userAuthFormattedPermissions($user->role_id);
        // 1. Store permissions in redis
        $this->redisRepository->storeUserPermissions($permssions['redisPermissions'], $user->id, $user->role_id);

        return response()->json(
            [
                "user" => [
                    "id" => $user->id,
                    "full_name" => $user->full_name,
                    "username" => $user->username,
                    'email' => $user->email,
                    "profile" => $user->profile,
                    "role" => ["role" => $user->role_id, "name" => $user->role_name],
                    'contact' => $user->contact,
                    "job" => $user->job,
                    "created_at" => $user->created_at,
                    "division" => $user->division,
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
        $locale = App::getLocale();
        $email = Email::where('value', '=', $credentials['email'])->first();
        if (!$email) {
            return response()->json([
                'message' => __('app_translation.email_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $loggedIn = Auth::guard('user:api')->attempt([
            "email_id" => $email->id,
            "password" => $request->password,
        ]);
        if ($loggedIn) {
            // Get the auth user
            $user = $loggedIn['user'];
            $userStatus = DB::table('user_statuses as us')
                ->where("us.user_id", $user->id)
                ->where('is_active', true)
                ->select('us.status_id')
                ->first();
            if ($userStatus->status_id == StatusEnum::block->value) {
                return response()->json([
                    'message' => __('app_translation.account_is_block'),
                ], 401, [], JSON_UNESCAPED_UNICODE);
            } else if (
                $userStatus->status_id == StatusEnum::pending->value ||
                $userStatus->status_id == StatusEnum::rejected->value
            ) {
                return response()->json([
                    'message' => __('app_translation.your_account_un_app'),
                ], 403, [], JSON_UNESCAPED_UNICODE);
            }

            $user = DB::table('users as u')
                ->where('u.id', $user->id)
                ->join('model_job_trans as mjt', function ($join) use ($locale) {
                    $join->on('mjt.model_job_id', '=', 'u.job_id')
                        ->where('mjt.language_name', $locale);
                })
                ->leftJoin('contacts as c', 'c.id', '=', 'u.contact_id')
                ->join('role_trans as rt', function ($join) use ($locale) {
                    $join->on('rt.role_id', '=', 'u.role_id')
                        ->where('rt.language_name', $locale);
                })->join('division_trans as dt', function ($join) use ($locale) {
                    $join->on('u.division_id', '=', 'dt.division_id')
                        ->where('dt.language_name', $locale);
                })
                ->select(
                    'u.id',
                    "u.profile",
                    'u.full_name',
                    'u.username',
                    'c.value as contact',
                    'u.contact_id',
                    'rt.value as role_name',
                    'rt.role_id',
                    "mjt.value as job",
                    "u.created_at",
                    "dt.value as division",
                )
                ->first();

            $this->storeUserLog($user->id, StringUtils::getModelName(User::class), "Login");
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

            $permssions = $this->userRepository->userAuthFormattedPermissions($user->role_id);
            // 1. Store permissions in redis

            $this->redisRepository->storeUserPermissions($permssions['redisPermissions'], $user->id, $user->role_id);
            return response()->json(
                [
                    "permissions" => $permssions['permissions'],
                    "user" => [
                        "id" => $user->id,
                        "full_name" => $user->full_name,
                        "username" => $user->username,
                        'email' => $credentials['email'],
                        "profile" => $user->profile,
                        "role" => ["role" => $user->role_id, "name" => $user->role_name],
                        'contact' => $user->contact,
                        "job" => $user->job,
                        "created_at" => $user->created_at,
                        "division" => $user->division,
                    ],
                    'access_token' => $loggedIn['access_token']
                ],
                200,
                [],
                JSON_UNESCAPED_UNICODE
            )->cookie($cookie);
        } else {
            return response()->json([
                'message' => __('app_translation.invalid_user_or_pass')
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function logout(Request $request)
    {
        $this->storeUserLog($request->user()->id, StringUtils::getModelName(User::class), "Logout");

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
