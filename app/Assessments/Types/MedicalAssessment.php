<?php

namespace App\Assessments\Types;

use App\Assessments\AssessmentType;

class MedicalAssessment extends AssessmentType
{
    public function label(): string
    {
        return 'Medical Assessment';
    }

    public function description(): string
    {
        return "Initial medical examination covering general health, "
             . "neurological findings, and medical history relevant to the child's condition.";
    }

    public function assessorCategory(): string
    {
        // Matches the `staff.category` enum used in Module 6
        return 'clinical';
    }

    public function sections(): array
    {
        return [
            'General Health' => [
                'general_condition' => [
                    'label' => 'General condition',
                    'type' => 'select',
                    'options' => [
                        'good' => 'Good',
                        'fair' => 'Fair',
                        'poor' => 'Poor',
                    ],
                    'required' => true,
                ],
                'nutritional_status' => [
                    'label' => 'Nutritional status',
                    'type' => 'select',
                    'options' => [
                        'normal' => 'Normal',
                        'underweight' => 'Underweight',
                        'overweight' => 'Overweight',
                        'obese' => 'Obese',
                    ],
                    'required' => true,
                ],
                'height_cm' => [
                    'label' => 'Height (cm)',
                    'type' => 'number',
                ],
                'weight_kg' => [
                    'label' => 'Weight (kg)',
                    'type' => 'number',
                ],
            ],

            'Neurological' => [
                'consciousness_level' => [
                    'label' => 'Level of consciousness',
                    'type' => 'select',
                    'options' => [
                        'alert' => 'Alert',
                        'drowsy' => 'Drowsy',
                        'unresponsive' => 'Unresponsive',
                    ],
                    'required' => true,
                ],
                'seizure_history' => [
                    'label' => 'Seizure history',
                    'type' => 'select',
                    'options' => [
                        'none' => 'None',
                        'past' => 'Past',
                        'current_controlled' => 'Current, controlled',
                        'current_uncontrolled' => 'Current, uncontrolled',
                    ],
                ],
                'neurological_notes' => [
                    'label' => 'Neurological findings',
                    'type' => 'textarea',
                ],
            ],

            'Medical History' => [
                'past_medical_history' => [
                    'label' => 'Past medical history',
                    'type' => 'textarea',
                ],
                'family_history' => [
                    'label' => 'Relevant family history',
                    'type' => 'textarea',
                ],
                'immunization_status' => [
                    'label' => 'Immunization status',
                    'type' => 'select',
                    'options' => [
                        'up_to_date' => 'Up to date',
                        'partial' => 'Partial',
                        'unknown' => 'Unknown',
                    ],
                ],
            ],

            'Current Concerns' => [
                'presenting_complaints' => [
                    'label' => 'Presenting complaints',
                    'type' => 'textarea',
                    'required' => true,
                ],
                'medical_observations' => [
                    'label' => 'Medical observations',
                    'type' => 'textarea',
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'findings.general_condition' => ['required', 'in:good,fair,poor'],
            'findings.nutritional_status' => ['required', 'in:normal,underweight,overweight,obese'],
            'findings.height_cm' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'findings.weight_kg' => ['nullable', 'numeric', 'min:0', 'max:400'],

            'findings.consciousness_level' => ['required', 'in:alert,drowsy,unresponsive'],
            'findings.seizure_history' => ['nullable', 'in:none,past,current_controlled,current_uncontrolled'],
            'findings.neurological_notes' => ['nullable', 'string', 'max:4000'],

            'findings.past_medical_history' => ['nullable', 'string', 'max:4000'],
            'findings.family_history' => ['nullable', 'string', 'max:4000'],
            'findings.immunization_status' => ['nullable', 'in:up_to_date,partial,unknown'],

            'findings.presenting_complaints' => ['required', 'string', 'max:4000'],
            'findings.medical_observations' => ['nullable', 'string', 'max:4000'],
        ];
    }
}