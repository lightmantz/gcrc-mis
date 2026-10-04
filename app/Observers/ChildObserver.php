<?php

namespace App\Observers;

use App\Models\Child;

class ChildObserver
{
    public function creating(Child $child): void
    {
        if (empty($child->child_number)) {
            $child->child_number = $this->nextChildNumber();
        }

        if (empty($child->registration_date)) {
            $child->registration_date = now()->toDateString();
        }
    }

    /**
     * Format: GCRC-YYYY-NNNN
     * The sequence resets each calendar year.
     */
    private function nextChildNumber(): string
    {
        $year = now()->year;
        $prefix = "GCRC-{$year}-";

        $last = Child::withTrashed()
            ->where('child_number', 'like', $prefix . '%')
            ->orderByDesc('child_number')
            ->value('child_number');

        $next = 1;

        if ($last) {
            $next = (int) substr($last, strlen($prefix)) + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}