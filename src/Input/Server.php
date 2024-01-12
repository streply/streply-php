<?php

namespace Streply\Input;

class Server
{
    public function getCpuLoad(): ?float
    {
        if (function_exists('sys_getloadavg') === false) {
            return null;
        }

        $load = sys_getloadavg();

        return $load[0] ?? null;
    }

    public function getDiskFreeSpace(): ?float
    {
        if (function_exists('disk_free_space') === false) {
            return null;
        }

        $value = disk_free_space('/');

        return $value === false ? null : $value;
    }

    public function getDiskTotalSpace(): ?float
    {
        if (function_exists('disk_total_space') === false) {
            return null;
        }

        $value = disk_total_space('/');

        return $value === false ? null : $value;
    }

    public function getMemoryUsage(): int
    {
        if (function_exists('memory_get_usage') === false) {
            return 0;
        }

        return memory_get_usage();
    }

    public function getMemoryPeakUsage(): int
    {
        if (function_exists('memory_get_peak_usage') === false) {
            return 0;
        }

        return memory_get_peak_usage();
    }
}
