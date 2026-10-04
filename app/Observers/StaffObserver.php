<?php

namespace App\Observers;

use App\Models\Staff;

class StaffObserver
{
    public function creating(Staff $staff): void
    {
        if (empty($staff->staff_number)) {
            $staff->staff_number = $this->nextStaffNumber();
        }
    }

    /**
     * Format: GCRC-STAFF-0001
     * Uses the highest existing number, so IDs are stable across deletions.
     */
    private function nextStaffNumber(): string
    {
        $prefix = 'GCRC-STAFF-';

        $last = Staff::withTrashed()
            ->where('staff_number', 'like', $prefix . '%')
            ->orderByDesc('staff_number')
            ->value('staff_number');

        $next = 1;

        if ($last) {
            $next = (int) str_replace($prefix, '', $last) + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}