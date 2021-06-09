<?php

namespace Hyperf\Sail;

use Hyperf\Sail\Console\InstallCommand;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'commands' => [
                InstallCommand::class,
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
