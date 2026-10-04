<?php

namespace App\Assessments\Types;

use App\Assessments\AssessmentType;

class PhysiotherapyAssessment extends AssessmentType
{
    public function label(): string
    {
        return 'Physiotherapy Assessment';
    }

    public function description(): string
    {
        return 'Motor function, muscle tone, range of motion, gait, and mobility. '
             . 'Performed by a physiotherapist.';
    }

    public function assessorCategory(): string
    {
        return 'therapy';
    }

    public function sections(): array
    {
        return [
            'Gross Motor Function' => [
                'head_control' => [
                    'label' => 'Head control',
                    'type' => 'select',
                    'options' => [
                        'independent' => 'Independent',
                        'partial' => 'Partial',
                        'absent' => 'Absent',
                    ],
                    'required' => true,
                ],
                'sitting_balance' => [
                    'label' => 'Sitting balance',
                    'type' => 'select',
                    'options' => [
                        'independent' => 'Independent',
                        'with_support' => 'With support',
                        'unable' => 'Unable',
                    ],
                    'required' => true,
                ],
                'standing_balance' => [
                    'label' => 'Standing balance',
                    'type' => 'select',
                    'options' => [
                        'independent' => 'Independent',
                        'with_support' => 'With support',
                        'unable' => 'Unable',
                    ],
                ],
                'walking' => [
                    'label' => 'Walking',
                    'type' => 'select',
                    'options' => [
                        'independent' => 'Independent',
                        'with_assistance' => 'With assistance',
                        'with_device' => 'With assistive device',
                        'non_ambulatory' => 'Non-ambulatory',
                    ],
                ],
            ],

            'Fine Motor Function' => [
                'hand_function_right' => [
                    'label' => 'Hand function — right',
                    'type' => 'select',
                    'options' => [
                        'functional' => 'Functional',
                        'partial' => 'Partial',
                        'non_functional' => 'Non-functional',
                    ],
                ],
                'hand_function_left' => [
                    'label' => 'Hand function — left',
                    'type' => 'select',
                    'options' => [
                        'functional' => 'Functional',
                        'partial' => 'Partial',
                        'non_functional' => 'Non-functional',
                    ],
                ],
                'grasp_pattern' => [
                    'label' => 'Grasp pattern',
                    'type' => 'select',
                    'options' => [
                        'normal' => 'Normal',
                        'immature' => 'Immature',
                        'absent' => 'Absent',
                    ],
                ],
            ],

            'Muscle Tone & Range' => [
                'muscle_tone' => [
                    'label' => 'Muscle tone',
                    'type' => 'select',
                    'options' => [
                        'normal' => 'Normal',
                        'hypotonic' => 'Hypotonic (low)',
                        'hypertonic' => 'Hypertonic (high)',
                        'mixed' => 'Mixed',
                        'fluctuating' => 'Fluctuating',
                    ],
                    'required' => true,
                ],
                'range_of_motion' => [
                    'label' => 'Range of motion',
                    'type' => 'select',
                    'options' => [
                        'full' => 'Full',
                        'mild_restriction' => 'Mild restriction',
                        'moderate_restriction' => 'Moderate restriction',
                        'severe_restriction' => 'Severe restriction',
                    ],
                ],
                'contractures' => [
                    'label' => 'Contractures',
                    'type' => 'textarea',
                    'help' => 'Describe any joint contractures and their location.',
                ],
            ],

            'Assessment & Plan' => [
                'clinical_impression' => [
                    'label' => 'Clinical impression',
                    'type' => 'textarea',
                    'required' => true,
                ],
                'treatment_goals' => [
                    'label' => 'Proposed treatment goals',
                    'type' => 'textarea',
                ],
                'recommended_frequency' => [
                    'label' => 'Recommended frequency',
                    'type' => 'select',
                    'options' => [
                        'once_weekly' => 'Once weekly',
                        'twice_weekly' => 'Twice weekly',
                        'thrice_weekly' => 'Three times weekly',
                        'daily' => 'Daily',
                        'as_needed' => 'As needed',
                    ],
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'findings.head_control' => ['required', 'in:independent,partial,absent'],
            'findings.sitting_balance' => ['required', 'in:independent,with_support,unable'],
            'findings.standing_balance' => ['nullable', 'in:independent,with_support,unable'],
            'findings.walking' => ['nullable', 'in:independent,with_assistance,with_device,non_ambulatory'],

            'findings.hand_function_right' => ['nullable', 'in:functional,partial,non_functional'],
            'findings.hand_function_left' => ['nullable', 'in:functional,partial,non_functional'],
            'findings.grasp_pattern' => ['nullable', 'in:normal,immature,absent'],

            'findings.muscle_tone' => ['required', 'in:normal,hypotonic,hypertonic,mixed,fluctuating'],
            'findings.range_of_motion' => ['nullable', 'in:full,mild_restriction,moderate_restriction,severe_restriction'],
            'findings.contractures' => ['nullable', 'string', 'max:4000'],

            'findings.clinical_impression' => ['required', 'string', 'max:4000'],
            'findings.treatment_goals' => ['nullable', 'string', 'max:4000'],
            'findings.recommended_frequency' => ['nullable', 'in:once_weekly,twice_weekly,thrice_weekly,daily,as_needed'],
        ];
    }
}