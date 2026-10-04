<?php

namespace App\Assessments\Types;

use App\Assessments\AssessmentType;

class SocialAssessment extends AssessmentType
{
    public function label(): string
    {
        return 'Social Assessment';
    }

    public function description(): string
    {
        return 'Family context, housing, economic situation, and community '
             . 'support network. Typically performed by a social worker.';
    }

    public function assessorCategory(): string
    {
        return 'admin';
    }

    public function sections(): array
    {
        return [
            'Household' => [
                'household_size' => [
                    'label' => 'Household size',
                    'type' => 'number',
                ],
                'primary_caregiver' => [
                    'label' => 'Primary caregiver',
                    'type' => 'text',
                ],
                'caregiver_relationship' => [
                    'label' => 'Caregiver relationship to child',
                    'type' => 'text',
                ],
                'household_notes' => [
                    'label' => 'Household composition',
                    'type' => 'textarea',
                ],
            ],

            'Housing & Living Conditions' => [
                'housing_type' => [
                    'label' => 'Housing type',
                    'type' => 'select',
                    'options' => [
                        'own_home' => 'Own home',
                        'rented' => 'Rented',
                        'family_home' => 'Living with extended family',
                        'institutional' => 'Institutional / care facility',
                        'shelter' => 'Shelter / temporary',
                        'other' => 'Other',
                    ],
                ],
                'housing_condition' => [
                    'label' => 'Housing condition',
                    'type' => 'select',
                    'options' => [
                        'adequate' => 'Adequate',
                        'crowded' => 'Crowded',
                        'unsafe' => 'Unsafe',
                        'inadequate' => 'Inadequate',
                    ],
                ],
                'utilities' => [
                    'label' => 'Access to utilities',
                    'type' => 'select',
                    'options' => [
                        'full' => 'Electricity, water, sanitation',
                        'partial' => 'Partial access',
                        'limited' => 'Limited access',
                        'none' => 'None',
                    ],
                ],
                'distance_to_center' => [
                    'label' => 'Distance to center',
                    'type' => 'select',
                    'options' => [
                        'under_5km' => 'Under 5 km',
                        '5_to_15km' => '5–15 km',
                        '15_to_30km' => '15–30 km',
                        'over_30km' => 'Over 30 km',
                    ],
                    'help' => 'Travel time is a common barrier to attendance.',
                ],
            ],

            'Economic Situation' => [
                'income_source' => [
                    'label' => 'Primary income source',
                    'type' => 'text',
                ],
                'financial_stability' => [
                    'label' => 'Financial stability',
                    'type' => 'select',
                    'options' => [
                        'stable' => 'Stable',
                        'variable' => 'Variable',
                        'precarious' => 'Precarious',
                        'destitute' => 'Destitute',
                    ],
                ],
                'support_received' => [
                    'label' => 'External support received',
                    'type' => 'textarea',
                    'help' => 'Government grants, NGO assistance, community aid.',
                ],
            ],

            'Support Network' => [
                'family_support_level' => [
                    'label' => 'Extended family support',
                    'type' => 'select',
                    'options' => [
                        'strong' => 'Strong',
                        'moderate' => 'Moderate',
                        'limited' => 'Limited',
                        'none' => 'None',
                    ],
                ],
                'community_support' => [
                    'label' => 'Community support',
                    'type' => 'textarea',
                ],
                'child_protection_concerns' => [
                    'label' => 'Child protection concerns',
                    'type' => 'select',
                    'options' => [
                        'none' => 'None',
                        'monitoring' => 'Under monitoring',
                        'reported' => 'Reported to authorities',
                    ],
                ],
            ],

            'Assessment & Plan' => [
                'social_worker_impression' => [
                    'label' => 'Social worker impression',
                    'type' => 'textarea',
                    'required' => true,
                ],
                'intervention_plan' => [
                    'label' => 'Intervention plan',
                    'type' => 'textarea',
                ],
                'referral_needed' => [
                    'label' => 'Further referral needed',
                    'type' => 'select',
                    'options' => [
                        'no' => 'No',
                        'protection_services' => 'Child protection services',
                        'ngo' => 'NGO / aid organization',
                        'other' => 'Other',
                    ],
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'findings.household_size' => ['nullable', 'integer', 'min:1', 'max:50'],
            'findings.primary_caregiver' => ['nullable', 'string', 'max:200'],
            'findings.caregiver_relationship' => ['nullable', 'string', 'max:120'],
            'findings.household_notes' => ['nullable', 'string', 'max:4000'],

            'findings.housing_type' => ['nullable', 'in:own_home,rented,family_home,institutional,shelter,other'],
            'findings.housing_condition' => ['nullable', 'in:adequate,crowded,unsafe,inadequate'],
            'findings.utilities' => ['nullable', 'in:full,partial,limited,none'],
            'findings.distance_to_center' => ['nullable', 'in:under_5km,5_to_15km,15_to_30km,over_30km'],

            'findings.income_source' => ['nullable', 'string', 'max:200'],
            'findings.financial_stability' => ['nullable', 'in:stable,variable,precarious,destitute'],
            'findings.support_received' => ['nullable', 'string', 'max:4000'],

            'findings.family_support_level' => ['nullable', 'in:strong,moderate,limited,none'],
            'findings.community_support' => ['nullable', 'string', 'max:4000'],
            'findings.child_protection_concerns' => ['nullable', 'in:none,monitoring,reported'],

            'findings.social_worker_impression' => ['required', 'string', 'max:4000'],
            'findings.intervention_plan' => ['nullable', 'string', 'max:4000'],
            'findings.referral_needed' => ['nullable', 'in:no,protection_services,ngo,other'],
        ];
    }
}