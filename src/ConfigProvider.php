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
            'publish' => [
                [
                    'id' => 'docker',
                    'description' => 'The docker file.',
                    'source' => __DIR__ . '/../runtimes/',
                    'destination' => BASE_PATH . '/docker/',
                ],
            ],
        ];
    }
}
