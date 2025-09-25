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

        $total_wall_time      = 0;
        $total_cpu_time       = 0;
        $total_memory         = 0;
        $peak_memory          = 0;
        $max_wall_time        = 0;
        $max_cpu_time         = 0;
        $function_count       = 0;
        $total_function_calls = 0;

        foreach ($xhprof_data as $function => $data) {
            if ($function === 'main()') {
                continue; // Skip main function
            }

            $function_count++;

            // Wall time (execution time)
            if (isset($data['wt'])) {
                $total_wall_time += $data['wt'];
                $max_wall_time = max($max_wall_time, $data['wt']);
            }

            // CPU time
            if (isset($data['cpu'])) {
                $total_cpu_time += $data['cpu'];
                $max_cpu_time = max($max_cpu_time, $data['cpu']);
            }

            // Memory usage
            if (isset($data['mu'])) {
                $total_memory += $data['mu'];
            }

            // Peak memory usage
            if (isset($data['pmu'])) {
                $peak_memory = max($peak_memory, $data['pmu']);
            }

            // Function call count
            if (isset($data['ct'])) {
                $total_function_calls += $data['ct'];
            }
        }

        $avg_wall_time = $function_count > 0 ? $total_wall_time / $function_count : 0;
        $avg_memory    = $function_count > 0 ? $total_memory / $function_count : 0;

        return [
            'xhprof_stats' => [
                'total_wall_time_microsec' => round($total_wall_time, 3),
                'total_wall_time_ms'       => round($total_wall_time / 1000, 3),
                'max_wall_time_microsec'   => round($max_wall_time, 3),
                'max_wall_time_ms'         => round($max_wall_time / 1000, 3),
                'avg_wall_time_microsec'   => round($avg_wall_time, 3),
                'avg_wall_time_ms'         => round($avg_wall_time / 1000, 3),
                'total_memory_bytes'       => round($total_memory, 3),
                'total_memory_kb'          => round($total_memory / 1024, 3),
                'peak_memory_bytes'        => $peak_memory,
                'peak_memory_kb'           => round($peak_memory / 1024, 3),
                'peak_memory_mb'           => round($peak_memory / 1024 / 1024, 3),
                'avg_memory_bytes'         => round($avg_memory, 3),
                'avg_memory_kb'            => round($avg_memory / 1024, 3),
                'total_function_calls'     => $total_function_calls,
                'unique_functions'         => $function_count,
                'avg_calls_per_function'   => $function_count > 0 ? round($total_function_calls / $function_count, 2) : 0,
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

        echo "=== Profiling Results ===\n";
        echo "Wall Time: {$stats['total_wall_time_ms']} ms\n";
        echo "Peak Memory: {$stats['peak_memory_kb']} KB ({$stats['peak_memory_mb']} MB)\n";
        echo "Total Functions: {$stats['unique_functions']}\n";
        echo "Total Function Calls: {$stats['total_function_calls']}\n";

        if ($detailed) {
            echo "--- Detailed Stats ---\n";
            echo "Max Wall Time: {$stats['max_wall_time_ms']} ms\n";
            echo "Avg Wall Time per Function: {$stats['avg_wall_time_ms']} ms\n";
            echo "Total Memory Usage: {$stats['total_memory_kb']} KB\n";
            echo "Avg Memory per Function: {$stats['avg_memory_kb']} KB\n";
            echo "Avg Calls per Function: {$stats['avg_calls_per_function']}\n";
        }

        echo "========================\n";
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
                'wall_time_ms'   => 0,
                'peak_memory_kb' => 0,
                'function_count' => 0,
                'total_calls'    => 0,
            ];
        }

        $stats = $stats_data['xhprof_stats'];

        return [
            'wall_time_ms'           => $stats['total_wall_time_ms'],
            'peak_memory_kb'         => $stats['peak_memory_kb'],
            'peak_memory_mb'         => $stats['peak_memory_mb'],
            'function_count'         => $stats['unique_functions'],
            'total_calls'            => $stats['total_function_calls'],
            'avg_calls_per_function' => $stats['avg_calls_per_function'],
        ];
    }
}
