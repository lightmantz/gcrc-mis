<?php

namespace App\Assessments;

use App\Assessments\Types\MedicalAssessment;

class AssessmentTypeRegistry
{
    /**
     * Registered assessment types.
     * Add new classes here to make them available system-wide.
     *
     * @var array<class-string<AssessmentType>>
     */
    private const TYPES = [
        MedicalAssessment::class,
        // PhysiotherapyAssessment::class,  // Module 8b
        // SpeechAssessment::class,         // Module 8b
    ];

    /**
     * @return array<string, AssessmentType>  keyed by type key
     */
    public static function all(): array
    {
        $types = [];

        foreach (self::TYPES as $class) {
            $instance = new $class();
            $types[$class::key()] = $instance;
        }

        return $types;
    }

    public static function get(string $key): ?AssessmentType
    {
        return self::all()[$key] ?? null;
    }

    public static function has(string $key): bool
    {
        return self::get($key) !== null;
    }

    /**
     * Type keys for validation rules and dropdowns.
     *
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_keys(self::all());
    }

    /**
     * For select dropdowns: ['medical' => 'Medical Assessment', ...]
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::all() as $key => $type) {
            $options[$key] = $type->label();
        }

        return $options;
    }
}