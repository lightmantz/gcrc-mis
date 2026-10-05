<?php

namespace App\Diagnoses;

class DiagnosisVocabulary
{
    /**
     * Categories group conditions in the dropdown.
     * The order here is the order they appear in the UI.
     */
    public const CATEGORIES = [
        'neurological'       => 'Neurological & Developmental',
        'sensory'            => 'Sensory',
        'motor'              => 'Motor & Musculoskeletal',
        'speech'             => 'Speech & Language',
        'behavioral'         => 'Behavioral & Mental Health',
        'congenital'         => 'Congenital & Genetic',
        'acquired'           => 'Acquired',
        'medical'            => 'General Medical',
        'other'              => 'Other',
    ];

    /**
     * Every condition.
     *
     * Format:
     *   'key' => [
     *       'label'    => 'Display name',
     *       'category' => 'one of the CATEGORIES keys',
     *   ]
     */
    public const CONDITIONS = [
        // ─── Neurological & Developmental ────────────────
        'cerebral_palsy' => [
            'label' => 'Cerebral Palsy',
            'category' => 'neurological',
        ],
        'down_syndrome' => [
            'label' => 'Down Syndrome',
            'category' => 'neurological',
        ],
        'autism_spectrum' => [
            'label' => 'Autism Spectrum Disorder',
            'category' => 'neurological',
        ],
        'intellectual_disability' => [
            'label' => 'Intellectual Disability',
            'category' => 'neurological',
        ],
        'global_developmental_delay' => [
            'label' => 'Global Developmental Delay',
            'category' => 'neurological',
        ],
        'epilepsy' => [
            'label' => 'Epilepsy',
            'category' => 'neurological',
        ],
        'hydrocephalus' => [
            'label' => 'Hydrocephalus',
            'category' => 'neurological',
        ],
        'microcephaly' => [
            'label' => 'Microcephaly',
            'category' => 'neurological',
        ],
        'adhd' => [
            'label' => 'Attention Deficit Hyperactivity Disorder',
            'category' => 'neurological',
        ],
        'learning_disability' => [
            'label' => 'Specific Learning Disability',
            'category' => 'neurological',
        ],

        // ─── Sensory ─────────────────────────────────────
        'hearing_impairment' => [
            'label' => 'Hearing Impairment',
            'category' => 'sensory',
        ],
        'visual_impairment' => [
            'label' => 'Visual Impairment',
            'category' => 'sensory',
        ],
        'deafblindness' => [
            'label' => 'Deafblindness',
            'category' => 'sensory',
        ],

        // ─── Motor & Musculoskeletal ─────────────────────
        'spina_bifida' => [
            'label' => 'Spina Bifida',
            'category' => 'motor',
        ],
        'muscular_dystrophy' => [
            'label' => 'Muscular Dystrophy',
            'category' => 'motor',
        ],
        'spinal_muscular_atrophy' => [
            'label' => 'Spinal Muscular Atrophy',
            'category' => 'motor',
        ],
        'limb_difference' => [
            'label' => 'Congenital Limb Difference',
            'category' => 'motor',
        ],
        'clubfoot' => [
            'label' => 'Clubfoot',
            'category' => 'motor',
        ],
        'scoliosis' => [
            'label' => 'Scoliosis',
            'category' => 'motor',
        ],

        // ─── Speech & Language ───────────────────────────
        'speech_language_disorder' => [
            'label' => 'Speech & Language Disorder',
            'category' => 'speech',
        ],
        'expressive_language_disorder' => [
            'label' => 'Expressive Language Disorder',
            'category' => 'speech',
        ],
        'receptive_language_disorder' => [
            'label' => 'Receptive Language Disorder',
            'category' => 'speech',
        ],
        'stuttering' => [
            'label' => 'Stuttering',
            'category' => 'speech',
        ],
        'articulation_disorder' => [
            'label' => 'Articulation Disorder',
            'category' => 'speech',
        ],

        // ─── Behavioral & Mental Health ──────────────────
        'anxiety_disorder' => [
            'label' => 'Anxiety Disorder',
            'category' => 'behavioral',
        ],
        'depression' => [
            'label' => 'Depression',
            'category' => 'behavioral',
        ],
        'trauma_related' => [
            'label' => 'Trauma- and Stressor-Related Disorder',
            'category' => 'behavioral',
        ],
        'behavioral_disorder' => [
            'label' => 'Behavioral Disorder',
            'category' => 'behavioral',
        ],

        // ─── Congenital & Genetic ────────────────────────
        'fragile_x' => [
            'label' => 'Fragile X Syndrome',
            'category' => 'congenital',
        ],
        'turner_syndrome' => [
            'label' => 'Turner Syndrome',
            'category' => 'congenital',
        ],
        'sickle_cell' => [
            'label' => 'Sickle Cell Disease',
            'category' => 'congenital',
        ],
        'congenital_heart_disease' => [
            'label' => 'Congenital Heart Disease',
            'category' => 'congenital',
        ],

        // ─── Acquired ────────────────────────────────────
        'traumatic_brain_injury' => [
            'label' => 'Traumatic Brain Injury',
            'category' => 'acquired',
        ],
        'post_meningitis' => [
            'label' => 'Post-Meningitis Sequelae',
            'category' => 'acquired',
        ],
        'post_encephalitis' => [
            'label' => 'Post-Encephalitis Sequelae',
            'category' => 'acquired',
        ],
        'polio_sequelae' => [
            'label' => 'Polio Sequelae',
            'category' => 'acquired',
        ],

        // ─── General Medical ─────────────────────────────
        'malnutrition' => [
            'label' => 'Malnutrition',
            'category' => 'medical',
        ],
        'hiv' => [
            'label' => 'HIV',
            'category' => 'medical',
        ],
        'tuberculosis' => [
            'label' => 'Tuberculosis',
            'category' => 'medical',
        ],
        'chronic_asthma' => [
            'label' => 'Chronic Asthma',
            'category' => 'medical',
        ],

        // ─── Other ───────────────────────────────────────
        'other' => [
            'label' => 'Other',
            'category' => 'other',
        ],
    ];

    /**
     * Label for a single key.
     */
    public static function label(string $key): string
    {
        return self::CONDITIONS[$key]['label']
            ?? ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * Category key for a condition.
     */
    public static function category(string $key): ?string
    {
        return self::CONDITIONS[$key]['category'] ?? null;
    }

    /**
     * All conditions, grouped by category, ready for a <select> with <optgroup>.
     *
     * @return array<string, array<string, string>>  category label => [key => label]
     */
    public static function grouped(): array
    {
        $grouped = [];

        foreach (self::CONDITIONS as $key => $definition) {
            $categoryKey = $definition['category'];
            $categoryLabel = self::CATEGORIES[$categoryKey] ?? ucfirst($categoryKey);

            $grouped[$categoryLabel] ??= [];
            $grouped[$categoryLabel][$key] = $definition['label'];
        }

        return $grouped;
    }

    /**
     * Flat map: ['cerebral_palsy' => 'Cerebral Palsy', ...]
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::CONDITIONS as $key => $definition) {
            $options[$key] = $definition['label'];
        }

        return $options;
    }

    /**
     * All keys as a plain array — used in validation `in:` rules.
     */
    public static function keys(): array
    {
        return array_keys(self::CONDITIONS);
    }
}