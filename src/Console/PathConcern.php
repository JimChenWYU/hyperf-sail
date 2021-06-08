<?php

namespace Hyperf\Sail\Console;

trait PathConcern
{
    public function basePath($path = '')
    {
        if (defined('BASE_PATH')) {
            return BASE_PATH . ($path ? DIRECTORY_SEPARATOR . $path : '');
        }

        return dirname(__DIR__, 4) . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}
