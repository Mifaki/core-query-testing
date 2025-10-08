<?php

class ExtractXhprofStats
{
    /**
     * Extract and format XHProf profiling statistics
     *
     * @param array|null $xhprof_data Raw XHProf data
     * @return array|null Formatted statistics array or null if invalid data
     */
    public static function extractStats($xhprof_data)
    {
        if (! $xhprof_data || ! is_array($xhprof_data)) {
            return null;
        }

        $main_data = $xhprof_data['main()'] ?? [];

        $total_wall_time    = 0;
        $total_memory_delta = 0;
        $peak_memory        = 0;
        $database_functions = 0;
        $core_functions     = 0;
        $php_core_functions = 0;
        $unique_functions   = 0;

        foreach ($xhprof_data as $function => $data) {
            if ($function === 'main()') {
                continue;
            }

            $unique_functions++;

            if (strpos($function, 'mysqli') !== false ||
                strpos($function, 'CoreDB') !== false ||
                strpos($function, 'mysql_') !== false ||
                strpos($function, 'PDO::') !== false ||
                strpos($function, 'QB::') !== false ||
                strpos($function, 'QBBase::') !== false ||
                strpos($function, 'QBWhere::') !== false ||
                strpos($function, 'QBRaw::') !== false) {
                $database_functions++;
            } elseif (strpos($function, 'Core') !== false) {
                $core_functions++;
            } else {
                $php_core_functions++;
            }

            if (isset($data['wt'])) {
                $total_wall_time += $data['wt'];
            }

            if (isset($data['mu'])) {
                $total_memory_delta += $data['mu'];
            }

            if (isset($data['pmu'])) {
                $peak_memory = max($peak_memory, $data['pmu']);
            }
        }

        return [
            'xhprof_stats' => [
                'total_execution_time_microsec' => $main_data['wt'] ?? 0,
                'total_execution_time_ms'       => round(($main_data['wt'] ?? 0) / 1000, 3),

                'total_memory_used_bytes'       => $main_data['mu'] ?? 0,
                'total_memory_used_kb'          => round(($main_data['mu'] ?? 0) / 1024, 3),
                'total_memory_used_mb'          => round(($main_data['mu'] ?? 0) / 1024 / 1024, 3),
                'peak_memory_kb'                => round(($main_data['pmu'] ?? 0) / 1024, 3),
                'peak_memory_mb'                => round(($main_data['pmu'] ?? 0) / 1024 / 1024, 3),

                'total_function_calls'          => $main_data['ct'] ?? 0,
                'unique_functions'              => $unique_functions,
                'database_related_functions'    => $database_functions,
                'core_framework_functions'      => $core_functions,
                'php_core_functions'            => $php_core_functions,

                'memory_efficiency'             => $database_functions > 0 ? round(($main_data['mu'] ?? 0) / $database_functions, 2) : 0,
                'time_efficiency'               => $database_functions > 0 ? round(($main_data['wt'] ?? 0) / $database_functions, 2) : 0,
            ],
        ];
    }

    /**
     * Display formatted profiling results
     *
     * @param array|null $stats_data Formatted stats from extractStats()
     * @param bool $detailed Whether to show detailed output
     * @return void
     */
    public static function displayStats($stats_data, $detailed = false)
    {
        if (! $stats_data || ! isset($stats_data['xhprof_stats'])) {
            echo "No profiling data available.\n";
            return;
        }

        $stats = $stats_data['xhprof_stats'];

        echo "=== CORE Framework Profiling Results ===\n";
        echo "Wall Time: {$stats['total_execution_time_ms']} ms\n";
        echo "Peak Memory: {$stats['peak_memory_kb']} KB ({$stats['peak_memory_mb']} MB)\n";
        echo "Total Functions: {$stats['unique_functions']}\n";
        echo "Total Function Calls: {$stats['total_function_calls']}\n";
        echo "Database Functions: {$stats['database_related_functions']}\n";
        echo "CORE Framework Functions: {$stats['core_framework_functions']}\n";
        echo "PHP Core Functions: {$stats['php_core_functions']}\n";

        if ($detailed) {
            echo "--- Detailed Stats ---\n";
            echo "Avg Wall Time per Function: {$stats['avg_wall_time_ms']} ms\n";
            echo "Total Memory Usage: {$stats['total_memory_used_kb']} KB\n";
            echo "Avg Memory per Function: {$stats['avg_memory_kb']} KB\n";
            echo "Avg Calls per Function: {$stats['avg_calls_per_function']}\n";
            echo "Memory per DB Call: {$stats['memory_efficiency_per_db_call']} bytes\n";
            echo "Time per DB Call: {$stats['time_efficiency_per_db_call']} microseconds\n";
        }

        echo "========================================\n";
    }

    /**
     * Get a simple summary array for API responses or logging
     *
     * @param array|null $stats_data Formatted stats from extractStats()
     * @return array Simple summary array
     */
    public static function getSummary($stats_data)
    {
        if (! $stats_data || ! isset($stats_data['xhprof_stats'])) {
            return [
                'wall_time_ms'       => 0,
                'peak_memory_kb'     => 0,
                'function_count'     => 0,
                'total_calls'        => 0,
                'database_functions' => 0,
                'core_functions'     => 0,
            ];
        }

        $stats = $stats_data['xhprof_stats'];

        return [
            'wall_time_ms'           => $stats['total_execution_time_ms'],
            'peak_memory_kb'         => $stats['peak_memory_kb'],
            'peak_memory_mb'         => $stats['peak_memory_mb'],
            'function_count'         => $stats['unique_functions'],
            'total_calls'            => $stats['total_function_calls'],
            'database_functions'     => $stats['database_related_functions'],
            'core_functions'         => $stats['core_framework_functions'],
            'php_core_functions'     => $stats['php_core_functions'],
            'avg_calls_per_function' => $stats['avg_calls_per_function'],
        ];
    }
}
