<?php

namespace App\Support;

class GoalCategories
{
    /**
     * Controlled vocabulary for treatment goal categories.
     *
     * Key => [ label, description (optional, shown as help text), color tone (for badges) ]
     */
    public const CATEGORIES = [
        'motor' => [
            'label' => 'Motor Skills',
            'description' => 'Gross and fine motor function, mobility, strength, coordination.',
            'tone' => 'teal',
        ],
        'self_care' => [
            'label' => 'Self-Care',
            'description' => 'Feeding, dressing, toileting, hygiene, daily living skills.',
            'tone' => 'blue',
        ],
        'communication' => [
            'label' => 'Communication',
            'description' => 'Receptive and expressive language, articulation, AAC.',
            'tone' => 'purple',
        ],
        'cognitive' => [
            'label' => 'Cognitive',
            'description' => 'Attention, memory, problem solving, learning.',
            'tone' => 'cyan',
        ],
        'social' => [
            'label' => 'Social & Emotional',
            'description' => 'Peer interaction, emotional regulation, behavioral goals.',
            'tone' => 'yellow',
        ],
        'education' => [
            'label' => 'Education',
            'description' => 'Academic skills, classroom participation, literacy, numeracy.',
            'tone' => 'green',
        ],
        'vocational' => [
            'label' => 'Vocational',
            'description' => 'Pre-vocational and vocational skills for older children.',
            'tone' => 'orange',
        ],
        'participation' => [
            'label' => 'Participation',
            'description' => 'Community integration, recreational participation, independence.',
            'tone' => 'pink',
        ],
        'medical' => [
            'label' => 'Medical',
            'description' => 'Medication management, seizure control, nutrition, health monitoring.',
            'tone' => 'red',
        ],
        'family' => [
            'label' => 'Family Support',
            'description' => 'Caregiver training, family engagement, home program adherence.',
            'tone' => 'indigo',
        ],
    ];

    public static function label(string $key): string
    {
        return self::CATEGORIES[$key]['label']
            ?? ucfirst(str_replace('_', ' ', $key));
    }

    public static function description(string $key): ?string
    {
        return self::CATEGORIES[$key]['description'] ?? null;
    }

    public static function tone(string $key): ?string
    {
        return self::CATEGORIES[$key]['tone'] ?? null;
    }

    /**
     * @return array<string, string>  key => label, for a select dropdown
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::CATEGORIES as $key => $def) {
            $options[$key] = $def['label'];
        }

        return $options;
    }

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_keys(self::CATEGORIES);
    }
}