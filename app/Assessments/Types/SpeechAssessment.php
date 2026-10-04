<?php

namespace App\Assessments\Types;

use App\Assessments\AssessmentType;

class SpeechAssessment extends AssessmentType
{
    public function label(): string
    {
        return 'Speech & Language Assessment';
    }

    public function description(): string
    {
        return 'Receptive and expressive language, articulation, fluency, and '
             . 'communication skills.';
    }

    public function assessorCategory(): string
    {
        return 'therapy';
    }

    public function sections(): array
    {
        return [
            'Receptive Language' => [
                'comprehension' => [
                    'label' => 'Comprehension',
                    'type' => 'select',
                    'options' => [
                        'age_appropriate' => 'Age-appropriate',
                        'mild_delay' => 'Mild delay',
                        'moderate_delay' => 'Moderate delay',
                        'severe_delay' => 'Severe delay',
                    ],
                    'required' => true,
                ],
                'follows_instructions' => [
                    'label' => 'Follows instructions',
                    'type' => 'select',
                    'options' => [
                        'simple' => 'Simple commands',
                        'two_step' => 'Two-step commands',
                        'complex' => 'Complex commands',
                        'none' => 'Does not follow commands',
                    ],
                ],
                'receptive_notes' => [
                    'label' => 'Receptive language observations',
                    'type' => 'textarea',
                ],
            ],

            'Expressive Language' => [
                'vocabulary' => [
                    'label' => 'Vocabulary',
                    'type' => 'select',
                    'options' => [
                        'age_appropriate' => 'Age-appropriate',
                        'limited' => 'Limited',
                        'very_limited' => 'Very limited',
                        'nonverbal' => 'Non-verbal',
                    ],
                    'required' => true,
                ],
                'sentence_formation' => [
                    'label' => 'Sentence formation',
                    'type' => 'select',
                    'options' => [
                        'complex' => 'Complex sentences',
                        'simple' => 'Simple sentences',
                        'phrases' => 'Short phrases',
                        'single_words' => 'Single words only',
                        'nonverbal' => 'Non-verbal',
                    ],
                ],
                'expressive_notes' => [
                    'label' => 'Expressive language observations',
                    'type' => 'textarea',
                ],
            ],

            'Speech Production' => [
                'articulation' => [
                    'label' => 'Articulation',
                    'type' => 'select',
                    'options' => [
                        'clear' => 'Clear',
                        'mild_errors' => 'Mild errors',
                        'moderate_errors' => 'Moderate errors',
                        'severe_errors' => 'Severe errors',
                        'unintelligible' => 'Unintelligible',
                    ],
                ],
                'fluency' => [
                    'label' => 'Fluency',
                    'type' => 'select',
                    'options' => [
                        'fluent' => 'Fluent',
                        'occasional_disfluency' => 'Occasional disfluency',
                        'stuttering' => 'Stuttering',
                    ],
                ],
                'voice_quality' => [
                    'label' => 'Voice quality',
                    'type' => 'select',
                    'options' => [
                        'normal' => 'Normal',
                        'hoarse' => 'Hoarse',
                        'nasal' => 'Hypernasal',
                        'weak' => 'Weak',
                    ],
                ],
                'oral_motor' => [
                    'label' => 'Oral-motor findings',
                    'type' => 'textarea',
                    'help' => 'Describe lip, tongue, and palate function.',
                ],
            ],

            'Communication & AAC' => [
                'primary_mode' => [
                    'label' => 'Primary communication mode',
                    'type' => 'select',
                    'options' => [
                        'verbal' => 'Verbal speech',
                        'signs' => 'Signs / gestures',
                        'aac_device' => 'AAC device',
                        'picture_board' => 'Picture board',
                        'combination' => 'Combination',
                        'nonverbal' => 'Non-verbal',
                    ],
                ],
                'aac_recommended' => [
                    'label' => 'AAC recommended',
                    'type' => 'select',
                    'options' => [
                        'no' => 'No',
                        'consider' => 'Consider',
                        'yes' => 'Yes',
                    ],
                ],
            ],

            'Assessment & Plan' => [
                'clinical_impression' => [
                    'label' => 'Clinical impression',
                    'type' => 'textarea',
                    'required' => true,
                ],
                'therapy_goals' => [
                    'label' => 'Therapy goals',
                    'type' => 'textarea',
                ],
                'recommended_frequency' => [
                    'label' => 'Recommended frequency',
                    'type' => 'select',
                    'options' => [
                        'once_weekly' => 'Once weekly',
                        'twice_weekly' => 'Twice weekly',
                        'thrice_weekly' => 'Three times weekly',
                        'as_needed' => 'As needed',
                    ],
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'findings.comprehension' => ['required', 'in:age_appropriate,mild_delay,moderate_delay,severe_delay'],
            'findings.follows_instructions' => ['nullable', 'in:simple,two_step,complex,none'],
            'findings.receptive_notes' => ['nullable', 'string', 'max:4000'],

            'findings.vocabulary' => ['required', 'in:age_appropriate,limited,very_limited,nonverbal'],
            'findings.sentence_formation' => ['nullable', 'in:complex,simple,phrases,single_words,nonverbal'],
            'findings.expressive_notes' => ['nullable', 'string', 'max:4000'],

            'findings.articulation' => ['nullable', 'in:clear,mild_errors,moderate_errors,severe_errors,unintelligible'],
            'findings.fluency' => ['nullable', 'in:fluent,occasional_disfluency,stuttering'],
            'findings.voice_quality' => ['nullable', 'in:normal,hoarse,nasal,weak'],
            'findings.oral_motor' => ['nullable', 'string', 'max:4000'],

            'findings.primary_mode' => ['nullable', 'in:verbal,signs,aac_device,picture_board,combination,nonverbal'],
            'findings.aac_recommended' => ['nullable', 'in:no,consider,yes'],

            'findings.clinical_impression' => ['required', 'string', 'max:4000'],
            'findings.therapy_goals' => ['nullable', 'string', 'max:4000'],
            'findings.recommended_frequency' => ['nullable', 'in:once_weekly,twice_weekly,thrice_weekly,as_needed'],
        ];
    }
}