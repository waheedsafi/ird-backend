<?php

namespace App\Http\Controllers\v1\app\projects;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\Languages\LanguageEnum;
use App\Http\Requests\v1\project\ProjectDetailsRequest;
use App\Repositories\Organization\OrganizationRepositoryInterface;


class EditProjectController extends Controller
{
    protected $organizationRepository;

    public function __construct(
        OrganizationRepositoryInterface $organizationRepository
    ) {
        $this->organizationRepository = $organizationRepository;
    }
    public function details(ProjectDetailsRequest $request)
    {
        $map = [
            'preamble'           => 'preamble',
            'health_experience'  => 'exper_in_health',
            'goals'              => 'goals',
            'objectives'         => 'objective',
            'expected_outcome'   => 'expected_outcome',
            'expected_impact'    => 'expected_impact',
            'subject'            => 'subject',
            'main_activities'    => 'main_activities',
            'introduction'       => 'project_intro',
            'operational_plan'   => 'action_plan',
            'mission'            => 'mission',
            'vission'            => 'vission',
            'terminologies'      => 'abbreviat',
            'name'               => 'project_name',
            'organization_senior_manangement' => 'organization_sen_man',
            'project_structure'  => 'project_structure',
        ];

        // Build validation rules dynamically

        foreach ($map as $aliasBase) {
            $rules["{$aliasBase}_english"] = 'required|string|min:5';
            $rules["{$aliasBase}_farsi"]   = 'required|string|min:5';
            $rules["{$aliasBase}_pashto"]  = 'required|string|min:5';
        }

        $validated = $request->validated();
        // Loop through languages and update records
        foreach (
            [
                LanguageEnum::default->value => 'english',
                LanguageEnum::farsi->value   => 'farsi',
                LanguageEnum::pashto->value  => 'pashto',
            ] as $langValue => $langName
        ) {

            $updateData = [];
            foreach ($map as $dbColumn => $aliasBase) {
                $updateData[$dbColumn] = $validated["{$aliasBase}_{$langName}"] ?? '';
            }

            DB::table('project_trans')
                ->where('project_id', $validated['id'])
                ->where('language_name', $langValue)
                ->update($updateData);
        }

        return response()->json(['message' => __('app_translation.success')]);
    }

    public function budget(Request $id) {}
}
