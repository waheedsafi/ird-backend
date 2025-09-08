<?php

namespace App\Http\Controllers\v1\template;

use App\Models\Role;
use App\Models\RoleTrans;
use Illuminate\Http\Request;
use App\Models\RoleAssignment;
use App\Models\RolePermission;
use App\Models\RolePermissionSub;
use App\Models\RoleAssignmentItem;
use Illuminate\Support\Facades\DB;
use App\Enums\Permissions\RoleEnum;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Enums\Languages\LanguageEnum;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Requests\v1\role\RoleStoreRequest;
use App\Repositories\Role\RoleRepositoryInterface;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }
    public function index()
    {
        $locale = App::getLocale();
        $tr = DB::table('role_trans as rt')
            ->where('rt.role_id', '!=', RoleEnum::debugger->value)
            ->where('rt.language_name', $locale)
            ->select('rt.role_id as id', "rt.value as name", 'rt.created_at')->get();
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function indexByUser(Request $request)
    {
        $authUser = $request->user();
        $tr = [];
        $locale = App::getLocale();

        if ($authUser->role_id === RoleEnum::super->value) {
            $tr = DB::table('role_trans as rt')
                ->whereNotIn('rt.role_id', [RoleEnum::debugger->value, RoleEnum::super->value])
                ->where('rt.language_name', $locale)
                ->select('rt.role_id as id', "rt.value as name", 'rt.created_at')->get();
        } else {
            $tr = DB::table('role_assignments as ra')
                ->where('ra.assigner_role_id', $authUser->role_id)
                ->where('ra.permission', PermissionEnum::users->value)
                ->join('role_assignment_items as rai', 'ra.id', '=', 'rai.role_assignment_id')
                ->join('role_trans as rt', function ($join) use ($locale) {
                    $join->on('rt.role_id', '=', 'rai.assignee_role_id')
                        ->where('rt.language_name', $locale);
                })
                ->whereNotIn('rt.role_id', [RoleEnum::debugger->value, RoleEnum::super->value])

                ->select('rt.role_id as id', "rt.value as name")
                ->get();
        }
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function indexByRole($id)
    {
        $locale = App::getLocale();
        $tr = DB::table('role_trans as rt')
            ->whereNotIn('rt.role_id', [RoleEnum::debugger->value, $id])
            ->where('rt.language_name', $locale)
            ->select('rt.role_id as id', "rt.value as name", 'rt.created_at')->get();
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store(RoleStoreRequest $request)
    {
        $request->validated();

        $requestedPermissions = $request->permissions;
        $sanitized = [];
        $denied = [];

        $this->sanitizePermissions($requestedPermissions, $sanitized);
        $roleId = RoleEnum::super->value;
        $actualPermissions = $this->roleRepository->rolePermissions($roleId);
        $this->validatePermissions($sanitized, $denied, $actualPermissions, $request->user_role_assignment);

        if (
            RoleTrans::where('language_name', 'en')
            ->where('value', $request->name_english)
            ->exists()
        ) {
            return response()->json([
                'errors' => [[__('app_translation.duplicate_english')]]
            ], 422);
        }
        if (empty($denied)) {
            // Success
            // 1. Create Role
            DB::beginTransaction();
            $newRole = Role::create([]);
            foreach (LanguageEnum::LANGUAGES as $code => $name) {
                RoleTrans::create([
                    "value" => $request["name_{$name}"],
                    "role_id" => $newRole->id,
                    "language_name" => $code,
                ]);
            }

            // Create assignment role
            $roleAssignment = RoleAssignment::create([
                'assigned_by' => $request->user()->id,
                'assigner_role_id' => $newRole->id,
                'permission' => PermissionEnum::users->value,
            ]);
            foreach ($request->user_role_assignment as $assignment) {
                RoleAssignmentItem::create([
                    'role_assignment_id' => $roleAssignment->id,
                    'assignee_role_id' => $assignment['id'],
                ]);
            }
            // Assign Role Permissions
            foreach ($sanitized as $san) {
                $rolePermission = RolePermission::create([
                    'role' => $newRole->id,
                    'permission' => $san['id'],
                    'view' => $san['view'],
                    'add' => $san['add'],
                    'edit' => $san['edit'],
                    'delete' => $san['delete'],
                ]);
                foreach ($san['sub'] as $subPermission) {
                    $meta = SubPermissionEnum::getMetaById($subPermission['id']);
                    RolePermissionSub::create([
                        'view' => $san['view'],
                        'add' => $san['add'],
                        'edit' => $san['edit'],
                        'delete' => $san['delete'],
                        "role_permission_id" =>  $rolePermission->id,
                        "sub_permission_id" => $subPermission['id'],
                        'is_category' => $meta['is_category'],
                    ]);
                }
            }
            DB::commit();

            $locale = App::getLocale();
            $name = $request->name_english;
            if ($locale == LanguageEnum::farsi->value) {
                $name = $request->name_farsi;
            } else if ($locale == LanguageEnum::pashto->value) {
                $name = $request->name_pashto;
            }
            return response()->json([
                'message' => __('app_translation.success'),
                'role' => [
                    "id" => $newRole->id,
                    "name" => $name,
                    "created_at" => $newRole->created_at
                ]
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json([
                'message' => __('app_translation.failed'),
                'errors' => $denied
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
    public function edit($id)
    {
        $role = DB::table('role_trans as rt')
            ->where('rt.role_id', $id)
            ->select(
                'rt.role_id',
                DB::raw("MAX(CASE WHEN rt.language_name = 'fa' THEN value END) as farsi"),
                DB::raw("MAX(CASE WHEN rt.language_name = 'en' THEN value END) as english"),
                DB::raw("MAX(CASE WHEN rt.language_name = 'ps' THEN value END) as pashto")
            )
            ->groupBy('rt.role_id')
            ->first();

        $permissions = $this->roleRepository->combineRolesPermssions(RoleEnum::super->value, $id);
        $formatted = $this->roleRepository->formatRolePermissions($permissions, false);
        $data = [
            'name_english' => $role->english,
            'name_farsi' => $role->farsi,
            'name_pashto' => $role->pashto,
            'permissions' => $formatted
        ];

        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function newRolePermissions()
    {
        $roleId = RoleEnum::super->value;
        $permissions = $this->roleRepository->rolePermissions($roleId);
        $formatted = $this->roleRepository->formatRolePermissions($permissions, true);

        return response()->json($formatted, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function rolesAssignments(Request $request)
    {
        $role_id = $request->input('id', 0);
        $permission_id = $request->input('per', 0);
        $locale = App::getLocale();

        $assignments = DB::table('role_assignments as ra')
            ->where('ra.assigner_role_id', $role_id)
            ->where('ra.permission', $permission_id)
            ->join('role_assignment_items as rai', 'ra.id', '=', 'rai.role_assignment_id')
            ->join('role_trans as rt', function ($join) use ($locale) {
                $join->on('rt.role_id', '=', 'rai.assignee_role_id')
                    ->where('rt.language_name', $locale);
            })

            ->select('rt.role_id as id', "rt.value as name")
            ->get();

        return response()->json($assignments, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function update(RoleStoreRequest $request)
    {
        $request->validated();
        // This validation not exist in UrgencyStoreRequest
        $request->validate([
            "id" => "required"
        ]);
        // Permission validation
        if (
            $request->id === $request->user()->role_id
            || $request->id === RoleEnum::super->value ||
            $request->id === RoleEnum::debugger->value
        ) {
            return response()->json([
                'errors' => [[__('app_translation.unauthorized')]]
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }
        // 1. Find
        $role = Role::find($request->id);
        if (!$role) {
            return response()->json([
                'errors' => [[__('app_translation.role_not_found')]]
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $requestedPermissions = $request->permissions;
        $sanitized = [];
        $denied = [];
        $this->sanitizePermissions($requestedPermissions, $sanitized);
        $roleId = RoleEnum::super->value;
        $actualPermissions = $this->roleRepository->rolePermissions($roleId);
        $this->validatePermissions($sanitized, $denied, $actualPermissions, $request->user_role_assignment);
        if (empty($denied)) {
            // Success
            // 1. Update Role Translation
            DB::beginTransaction();
            $trans = RoleTrans::where('role_id', $role->id)
                ->select('id', 'language_name', 'value')
                ->get();
            // Update
            foreach (LanguageEnum::LANGUAGES as $code => $name) {
                $tran =  $trans->where('language_name', $code)->first();
                $tran->value = $request["name_{$name}"];
                $tran->save();
            }

            // 2. Remove prevoius Assignment role
            $roleAssignmentIds = RoleAssignment::where('assigner_role_id', $role->id)->pluck('id');
            RoleAssignmentItem::whereIn('role_assignment_id', $roleAssignmentIds)->delete();
            RoleAssignment::where('assigner_role_id', $role->id)->delete();
            // 3. Create New Assignment role
            $roleAssignment = RoleAssignment::create([
                'assigned_by' => $request->user()->id,
                'assigner_role_id' => $role->id,
                'permission' => PermissionEnum::users->value,
            ]);
            foreach ($request->user_role_assignment as $assignment) {
                RoleAssignmentItem::create([
                    'role_assignment_id' => $roleAssignment->id,
                    'assignee_role_id' => $assignment['id'],
                ]);
            }

            // 4. Remove prevoius permissions
            $rolePermissionIds = RolePermission::where('role', $role->id)->pluck('id');
            // 4. Delete related RolePermissionSub entries
            RolePermissionSub::whereIn('role_permission_id', $rolePermissionIds)->delete();

            // 5. Delete RolePermission entries
            RolePermission::where('role', $role->id)->delete();
            // 6. Assign Role Permissions
            foreach ($sanitized as $san) {
                $rolePermission = RolePermission::create([
                    'role' => $role->id,
                    'permission' => $san['id'],
                    'view' => $san['view'],
                    'add' => $san['add'],
                    'edit' => $san['edit'],
                    'delete' => $san['delete'],
                ]);
                foreach ($san['sub'] as $subPermission) {
                    $meta = SubPermissionEnum::getMetaById($subPermission['id']);
                    RolePermissionSub::create([
                        'view' => $san['view'],
                        'add' => $san['add'],
                        'edit' => $san['edit'],
                        'delete' => $san['delete'],
                        "role_permission_id" =>  $rolePermission->id,
                        "sub_permission_id" => $subPermission['id'],
                        'is_category' => $meta['is_category'],
                    ]);
                }
            }
            DB::commit();

            $locale = App::getLocale();
            $name = $request->name_english;
            if ($locale == LanguageEnum::farsi->value) {
                $name = $request->name_farsi;
            } else if ($locale == LanguageEnum::pashto->value) {
                $name = $request->name_pashto;
            }
            return response()->json([
                'message' => __('app_translation.success'),
                'role' => [
                    "id" => $role->id,
                    "name" => $name,
                    "created_at" => $role->created_at
                ]
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json([
                'message' => __('app_translation.failed'),
                'errors' => $denied
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    private function sanitizePermissions(&$requestedPermissions, &$sanitized)
    {
        foreach ($requestedPermissions as $requested) {
            if ($requested['view']) {
                $tempSubPermission = [];
                foreach ($requested['sub'] as $requestedSub) {
                    if ($requestedSub['view']) {
                        $tempSubPermission[] = $requestedSub;
                    }
                }
                $requested['sub'] = $tempSubPermission;
                $sanitized[] = $requested;
            }
        }
    }
    private function validatePermissions(&$sanitized, &$denied, &$actualPermissions, $user_role_assignment)
    {
        // Trans
        $you_do_not_Trans = __("app_translation.you_do_not");
        $access_to_Trans = __("app_translation.access_to");
        $addTrans = __("front_end.add");
        $editTrans = __("front_end.edit");
        $deleteTrans = __("front_end.delete");
        $viewTrans = __("front_end.view");

        foreach ($sanitized as $san) {
            $actual = $actualPermissions->firstWhere('id', $san['id']);
            $permissionTrans = __("front_end." . $actual->permission);

            // Check for user permission
            if ($san['id'] === PermissionEnum::users->value) {
                if (
                    count($user_role_assignment) < 1
                ) {
                    $denied[] = [__("app_translation.select_item") . ' (' . __("front_end.user_role_assignment") . ').'];
                }
            }

            if (!$actual) {
                $denied[] = ["{$you_do_not_Trans} {$access_to_Trans} '{$permissionTrans}'"];
            } else {

                if (!$actual->view && $san['view']) {
                    $denied[] = ["{$you_do_not_Trans} <{$viewTrans}> {$access_to_Trans} '{$permissionTrans}'"];
                }
                if (!$actual->add && $san['add']) {
                    $denied[] = ["{$you_do_not_Trans} <{$addTrans}> {$access_to_Trans} '{$permissionTrans}'"];
                }
                if (!$actual->edit && $san['edit']) {
                    $denied[] = ["{$you_do_not_Trans} <{$editTrans}> {$access_to_Trans} {$permissionTrans}"];
                }
                if (!$actual->delete && $san['delete']) {
                    $denied[] = ["{$you_do_not_Trans} <{$deleteTrans}> {$access_to_Trans} '{$permissionTrans}'"];
                }
            }
            foreach ($san['sub'] as $subPermission) {
                if ($subPermission['id'] === SubPermissionEnum::configurations_role->value) {
                    $denied[] = [__("app_translation.unauthorized_to_set")];
                    continue;
                }
                $actualSub = $actualPermissions->firstWhere('sub_permission_id', $subPermission['id']);
                $subPermissionTrans = __("front_end." . $actualSub->name);

                if (!$actualSub) {
                    $denied[] = ["{$you_do_not_Trans} {$access_to_Trans} '{$subPermissionTrans}'"];
                } else {
                    $trans = __("front_end." . $actualSub->name);
                    if (!$actualSub->sub_view && $subPermission['view']) {
                        $denied[] = ["{$you_do_not_Trans} <{$viewTrans}> {$access_to_Trans} '{$trans}'"];
                    }
                    if (!$actualSub->sub_add && $subPermission['add']) {
                        $denied[] = ["{$you_do_not_Trans} <{$addTrans}> {$access_to_Trans} '{$trans}'"];
                    }
                    if (!$actualSub->sub_edit && $subPermission['edit']) {
                        $denied[] = ["{$you_do_not_Trans} <{$editTrans}> {$access_to_Trans} '{$trans}'"];
                    }
                    if (!$actualSub->sub_delete && $subPermission['delete']) {
                        $denied[] = ["{$you_do_not_Trans} <{$deleteTrans}> {$access_to_Trans} '{$permissionTrans}'"];
                    }
                }
            }
        }
    }
}
