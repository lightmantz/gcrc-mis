<?php

namespace App\Assessments\Types;

use App\Assessments\AssessmentType;

class OccupationalTherapyAssessment extends AssessmentType
{
    public function label(): string
    {
        return 'Occupational Therapy Assessment';
    }

    public function description(): string
    {
        return 'Activities of daily living, fine motor skills, sensory processing, '
             . 'and functional independence.';
    }

    public function assessorCategory(): string
    {
        return 'therapy';
    }

    public function sections(): array
    {
        return [
            'Activities of Daily Living' => [
                'feeding' => [
                    'label' => 'Feeding',
                    'type' => 'select',
                    'options' => [
                        'independent' => 'Independent',
                        'supervision' => 'Requires supervision',
                        'assistance' => 'Requires assistance',
                        'dependent' => 'Fully dependent',
                    ],
                    'required' => true,
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
                'toileting' => [
                    'label' => 'Toileting',
                    'type' => 'select',
                    'options' => [
                        'independent' => 'Independent',
                        'supervision' => 'Requires supervision',
                        'assistance' => 'Requires assistance',
                        'dependent' => 'Fully dependent',
                    ],
                ],
                'personal_hygiene' => [
                    'label' => 'Personal hygiene',
                    'type' => 'select',
                    'options' => [
                        'independent' => 'Independent',
                        'supervision' => 'Requires supervision',
                        'assistance' => 'Requires assistance',
                        'dependent' => 'Fully dependent',
                    ],
                ],
            ],

            'Fine Motor & Dexterity' => [
                'pincer_grasp' => [
                    'label' => 'Pincer grasp',
                    'type' => 'select',
                    'options' => [
                        'present' => 'Present',
                        'emerging' => 'Emerging',
                        'absent' => 'Absent',
                    ],
                ],
                'handwriting' => [
                    'label' => 'Handwriting',
                    'type' => 'select',
                    'options' => [
                        'age_appropriate' => 'Age-appropriate',
                        'emerging' => 'Emerging',
                        'not_yet' => 'Not yet developed',
                        'not_applicable' => 'Not applicable',
                    ],
                ],
                'bilateral_coordination' => [
                    'label' => 'Bilateral coordination',
                    'type' => 'select',
                    'options' => [
                        'good' => 'Good',
                        'fair' => 'Fair',
                        'poor' => 'Poor',
                    ],
                ],
            ],

            'Sensory Processing' => [
                'tactile_response' => [
                    'label' => 'Tactile response',
                    'type' => 'select',
                    'options' => [
                        'typical' => 'Typical',
                        'hypersensitive' => 'Hypersensitive',
                        'hyposensitive' => 'Hyposensitive',
                        'mixed' => 'Mixed',
                    ],
                ],
                'auditory_response' => [
                    'label' => 'Auditory response',
                    'type' => 'select',
                    'options' => [
                        'typical' => 'Typical',
                        'hypersensitive' => 'Hypersensitive',
                        'hyposensitive' => 'Hyposensitive',
                    ],
                ],
                'vestibular_response' => [
                    'label' => 'Vestibular response',
                    'type' => 'select',
                    'options' => [
                        'typical' => 'Typical',
                        'seeking' => 'Sensory seeking',
                        'avoiding' => 'Sensory avoiding',
                    ],
                ],
                'sensory_notes' => [
                    'label' => 'Additional sensory observations',
                    'type' => 'textarea',
                ],
            ],

            'Assessment & Plan' => [
                'clinical_impression' => [
                    'label' => 'Clinical impression',
                    'type' => 'textarea',
                    'required' => true,
                ],
                'functional_goals' => [
                    'label' => 'Functional goals',
                    'type' => 'textarea',
                ],
                'recommended_equipment' => [
                    'label' => 'Recommended equipment / adaptations',
                    'type' => 'textarea',
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'findings.feeding' => ['required', 'in:independent,supervision,assistance,dependent'],
            'findings.dressing' => ['nullable', 'in:independent,supervision,assistance,dependent'],
            'findings.toileting' => ['nullable', 'in:independent,supervision,assistance,dependent'],
            'findings.personal_hygiene' => ['nullable', 'in:independent,supervision,assistance,dependent'],

            'findings.pincer_grasp' => ['nullable', 'in:present,emerging,absent'],
            'findings.handwriting' => ['nullable', 'in:age_appropriate,emerging,not_yet,not_applicable'],
            'findings.bilateral_coordination' => ['nullable', 'in:good,fair,poor'],

            'findings.tactile_response' => ['nullable', 'in:typical,hypersensitive,hyposensitive,mixed'],
            'findings.auditory_response' => ['nullable', 'in:typical,hypersensitive,hyposensitive'],
            'findings.vestibular_response' => ['nullable', 'in:typical,seeking,avoiding'],
            'findings.sensory_notes' => ['nullable', 'string', 'max:4000'],

            'findings.clinical_impression' => ['required', 'string', 'max:4000'],
            'findings.functional_goals' => ['nullable', 'string', 'max:4000'],
            'findings.recommended_equipment' => ['nullable', 'string', 'max:4000'],
        ];
    }
}