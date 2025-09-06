<?php

namespace App\Http\Controllers\v1\template;

use App\Models\UserStatus;
use Illuminate\Http\Request;
use App\Traits\UtilHelperTrait;
use App\Enums\Statuses\StatusEnum;
use App\Models\OrganizationStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\DonorStatus;

class StatusController extends Controller
{
    use UtilHelperTrait;
    // Users
    public function userIndex($id)
    {
        $locale = App::getLocale();

        $userStatus = DB::table('user_statuses as us')
            ->where("us.user_id", $id)
            ->where('is_active', true)
            ->select('us.status_id')
            ->first();
        if (
            $userStatus->status_id == StatusEnum::pending->value ||
            $userStatus->status_id == StatusEnum::rejected->value
        ) {
            return response()->json(
                [
                    'message' => __('app_translation.user_need_approval')
                ],
                422,
                [],
                JSON_UNESCAPED_UNICODE
            );
        } else if ($userStatus->status_id == StatusEnum::block->value) {
            // Start building the query
            $tr = DB::table('status_trans as st')
                ->where('st.status_id', StatusEnum::active->value)
                ->where('st.language_name', $locale)
                ->select(
                    "st.status_id as id",
                    "st.name",
                )
                ->get();
        } else {
            $tr = DB::table('status_trans as st')
                ->where('st.status_id', StatusEnum::block->value)
                ->where('st.language_name', $locale)
                ->select(
                    "st.status_id as id",
                    "st.name",
                )
                ->get();
        }


        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function userStatuses($id)
    {
        $locale = App::getLocale();

        // Start building the query
        $tr = DB::table('users as u')
            ->where('u.id', $id)
            ->join('user_statuses as us', 'u.id', '=', 'us.user_id')
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('us.status_id', '=', 'st.status_id')
                    ->where('st.language_name', $locale);
            })
            ->leftJoin('users as user', 'user.id', '=', 'us.saved_by')
            ->select(
                "us.id",
                "st.name",
                "st.status_id",
                "us.is_active",
                "us.comment",
                "user.username as saved_by",
                "us.created_at",
            )
            ->orderByDesc('us.id')
            ->get();

        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function storeUser(Request $request)
    {
        // Validate request
        $validatedData = $request->validate([
            'id' => 'required|integer',
            'status_id' => 'required|integer',
            'comment' => 'required|string',
            'status' => 'required'
        ]);
        $userStatus = DB::table('user_statuses as us')
            ->where("us.user_id", $validatedData['id'])
            ->where('is_active', true)
            ->select('us.status_id')
            ->first();
        if (
            $userStatus->status_id == StatusEnum::pending->value ||
            $userStatus->status_id == StatusEnum::rejected->value
        ) {
            return response()->json(
                [
                    'message' => __('app_translation.user_need_approval')
                ],
                422,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }
        $authUser = $request->user();
        // Begin transaction
        DB::beginTransaction();
        // Fetch the currently active status for this organization
        DB::table('user_statuses')
            ->where('user_id', (int) $validatedData['id'])
            ->where('is_active', true)
            ->limit(1)
            ->update(['is_active' => false]);

        $newStatus = UserStatus::create([
            "user_id" => (int) $validatedData['id'],
            "saved_by" => $authUser->id,
            "is_active" => true,
            "comment" => $validatedData['comment'],
            "status_id" => $validatedData['status_id'],
        ]);

        // Prepare response
        $data = [
            'id' => $newStatus->id,
            'is_active' => true,
            'name' => $validatedData['status'],
            'status_id' => $validatedData['status_id'],
            "comment" => $validatedData['comment'],
            'username' => $authUser->username,
            'created_at' => $newStatus->created_at,
        ];
        DB::commit();

        return response()->json([
            'message' => __('app_translation.success'),
            'status' => $data
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    // Organization
    public function organizationIndex($id)
    {
        $locale = App::getLocale();

        $userStatus = DB::table('organization_statuses as os')
            ->where("os.organization_id", $id)
            ->where('os.is_active', true)
            ->select('os.status_id')
            ->first();
        if ($userStatus->status_id == StatusEnum::block->value) {
            // Start building the query
            $tr = DB::table('status_trans as st')
                ->where('st.status_id', StatusEnum::active->value)
                ->where('st.language_name', $locale)
                ->select(
                    "st.status_id as id",
                    "st.name",
                )
                ->get();
        } else if ($userStatus->status_id == StatusEnum::active->value) {
            $tr = DB::table('status_trans as st')
                ->where('st.status_id', StatusEnum::block->value)
                ->where('st.language_name', $locale)
                ->select(
                    "st.status_id as id",
                    "st.name",
                )
                ->get();
        } else {
            return response()->json(
                [
                    'message' => __('app_translation.org_need_approval')
                ],
                422,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }


        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function storeOrganization(Request $request)
    {
        // Validate request
        $validatedData = $request->validate([
            'organization_id' => 'required|integer',
            'status_id' => 'required|integer',
            'comment' => 'required|string',
        ]);

        $authUser = $request->user();

        // Fetch the currently active status for this NGO
        $previousStatus = OrganizationStatus::where('organization_id', $validatedData['organization_id'])
            ->where('is_active', true)
            ->first();

        // Check if the current active status allows transition
        if (
            $previousStatus &&
            ($previousStatus->status_id === StatusEnum::active->value ||
                $previousStatus->status_id === StatusEnum::block->value)
        ) {
            // Begin transaction
            DB::beginTransaction();
            // Deactivate the old status
            $previousStatus->is_active = false;
            $previousStatus->save();

            // Create a new status entry
            $newStatus = OrganizationStatus::create([
                'status_id' => $validatedData['status_id'],
                'organization_id' => $validatedData['organization_id'],
                'comment' => $validatedData['comment'],
                'is_active' => true,
                'userable_id' => $authUser->id,
                'userable_type' => $this->getModelName(get_class($authUser)),
            ]);

            // Prepare response
            $data = [
                'organization_status_id' => $newStatus->id,
                'is_active' => true,
                'username' => $authUser->username,
                'saved_by' => $newStatus->userable_type,
                'created_at' => $newStatus->created_at,
            ];
            DB::commit();

            return response()->json([
                'message' => __('app_translation.success'),
                'status' => $data
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            // Not authorized to change status
            return response()->json([
                'message' => __('app_translation.unauthorized')
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }
    }
    public function organizationStatuses($id)
    {
        $locale = App::getLocale();
        $result = DB::table('organizations as n')
            ->where('n.id', '=', $id)
            ->join('organization_statuses as ngs', function ($join) {
                $join->on('ngs.organization_id', '=', 'n.id');
                // ->where('ngs.is_active', true);
            })
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('st.status_id', '=', 'ngs.status_id')
                    ->where('st.language_name', $locale);
            })->select(
                'n.id as organization_id',
                'ngs.id',
                'ngs.comment',
                'ngs.status_id',
                'st.name',
                'ngs.userable_type',
                'ngs.is_active',
                'ngs.created_at',
            )
            ->orderByDesc('ngs.id')
            ->get();

        return response()->json([
            'statuses' => $result,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    // Donor
    public function donorIndex($id)
    {
        $locale = App::getLocale();

        $userStatus = DB::table('donor_statuses as os')
            ->where("os.donor_id", $id)
            ->where('os.is_active', true)
            ->select('os.status_id')
            ->first();
        if ($userStatus->status_id == StatusEnum::block->value) {
            // Start building the query
            $tr = DB::table('status_trans as st')
                ->where('st.status_id', StatusEnum::active->value)
                ->where('st.language_name', $locale)
                ->select(
                    "st.status_id as id",
                    "st.name",
                )
                ->get();
        } else if ($userStatus->status_id == StatusEnum::active->value) {
            $tr = DB::table('status_trans as st')
                ->where('st.status_id', StatusEnum::block->value)
                ->where('st.language_name', $locale)
                ->select(
                    "st.status_id as id",
                    "st.name",
                )
                ->get();
        } else {
            return response()->json(
                [
                    'message' => __('app_translation.org_need_approval')
                ],
                422,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }


        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function storeDonor(Request $request)
    {
        // Validate request
        $validatedData = $request->validate([
            'donor_id' => 'required|integer',
            'status_id' => 'required|integer',
            'comment' => 'required|string',
        ]);

        $authUser = $request->user();

        // Fetch the currently active status for this NGO
        $previousStatus = DonorStatus::where('donor_id', $validatedData['donor_id'])
            ->where('is_active', true)
            ->first();

        // Check if the current active status allows transition
        if (
            $previousStatus &&
            ($previousStatus->status_id === StatusEnum::active->value ||
                $previousStatus->status_id === StatusEnum::block->value)
        ) {
            // Begin transaction
            DB::beginTransaction();
            // Deactivate the old status
            $previousStatus->is_active = false;
            $previousStatus->save();

            // Create a new status entry
            $newStatus = DonorStatus::create([
                'status_id' => $validatedData['status_id'],
                'donor_id' => $validatedData['donor_id'],
                'comment' => $validatedData['comment'],
                'is_active' => true,
                'user_id' => $authUser->id,
            ]);

            // Prepare response
            $data = [
                'donor_status_id' => $newStatus->id,
                'is_active' => true,
                'username' => $authUser->username,
                'saved_by' => $newStatus->userable_type,
                'created_at' => $newStatus->created_at,
            ];
            DB::commit();

            return response()->json([
                'message' => __('app_translation.success'),
                'status' => $data
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            // Not authorized to change status
            return response()->json([
                'message' => __('app_translation.unauthorized')
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }
    }
    public function donorStatuses($id)
    {
        $locale = App::getLocale();
        $result = DB::table('donors as don')
            ->where('don.id', '=', $id)
            ->join('donor_statuses as dons', function ($join) {
                $join->on('dons.donor_id', '=', 'don.id');
            })
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('st.status_id', '=', 'dons.status_id')
                    ->where('st.language_name', $locale);
            })
            ->join('users as us', 'dons.user_id', '=', 'us.id')
            ->select(
                'don.id as donor_id',
                'dons.id',
                'dons.comment',
                'dons.status_id',
                'st.name',
                'us.username',
                'dons.is_active',
                'dons.created_at',
            )
            ->orderByDesc('dons.id')
            ->get();

        return response()->json([
            'statuses' => $result,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
