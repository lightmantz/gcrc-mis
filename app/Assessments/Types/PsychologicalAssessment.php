<?php

namespace App\Assessments\Types;

use App\Assessments\AssessmentType;

class PsychologicalAssessment extends AssessmentType
{
    public function label(): string
    {
        return 'Psychological / Counseling Assessment';
    }

    public function description(): string
    {
        return 'Cognitive, emotional, and behavioral functioning. Includes '
             . 'family and psychosocial context.';
    }

    public function assessorCategory(): string
    {
        return 'clinical';
    }

    public function sections(): array
    {
        return [
            'Presenting Concerns' => [
                'primary_concern' => [
                    'label' => 'Primary concern',
                    'type' => 'textarea',
                    'required' => true,
                ],
                'concern_duration' => [
                    'label' => 'Duration of concern',
                    'type' => 'select',
                    'options' => [
                        'less_than_1_month' => 'Less than 1 month',
                        '1_to_6_months' => '1–6 months',
                        '6_to_12_months' => '6–12 months',
                        'more_than_1_year' => 'More than 1 year',
                    ],
                ],
            ],

            'Cognitive Functioning' => [
                'attention' => [
                    'label' => 'Attention / concentration',
                    'type' => 'select',
                    'options' => [
                        'age_appropriate' => 'Age-appropriate',
                        'mild_difficulty' => 'Mild difficulty',
                        'moderate_difficulty' => 'Moderate difficulty',
                        'severe_difficulty' => 'Severe difficulty',
                    ],
                ],
                'memory' => [
                    'label' => 'Memory',
                    'type' => 'select',
                    'options' => [
                        'age_appropriate' => 'Age-appropriate',
                        'mild_difficulty' => 'Mild difficulty',
                        'moderate_difficulty' => 'Moderate difficulty',
                        'severe_difficulty' => 'Severe difficulty',
                    ],
                ],
                'problem_solving' => [
                    'label' => 'Problem solving',
                    'type' => 'select',
                    'options' => [
                        'age_appropriate' => 'Age-appropriate',
                        'delayed' => 'Delayed',
                        'significantly_delayed' => 'Significantly delayed',
                    ],
                ],
                'cognitive_notes' => [
                    'label' => 'Cognitive observations',
                    'type' => 'textarea',
                ],
            ],

            'Emotional & Behavioral' => [
                'mood' => [
                    'label' => 'Predominant mood',
                    'type' => 'select',
                    'options' => [
                        'euthymic' => 'Euthymic (balanced)',
                        'anxious' => 'Anxious',
                        'depressed' => 'Depressed',
                        'irritable' => 'Irritable',
                        'labile' => 'Labile (rapidly shifting)',
                    ],
                ],
                'affect' => [
                    'label' => 'Affect',
                    'type' => 'select',
                    'options' => [
                        'appropriate' => 'Appropriate',
                        'flat' => 'Flat',
                        'blunted' => 'Blunted',
                        'restricted' => 'Restricted',
                        'expansive' => 'Expansive',
                    ],
                ],
                'behavioral_concerns' => [
                    'label' => 'Behavioral concerns',
                    'type' => 'textarea',
                    'help' => 'Describe any behavioral challenges observed or reported by caregivers.',
                ],
                'social_interaction' => [
                    'label' => 'Social interaction',
                    'type' => 'select',
                    'options' => [
                        'age_appropriate' => 'Age-appropriate',
                        'withdrawn' => 'Withdrawn',
                        'aggressive' => 'Aggressive',
                        'disinhibited' => 'Disinhibited',
                        'mixed' => 'Mixed',
                    ],
                ],
            ],

            'Family & Social Context' => [
                'family_support' => [
                    'label' => 'Family support',
                    'type' => 'select',
                    'options' => [
                        'strong' => 'Strong',
                        'moderate' => 'Moderate',
                        'limited' => 'Limited',
                        'absent' => 'Absent',
                    ],
                ],
                'significant_stressors' => [
                    'label' => 'Significant stressors',
                    'type' => 'textarea',
                    'help' => 'e.g. family illness, financial hardship, bereavement.',
                ],
            ],

            'Assessment & Plan' => [
                'clinical_impression' => [
                    'label' => 'Clinical impression',
                    'type' => 'textarea',
                    'required' => true,
                ],
                'recommendations' => [
                    'label' => 'Recommendations',
                    'type' => 'textarea',
                ],
                'referral_needed' => [
                    'label' => 'Further referral needed',
                    'type' => 'select',
                    'options' => [
                        'no' => 'No',
                        'psychiatry' => 'Psychiatry',
                        'neurology' => 'Neurology',
                        'other' => 'Other',
                    ],
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'findings.primary_concern' => ['required', 'string', 'max:4000'],
            'findings.concern_duration' => ['nullable', 'in:less_than_1_month,1_to_6_months,6_to_12_months,more_than_1_year'],

            'findings.attention' => ['nullable', 'in:age_appropriate,mild_difficulty,moderate_difficulty,severe_difficulty'],
            'findings.memory' => ['nullable', 'in:age_appropriate,mild_difficulty,moderate_difficulty,severe_difficulty'],
            'findings.problem_solving' => ['nullable', 'in:age_appropriate,delayed,significantly_delayed'],
            'findings.cognitive_notes' => ['nullable', 'string', 'max:4000'],

            'findings.mood' => ['nullable', 'in:euthymic,anxious,depressed,irritable,labile'],
            'findings.affect' => ['nullable', 'in:appropriate,flat,blunted,restricted,expansive'],
            'findings.behavioral_concerns' => ['nullable', 'string', 'max:4000'],
            'findings.social_interaction' => ['nullable', 'in:age_appropriate,withdrawn,aggressive,disinhibited,mixed'],

            'findings.family_support' => ['nullable', 'in:strong,moderate,limited,absent'],
            'findings.significant_stressors' => ['nullable', 'string', 'max:4000'],

            'findings.clinical_impression' => ['required', 'string', 'max:4000'],
            'findings.recommendations' => ['nullable', 'string', 'max:4000'],
            'findings.referral_needed' => ['nullable', 'in:no,psychiatry,neurology,other'],
        ];
    }
}