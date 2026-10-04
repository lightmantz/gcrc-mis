<?php

namespace App\Assessments\Types;

use App\Assessments\AssessmentType;

class FunctionalAssessment extends AssessmentType
{
    public function label(): string
    {
        return 'Functional Assessment';
    }

    public function description(): string
    {
        return 'Overall functional independence across daily activities, '
             . 'adaptive behavior, and participation.';
    }

    public function assessorCategory(): string
    {
        return 'therapy';
    }

    public function sections(): array
    {
        return [
            'Mobility & Movement' => [
                'transfers' => [
                    'label' => 'Transfers',
                    'type' => 'select',
                    'options' => [
                        'independent' => 'Independent',
                        'supervision' => 'Requires supervision',
                        'assistance' => 'Requires physical assistance',
                        'dependent' => 'Fully dependent',
                    ],
                ],
                'community_mobility' => [
                    'label' => 'Community mobility',
                    'type' => 'select',
                    'options' => [
                        'independent' => 'Independent',
                        'with_device' => 'With assistive device',
                        'with_person' => 'With a person',
                        'restricted' => 'Restricted to home',
                    ],
                ],
                'stairs' => [
                    'label' => 'Stairs',
                    'type' => 'select',
                    'options' => [
                        'independent' => 'Independent',
                        'with_rail' => 'Requires rail',
                        'with_assistance' => 'Requires assistance',
                        'unable' => 'Unable',
                    ],
                ],
            ],

            'Self-Care' => [
                'bathing' => [
                    'label' => 'Bathing',
                    'type' => 'select',
                    'options' => [
                        'independent' => 'Independent',
                        'supervision' => 'Requires supervision',
                        'assistance' => 'Requires assistance',
                        'dependent' => 'Fully dependent',
                    ],
                ],
                'dressing' => [
                    'label' => 'Dressing',
                    'type' => 'select',
                    'options' => [
                        'independent' => 'Independent',
                        'supervision' => 'Requires supervision',
                        'assistance' => 'Requires assistance',
                        'dependent' => 'Fully dependent',
                    ],
                ],
                'eating' => [
                    'label' => 'Eating',
                    'type' => 'select',
                    'options' => [
                        'independent' => 'Independent',
                        'supervision' => 'Requires supervision',
                        'assistance' => 'Requires assistance',
                        'dependent' => 'Fully dependent',
                        'tube_fed' => 'Tube-fed',
                    ],
                ],
                'continence' => [
                    'label' => 'Continence',
                    'type' => 'select',
                    'options' => [
                        'independent' => 'Independent',
                        'occasional_accidents' => 'Occasional accidents',
                        'regular_accidents' => 'Regular accidents',
                        'incontinent' => 'Incontinent',
                        'catheter' => 'Catheterized',
                    ],
                ],
            ],

            'Communication & Cognition' => [
                'expresses_needs' => [
                    'label' => 'Expresses needs',
                    'type' => 'select',
                    'options' => [
                        'verbally' => 'Verbally',
                        'signs' => 'Signs / gestures',
                        'aac' => 'AAC device',
                        'behavior' => 'Through behavior',
                        'not_effectively' => 'Not effectively',
                    ],
                ],
                'follows_routines' => [
                    'label' => 'Follows routines',
                    'type' => 'select',
                    'options' => [
                        'fully' => 'Fully',
                        'with_prompts' => 'With prompts',
                        'with_assistance' => 'With assistance',
                        'unable' => 'Unable',
                    ],
                ],
                'safety_awareness' => [
                    'label' => 'Safety awareness',
                    'type' => 'select',
                    'options' => [
                        'age_appropriate' => 'Age-appropriate',
                        'reduced' => 'Reduced',
                        'significantly_impaired' => 'Significantly impaired',
                        'absent' => 'Absent',
                    ],
                ],
            ],

            'Participation' => [
                'school_participation' => [
                    'label' => 'School participation',
                    'type' => 'select',
                    'options' => [
                        'full' => 'Full participation',
                        'partial' => 'Partial participation',
                        'supported' => 'With support',
                        'not_attending' => 'Not attending',
                        'not_applicable' => 'Not applicable (under school age)',
                    ],
                ],
                'social_participation' => [
                    'label' => 'Social participation',
                    'type' => 'select',
                    'options' => [
                        'active' => 'Active',
                        'limited' => 'Limited',
                        'passive' => 'Passive',
                        'isolated' => 'Isolated',
                    ],
                ],
            ],

            'Assessment & Plan' => [
                'summary_of_function' => [
                    'label' => 'Summary of functional status',
                    'type' => 'textarea',
                    'required' => true,
                ],
                'priority_needs' => [
                    'label' => 'Priority needs',
                    'type' => 'textarea',
                ],
                'intervention_priorities' => [
                    'label' => 'Intervention priorities',
                    'type' => 'textarea',
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'findings.transfers' => ['nullable', 'in:independent,supervision,assistance,dependent'],
            'findings.community_mobility' => ['nullable', 'in:independent,with_device,with_person,restricted'],
            'findings.stairs' => ['nullable', 'in:independent,with_rail,with_assistance,unable'],

            'findings.bathing' => ['nullable', 'in:independent,supervision,assistance,dependent'],
            'findings.dressing' => ['nullable', 'in:independent,supervision,assistance,dependent'],
            'findings.eating' => ['nullable', 'in:independent,supervision,assistance,dependent,tube_fed'],
            'findings.continence' => ['nullable', 'in:independent,occasional_accidents,regular_accidents,incontinent,catheter'],

            'findings.expresses_needs' => ['nullable', 'in:verbally,signs,aac,behavior,not_effectively'],
            'findings.follows_routines' => ['nullable', 'in:fully,with_prompts,with_assistance,unable'],
            'findings.safety_awareness' => ['nullable', 'in:age_appropriate,reduced,significantly_impaired,absent'],

            'findings.school_participation' => ['nullable', 'in:full,partial,supported,not_attending,not_applicable'],
            'findings.social_participation' => ['nullable', 'in:active,limited,passive,isolated'],

            'findings.summary_of_function' => ['required', 'string', 'max:4000'],
            'findings.priority_needs' => ['nullable', 'string', 'max:4000'],
            'findings.intervention_priorities' => ['nullable', 'string', 'max:4000'],
        ];
    }
}