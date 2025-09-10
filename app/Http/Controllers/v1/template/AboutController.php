<?php

namespace App\Http\Controllers\v1\template;

use App\Models\AboutStaff;
use Illuminate\Http\Request;
use App\Models\AboutStaffTrans;
use App\Traits\FileHelperTrait;
use App\Traits\PathHelperTrait;
use App\Models\OfficeInformation;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\AboutStaffEnum;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\Languages\LanguageEnum;
use App\Models\OfficeInformationTrans;
use App\Http\Requests\v1\about\StaffStoreRequest;
use App\Http\Requests\v1\about\OfficeStoreRequest;
use App\Http\Requests\v1\about\StaffUpdateRequest;
use App\Http\Requests\v1\about\OfficeUpdateRequest;

class AboutController extends Controller
{
    use FileHelperTrait, PathHelperTrait;

    public function staffs()
    {
        $locale = App::getLocale();
        $query  = DB::table('about_staff as as')
            ->join('about_staff_trans as ast', function ($join) use ($locale) {
                $join->on('ast.about_staff_id', '=', 'as.id')
                    ->where('ast.language_name', '=', $locale);
            })
            ->join('about_staff_type_trans as astt', function ($join) use ($locale) {
                $join->on('astt.about_staff_type_id', '=', 'as.about_staff_type_id')
                    ->where('astt.language_name', '=', $locale);
            })
            ->select(
                'as.id',
                'as.contact',
                'as.email',
                'as.profile as picture',
                'ast.name',
                'astt.value as job'
            )
            ->get();
        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function manager()
    {
        return response()->json(
            $this->staffByType(AboutStaffEnum::manager->value, true),
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function technicalSupport()
    {
        return response()->json(
            $this->staffByType(AboutStaffEnum::technical_support->value),
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    // Director
    public function director()
    {

        return response()->json(
            $this->staffByType(AboutStaffEnum::director->value, true),
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function storeTechnicalSupport(StaffStoreRequest $request)
    {
        return $this->store($request, "images/technical/", 'picture', AboutStaffEnum::technical_support->value);
    }
    public function storeDirector(StaffStoreRequest $request)
    {
        return $this->store($request, "images/technical/", 'picture', AboutStaffEnum::director->value);
    }
    public function storeManager(StaffStoreRequest $request)
    {
        return $this->store($request, "images/technical/", AboutStaffEnum::manager->value);
    }

    public function updateTechnicalSupport(StaffUpdateRequest $request)
    {
        return $this->update($request, "images/technical/");
    }
    public function updateDirector(StaffUpdateRequest $request)
    {
        return $this->update($request, "images/technical/");
    }
    public function updateManager(StaffUpdateRequest $request)
    {
        return $this->update($request, "images/technical/");
    }
    public function update(StaffUpdateRequest $request, $path)
    {
        // Begin transaction
        DB::beginTransaction();
        // Find the staff entry by ID
        $staff = AboutStaff::find($request->id);
        if (!$staff) {
            return response()->json([
                'message' => __('app_translation.staff_not_found'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        $profile = $staff->profile;
        if ($profile !== $request->picture) {
            // Update profile
            $this->deleteDocument($this->transformToPublic($profile));

            // 1.2 update document
            $document = $this->storePublicDocument($request, $path, 'picture');
            if (!$document) {
                return response()->json([
                    'message' => __('app_translation.failed'),
                    'path' => $document,
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            $staff->profile =  $document['path'];
        }

        // Update Staff details
        $staff->contact = $request->contact;
        $staff->email = $request->email;
        $staff->save();

        // Update or create translations
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            AboutStaffTrans::updateOrCreate(
                [
                    'about_staff_id' => $staff->id,
                    'language_name' => $code,
                ],
                [
                    'name' => $request["name_{$name}"],
                ]
            );
        }
        // Commit transaction
        DB::commit();

        return response()->json([
            'message' => __('app_translation.success'),
            'staff' => [
                "id" => $staff->id,
                "name_english" => $request->name_english,
                "name_pashto" => $request->name_pashto,
                "name_farsi" => $request->name_farsi,
                "contact" => $request->contact,
                "email" => $request->email,
                "picture" => $staff->profile,
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store(StaffStoreRequest $request, $path, $staffType)
    {
        // Validate the request
        $validateData = $request->validated(); // Use validated() for already validated data
        // Begin transaction
        DB::beginTransaction();
        // Store the profile
        $document = $this->storePublicDocument($request, $path, 'picture');
        if (!$document) {
            return response()->json([
                'message' => __('app_translation.failed'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }

        // Store Staff data
        $staff = AboutStaff::create([
            'contact' => $validateData['contact'],
            'email' => $validateData['email'],
            'about_staff_type_id' =>  $staffType,
            'profile' => $document['path'],
        ]);

        // Handle translation insertion
        AboutStaffTrans::create([
            'language_name' => LanguageEnum::default->value,
            'about_staff_id' => $staff->id,
            'name' => $validateData["name_english"],
        ]);
        AboutStaffTrans::create([
            'language_name' => LanguageEnum::farsi->value,
            'about_staff_id' => $staff->id,
            'name' => $validateData["name_farsi"],
        ]);
        AboutStaffTrans::create([
            'language_name' => LanguageEnum::pashto->value,
            'about_staff_id' => $staff->id,
            'name' => $validateData["name_pashto"],
        ]);
        DB::commit();

        return response()->json([
            'message' => __('app_translation.success'),
            'staff' => [
                "id" => $staff->id,
                "name_english" => $validateData['name_english'],
                "name_pashto" => $validateData['name_pashto'],
                "name_farsi" => $validateData['name_farsi'],
                "contact" => $validateData['contact'],
                "email" => $validateData['email'],
                "picture" => $document['path'],
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function staffByType($type, $selectOne = false)
    {
        $query = DB::table('about_staff as as')
            ->where('as.about_staff_type_id', $type)
            ->join('about_staff_trans as ast', 'ast.about_staff_id', '=', 'as.id')
            ->select(
                'as.id',
                'as.about_staff_type_id',
                'as.contact',
                'as.email',
                'as.profile as picture',
                'as.created_at',
                'as.updated_at',
                DB::raw("MAX(CASE WHEN ast.language_name = 'en' THEN ast.name END) as name_english"),
                DB::raw("MAX(CASE WHEN ast.language_name = 'fa' THEN ast.name END) as name_farsi"),
                DB::raw("MAX(CASE WHEN ast.language_name = 'ps' THEN ast.name END) as name_pashto")
            )
            ->groupBy('as.id', 'as.about_staff_type_id', 'as.contact', 'as.email', 'as.profile', 'as.created_at', 'as.updated_at');
        if ($selectOne) {
            return $query->first();
        } else {
            return $query->get();
        }
    }
    // Office
    public function storeOffice(OfficeStoreRequest $request)
    {
        // Validate the request
        $validateData = $request->validated();
        // Begin transaction
        DB::beginTransaction();

        // Store Staff data
        $office = OfficeInformation::create([
            'contact' => $validateData['contact'],
            'email' => $validateData['email'],
            'address_english' => $validateData['address_english'],
            'address_farsi' => $validateData['address_farsi'],
            'address_pashto' => $validateData['address_pashto'],
        ]);

        DB::commit();
        return response()->json([
            'message' => __('app_translation.success'),
            'office' => [
                "id" => $office->id,
                "address_english" => $validateData['address_english'],
                "address_farsi" => $validateData['address_farsi'],
                "address_pashto" => $validateData['address_pashto'],
                "contact" => $validateData['contact'],
                "email" => $validateData['email'],
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function updateOffice(OfficeUpdateRequest $request)
    {
        // Validate the request
        $validateData = $request->validated(); // Use validated() for already validated data
        // Begin transaction
        DB::beginTransaction();
        // Find the staff entry by ID
        $office = OfficeInformation::find($validateData['id']);
        if (!$office) {
            return response()->json([
                'message' => __('app_translation.not_found'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        // Update Staff details
        $office->contact = $validateData['contact'];
        $office->email = $validateData['email'];
        $office->save();

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            OfficeInformationTrans::updateOrCreate(
                [
                    'office_information_id' => $office->id,
                    'language_name' => $code,
                ],
                [
                    'value' => $request["address_{$name}"],
                ]
            );
        }

        // Commit transaction
        DB::commit();

        return response()->json([
            'message' => __('app_translation.success'),
            'office' => [
                "id" => $office->id,
                "name_english" => $validateData['address_english'],
                "name_pashto" => $validateData['address_pashto'],
                "address_farsi" => $validateData['address_farsi'],
                "contact" => $validateData['contact'],
                "email" => $validateData['email'],
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function office()
    {
        $locale = App::getLocale();
        $office  =  DB::table('office_information as oi')
            ->join('office_information_trans as oit', function ($join) use (&$locale) {
                $join->on('oit.office_information_id', '=', 'oi.id')
                    ->where('oit.language_name', $locale);
            })
            ->select(
                'oi.contact',
                'oi.email',
                'oit.value as address'
            )
            ->first();

        return response()->json(
            $office,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function editOffice()
    {
        $office = DB::table('office_information as oi')
            ->join('office_information_trans as oit', 'oit.office_information_id', '=', 'oi.id')
            ->select(
                'oi.id',
                'oi.contact',
                'oi.email',
                DB::raw("MAX(CASE WHEN oit.language_name = 'en' THEN oit.value END) as address_english"),
                DB::raw("MAX(CASE WHEN oit.language_name = 'fa' THEN oit.value END) as address_farsi"),
                DB::raw("MAX(CASE WHEN oit.language_name = 'ps' THEN oit.value END) as address_pashto")
            )
            ->groupBy('oi.id', 'oi.contact', 'oi.email')
            ->first();

        return response()->json([
            "office" => $office,

        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
