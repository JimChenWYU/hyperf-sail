<?php

namespace Hyperf\Sail\Console;

use Hyperf\Command\Command;

class PublishCommand extends Command
{
    use PathConcern;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sail:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the Hyperf Sail Docker files';

    public function handle()
    {
        file_put_contents(
            $this->basePath('docker-compose.yml'),
            str_replace(
                [
                    './vendor/jimchen/hyperf-sail/runtimes/7.2',
                    './vendor/jimchen/hyperf-sail/runtimes/7.3',
                    './vendor/jimchen/hyperf-sail/runtimes/7.4',
                ],
                [
                    './docker/7.4',
                    './docker/7.3',
                    './docker/7.2',
                ],
                file_get_contents($this->basePath('docker-compose.yml'))
            )
        );
    }
}
