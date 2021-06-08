<?php

namespace Hyperf\Sail;

use Hyperf\Sail\Console\InstallCommand;
use Hyperf\Sail\Console\PublishCommand;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'commands' => [
                InstallCommand::class,
                PublishCommand::class,
            ],
        ];
    }
}
