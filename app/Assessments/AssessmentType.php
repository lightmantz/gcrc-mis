<?php

namespace App\Assessments;

abstract class AssessmentType
{
    /**
     * The key used in the database and route URLs (e.g. "medical").
     * Default: the class basename lowercased, minus the "Assessment" suffix.
     */
    public static function key(): string
    {
        $class = class_basename(static::class);

        return strtolower(str_replace('Assessment', '', $class));
    }

    /**
     * Human-readable name for menus and headings.
     */
    abstract public function label(): string;

    /**
     * Short description shown on the assessment index and create pages.
     */
    public function description(): string
    {
        return '';
    }

    /**
     * The category of staff expected to perform this assessment.
     * Used for authorization: a physiotherapist can edit another
     * physiotherapist's draft, but a nurse cannot.
     *
     * Must match one of the `staff.category` enum values:
     * management, clinical, therapy, education, admin, support.
     */
    abstract public function assessorCategory(): string;

    /**
     * Section and field definitions.
     *
     * Returns an array keyed by section label. Each section contains
     * fields keyed by their field name. Each field declares:
     *   - label    (string, required)
     *   - type     (string, required: text, textarea, number, select,
     *               radio, checkbox, date)
     *   - options  (array, required for select/radio)
     *   - help     (string, optional)
     *   - required (bool, optional)
     *
     * @return array<string, array<string, array>>
     */
    abstract public function sections(): array;

    /**
     * Validation rules for the findings payload.
     * Rules are keyed with the `findings.` prefix.
     *
     * @return array<string, array|string>
     */
    abstract public function rules(): array;

    /**
     * Flattened list of every field name across every section.
     *
     * @return array<int, string>
     */
    public function fieldNames(): array
    {
        $names = [];

        foreach ($this->sections() as $section) {
            foreach (array_keys($section) as $fieldName) {
                $names[] = $fieldName;
            }
        }

        return $names;
    }
}