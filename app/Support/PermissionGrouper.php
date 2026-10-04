<?php

namespace App\Support;

use Spatie\Permission\Models\Permission;

class PermissionGrouper
{
    /**
     * Groups all permissions by module prefix (before the first dot).
     *
     * @param  array<int, string>  $grantedNames  Permission names currently granted (may be empty)
     * @return array<string, array{
     *     label: string,
     *     granted_count: int,
     *     total_count: int,
     *     permissions: array<int, array{name: string, action: string, granted: bool}>
     * }>
     */
    public static function group(array $grantedNames = []): array
    {
        $all = Permission::orderBy('name')->pluck('name')->all();
        $granted = array_flip($grantedNames);
        $groups = [];

        foreach ($all as $name) {
            [$module, $action] = array_pad(explode('.', $name, 2), 2, 'view');

            $groups[$module] ??= [
                'label'         => self::humaniseModule($module),
                'permissions'   => [],
                'granted_count' => 0,
                'total_count'   => 0,
            ];

            $isGranted = isset($granted[$name]);

            $groups[$module]['permissions'][] = [
                'name'    => $name,
                'action'  => $action,
                'granted' => $isGranted,
            ];

            $groups[$module]['total_count']++;

            if ($isGranted) {
                $groups[$module]['granted_count']++;
            }
        }

        ksort($groups);

        return $groups;
    }

    /**
     * Same as group(), but scoped to a specific list of permission names.
     * Useful for rendering a diff of "these permissions only".
     *
     * @param  array<int, string>  $names
     */
    public static function summarise(array $names): array
    {
        $groups = [];

        foreach ($names as $name) {
            [$module, $action] = array_pad(explode('.', $name, 2), 2, 'view');

            $groups[$module] ??= [
                'label'   => self::humaniseModule($module),
                'actions' => [],
            ];

            $groups[$module]['actions'][] = $action;
        }

        ksort($groups);

        return $groups;
    }

    /**
     * Converts "staff_attendance" to "Staff Attendance".
     */
    public static function humaniseModule(string $module): string
    {
        return ucwords(str_replace('_', ' ', $module));
    }
}