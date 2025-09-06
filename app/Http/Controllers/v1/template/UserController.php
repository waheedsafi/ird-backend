<?php

namespace App\Http\Controllers\v1\template;

use App\Models\User;
use App\Models\Email;
use App\Models\Contact;
use App\Models\UserStatus;
use App\Traits\FilterTrait;
use Illuminate\Http\Request;
use App\Traits\FileHelperTrait;
use App\Traits\ApplicationTrait;
use App\Enums\Types\NotifierEnum;
use App\Enums\Statuses\StatusEnum;
use Illuminate\Support\Facades\DB;
use App\Enums\Permissions\RoleEnum;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cache;
use App\Enums\Permissions\PermissionEnum;
use App\Http\Requests\v1\user\UpdateUserRequest;
use App\Http\Requests\v1\user\UserRegisterRequest;
use App\Repositories\User\UserRepositoryInterface;
use App\Http\Requests\v1\user\UpdateUserPasswordRequest;
use App\Repositories\Approval\ApprovalRepositoryInterface;
use App\Repositories\Notification\NotificationRepositoryInterface;

class UserController extends Controller
{
    use FileHelperTrait, FilterTrait, ApplicationTrait;
    private $cacheName;
    protected $userRepository;
    protected $approvalRepository;
    protected $notificationRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ApprovalRepositoryInterface $approvalRepository,
        NotificationRepositoryInterface $notificationRepository,
    ) {
        $this->cacheName = 'user_statistics';
        $this->userRepository = $userRepository;
        $this->approvalRepository = $approvalRepository;
        $this->notificationRepository = $notificationRepository;
    }
    public function index(Request $request)
    {
        $locale = App::getLocale();
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        // Start building the query
        $query = DB::table('users as u')
            ->leftJoin('contacts as c', 'c.id', '=', 'u.contact_id')
            ->join('emails as e', 'e.id', '=', 'u.email_id')
            ->join('roles as r', 'r.id', '=', 'u.role_id')
            ->join('user_statuses as us', function ($join) {
                $join->on('us.user_id', '=', 'u.id')
                    ->where('us.is_active', true);
            })
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('st.status_id', '=', 'us.status_id')
                    ->where('st.language_name', $locale);
            })
            ->join('model_job_trans as mjt', function ($join) use ($locale) {
                $join->on('mjt.model_job_id', '=', 'u.job_id')
                    ->where('mjt.language_name', $locale);
            })
            ->select(
                "u.id",
                "u.username",
                "us.status_id",
                "st.name as status",
                "u.profile",
                "u.created_at",
                "e.value AS email",
                "c.value AS contact",
                "mjt.value as job",
            );

        $this->applyDate($query, $request, 'u.created_at', 'u.created_at');
        $this->applyFilters($query, $request, [
            'username' => 'u.username',
            'created_at' => 'u.created_at',
            'status' => 'us.status_id',
            'job' => 'mjt.value',
        ]);
        $this->applySearch($query, $request, [
            'username' => 'u.username',
            'contact' => 'c.value',
            'email' => 'e.value'
        ]);

        // Apply pagination (ensure you're paginating after sorting and filtering)
        $tr = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json(
            [
                "users" => $tr,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function edit($id)
    {
        $locale = App::getLocale();

        $user = DB::table('users as u')
            ->where('u.id', $id)
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
            ->join('user_statuses as us', function ($join) use ($locale) {
                $join->on('us.user_id', '=', 'u.id')
                    ->where('us.is_active', true);
            })
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('st.status_id', '=', 'us.status_id')
                    ->where('st.language_name', $locale);
            })
            ->join('division_trans as dt', function ($join) use ($locale) {
                $join->on('dt.division_id', '=', 'u.division_id')
                    ->where('dt.language_name', $locale);
            })
            ->select(
                'u.id',
                "u.profile",
                'u.full_name',
                'u.username',
                'c.value as contact',
                'u.contact_id',
                'e.value as email',
                'rt.value as role_name',
                'u.role_id',
                "mjt.value as job",
                "u.created_at",
                "u.job_id",
                "us.status_id",
                "st.name as status",
                "dt.value as division",
                "dt.division_id",
            )
            ->first();
        if (!$user) {
            return response()->json([
                'message' => __('app_translation.user_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json(
            [
                "user" => [
                    "id" => $user->id,
                    "full_name" => $user->full_name,
                    "username" => $user->username,
                    'email' => $user->email,
                    'status' => $user->status,
                    'status_id' => $user->status_id,
                    "profile" => $user->profile,
                    "role" => ['id' => $user->role_id, 'name' => $user->role_name],
                    'contact' => $user->contact,
                    "job" => ["id" => $user->job_id, "name" => $user->job],
                    "created_at" => $user->created_at,
                    "division" => ['id' => $user->division_id, 'name' => $user->division]
                ],
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function store(UserRegisterRequest $request)
    {
        $request->validated();
        // 0. Check user a
        // 1. Check email
        $email = Email::where('value', '=', $request->email)->first();
        if ($email) {
            return response()->json([
                'message' => __('app_translation.email_exist'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }
        // 2. Check contact
        $contact = null;
        if ($request->contact) {
            $contact = Contact::where('value', '=', $request->contact)->first();
            if ($contact) {
                return response()->json([
                    'message' => __('app_translation.contact_exist'),
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
        }
        DB::beginTransaction();
        // Add email and contact
        $email = Email::create([
            "value" => $request->email
        ]);
        $contact = null;
        if ($request->contact) {
            $contact = Contact::create([
                "value" => $request->contact
            ]);
        }
        // 3. Create User
        $newUser = User::create([
            "full_name" => $request->full_name,
            "username" => $request->username,
            "email_id" => $email->id,
            "password" => Hash::make($request->password),
            "role_id" => $request->role,
            "job_id" => $request->job_id,
            "division_id" => $request->division_id,
            "contact_id" => $contact ? $contact->id : $contact,
            "profile" => null,
        ]);
        // Check for approval
        $status_id = StatusEnum::active->value;
        if ($this->approvable()) {
            $status_id = StatusEnum::pending->value;
            $this->approvalRepository->storeApproval(
                $newUser->id,
                User::class,
                NotifierEnum::confirm_adding_user->value,
                $request->request_comment
            );
            // Notification
            $message = [
                'en' => Lang::get('app_translation.user_sent_for_approval', ['username' => $newUser->username ?? 'Unknown User'], 'en'),
                'fa' => Lang::get('app_translation.user_sent_for_approval', ['username' => $newUser->username ?? 'Unknown User'], 'fa'),
                'ps' => Lang::get('app_translation.user_sent_for_approval', ['username' => $newUser->username ?? 'Unknown User'], 'ps'),
            ];
            $this->notificationRepository->sendStoreNotification(
                NotifierEnum::confirm_adding_user->value,
                $message,
                null,
                null,
                PermissionEnum::users->value,
                'users'
            );
        }

        UserStatus::create([
            "user_id" => $newUser->id,
            "saved_by" => $request->user()->id,
            "is_active" => true,
            "status_id" => $status_id,
        ]);
        $locale = App::getLocale();
        $trans = DB::table('status_trans as st')
            ->where('st.status_id', $status_id)
            ->where('st.language_name', $locale)
            ->select('st.name')
            ->first();

        DB::commit();
        Cache::forget($this->cacheName);

        return response()->json([
            'user' => [
                "id" => $newUser->id,
                "username" => $newUser->username,
                'email' => $request->email,
                "profile" => $newUser->profile,
                "job" => $request->job,
                "status" => $trans?->name ?? 'Unknown',
                "created_at" => $newUser->created_at,
            ],
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function update(UpdateUserRequest $request)
    {
        $request->validated();
        // 1. User is passed from middleware
        DB::beginTransaction();
        $user = $request->get('validatedUser');
        if ($user) {
            $email = Email::where('value', $request->email)
                ->select('id')->first();
            // Email Is taken by someone
            if ($email) {
                if ($email->id == $user->email_id) {
                    $email->value = $request->email;
                    $email->save();
                } else {
                    return response()->json([
                        'message' => __('app_translation.email_exist'),
                    ], 409, [], JSON_UNESCAPED_UNICODE);
                }
            } else {
                $email = Email::where('id', $user->email_id)->first();
                $email->value = $request->email;
                $email->save();
            }
            if (isset($request->contact)) {
                $contact = Contact::where('value', $request->contact)
                    ->select('id')->first();
                if ($contact) {
                    if ($contact->id == $user->contact_id) {
                        $contact->value = $request->contact;
                        $contact->save();
                    } else {
                        return response()->json([
                            'message' => __('app_translation.contact_exist'),
                        ], 409, [], JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    if (isset($user->contact_id)) {
                        $contact = Contact::where('id', $user->contact_id)->first();
                        $contact->value = $request->contact;
                        $contact->save();
                    } else {
                        $contact = Contact::create(['value' => $request->contact]);
                        $user->contact_id = $contact->id;
                    }
                }
            }

            // 4. Update User other attributes
            $user->full_name = $request->full_name;
            $user->username = $request->username;
            $user->role_id = $request->role;
            $user->job_id = $request->job;
            $user->division_id = $request->division;
            $user->save();

            DB::commit();
            return response()->json([
                'message' => __('app_translation.success'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        return response()->json([
            'message' => __('app_translation.user_not_found'),
        ], 404, [], JSON_UNESCAPED_UNICODE);
    }
    public function destroy($id)
    {
        DB::beginTransaction();
        $user = User::find($id);
        if ($user->role_id == RoleEnum::super->value || $user->role_id == RoleEnum::debugger->value) {
            return response()->json([
                'message' => __('app_translation.unauthorized'),
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
        if ($user) {
            // 1. Delete user email
            Email::where('id', '=', $user->email_id)->delete();
            // 2. Delete user contact
            Contact::where('id', '=', $user->contact_id)->delete();
            $user->delete();
            DB::commit();
            Cache::forget($this->cacheName);
            return response()->json([
                'message' => __('app_translation.success'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json([
                'message' => __('app_translation.failed'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }
    }
    public function changePassword(UpdateUserPasswordRequest $request)
    {
        $request->validated();
        $user = $request->get('validatedUser');
        DB::beginTransaction();
        $user->password = Hash::make($request->new_password);
        $user->save();
        DB::commit();
        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function userStatistics()
    {
        $active = StatusEnum::active->value;
        $block = StatusEnum::block->value;
        $locale = App::getLocale();

        $statistics = Cache::remember($this->cacheName . "_{$locale}", 180, function () use ($active, $block) {
            return DB::select("
            SELECT
                COUNT(u.id) AS userCount,
                (SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURRENT_DATE) AS todayCount,
                (
                    SELECT COUNT(*)
                    FROM users u2
                    INNER JOIN user_statuses us ON us.user_id = u2.id
                    WHERE us.status_id = ?
                ) AS activeUserCount,
                (
                    SELECT COUNT(*)
                    FROM users u3
                    INNER JOIN user_statuses us ON us.user_id = u3.id
                    WHERE us.status_id = ?
                ) AS inActiveUserCount
            FROM users u
        ", [$active, $block]);
        });

        return response()->json([
            'counts' => [
                "userCount" => $statistics[0]->usercount,
                "todayCount" => $statistics[0]->todaycount,
                "activeUserCount" => $statistics[0]->activeusercount,
                "inActiveUserCount" => $statistics[0]->inactiveusercount,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
