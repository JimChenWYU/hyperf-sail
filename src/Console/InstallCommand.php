<?php

namespace Hyperf\Sail\Console;

use Hyperf\Command\Command;
use Hyperf\Sail\Concerns;

class InstallCommand extends Command
{
    use Concerns\Pathname;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sail:install
                                {--php= : The php version}
                                {--with= : The services that should be included in the installation}';

    const PHP_VERSION_FIELDS = [
        '7.2',
        '7.3',
        '7.4',
        '8.0',
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Hyperf Sail\'s default Docker Compose file & Dockerfile';

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->setDescription($this->description);
    }

    public function handle()
    {
        $with = $this->input->getOption('with');
        $phpversion = $this->input->getOption('php');

        if ($phpversion) {
            if (! in_array($phpversion, self::PHP_VERSION_FIELDS)) {
                $this->warn("PHP {$phpversion} not supported.");
                return ;
            }
        } else {
            $phpversion = $this->gatherPhpVersionWithSymfonyMenu();
        }

        if ($with) {
            $services = ($with === 'none' ? [] : explode(',', $with));
        } elseif ($this->input->getOption('no-interaction')) {
            $services = ['mysql', 'redis', 'selenium', 'mailhog'];
        } else {
            $services = $this->gatherServicesWithSymfonyMenu();
        }

        $this->buildDockerfile();
        $this->buildDockerCompose($services, $phpversion);
        $this->replaceEnvVariables($services);

        $this->info('Sail scaffolding installed successfully.');
    }

    /**
     * Gather the desired PHP version using a Symfony menu.
     *
     * @return string
     */
    protected function gatherPhpVersionWithSymfonyMenu()
    {
        return $this->choice('Which php version would you like to build?', self::PHP_VERSION_FIELDS, 0, null);
    }

    /**
     * Gather the desired Sail services using a Symfony menu.
     *
     * @return array
     */
    protected function gatherServicesWithSymfonyMenu()
    {
        return $this->choiceMultiple('Which services would you like to install?', [
            'mysql',
            'pgsql',
            'mariadb',
            'redis',
            'memcached',
            'meilisearch',
            'minio',
            'mailhog',
            'selenium',
        ], 0, null);
    }

    /**
     * Build the Docker Compose file.
     *
     * @param array  $services
     * @param string $phpversion
     * @return void
     */
    protected function buildDockerCompose(array $services, string $phpversion)
    {
        $depends = collect($services)
            ->filter(function ($service) {
                return in_array($service, ['mysql', 'pgsql', 'mariadb', 'redis', 'meilisearch', 'minio', 'selenium']);
            })->map(function ($service) {
                return "            - {$service}";
            })->pipe(function ($collection) {
                return $collection->when($collection->isNotEmpty(), function ($collection) {
                    return $collection->prepend('depends_on:');
                });
            })->implode("\n");

        $stubs = rtrim(collect($services)->map(function ($service) {
            return file_get_contents(__DIR__ . "/../../stubs/{$service}.stub");
        })->implode(''));

        $volumes = collect($services)
            ->filter(function ($service) {
                return in_array($service, ['mysql', 'pgsql', 'mariadb', 'redis', 'meilisearch', 'minio']);
            })->map(function ($service) {
                return "    sail{$service}:\n        driver: local";
            })->pipe(function ($collection) {
                return $collection->when($collection->isNotEmpty(), function ($collection) {
                    return $collection->prepend('volumes:');
                });
            })->implode("\n");

        $dockerCompose = file_get_contents(__DIR__ . '/../../stubs/docker-compose.stub');

        $dockerCompose = str_replace('{{phpversion}}', $phpversion, $dockerCompose);
        $dockerCompose = str_replace('{{depends}}', empty($depends) ? '' : '        ' . $depends, $dockerCompose);
        $dockerCompose = str_replace('{{services}}', $stubs, $dockerCompose);
        $dockerCompose = str_replace('{{volumes}}', $volumes, $dockerCompose);

        // Remove empty lines...
        $dockerCompose = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $dockerCompose);

        file_put_contents($this->basePath('docker-compose.yml'), $dockerCompose);
    }

    /**
     * Build the Dockerfile.
     */
    public function buildDockerfile()
    {
        if (!copy(__DIR__ . '/../../stubs/Dockerfile.stub', $this->basePath('Dockerfile'))) {
            $this->warn('Copy Dockerfile failed.');
        }
    }

    /**
     * Replace the Host environment variables in the app's .env file.
     *
     * @param  array  $services
     * @return void
     */
    protected function replaceEnvVariables(array $services)
    {
        $environment = file_get_contents($this->basePath('.env'));

        if (in_array('pgsql', $services)) {
            $environment = str_replace('DB_CONNECTION=mysql', "DB_CONNECTION=pgsql", $environment);
            $environment = str_replace('DB_HOST=127.0.0.1', "DB_HOST=pgsql", $environment);
            $environment = str_replace('DB_PORT=3306', "DB_PORT=5432", $environment);
        } elseif (in_array('mariadb', $services)) {
            $environment = str_replace('DB_HOST=127.0.0.1', "DB_HOST=mariadb", $environment);
        } else {
            $environment = str_replace('DB_HOST=127.0.0.1', "DB_HOST=mysql", $environment);
        }

        $environment = str_replace('DB_USERNAME=root', "DB_USERNAME=sail", $environment);
        $environment = preg_replace("/DB_PASSWORD=(.*)/", "DB_PASSWORD=password", $environment);

        $environment = str_replace('MEMCACHED_HOST=127.0.0.1', 'MEMCACHED_HOST=memcached', $environment);
        $environment = str_replace('REDIS_HOST=127.0.0.1', 'REDIS_HOST=redis', $environment);

        if (in_array('meilisearch', $services)) {
            $environment .= "\nSCOUT_DRIVER=meilisearch";
            $environment .= "\nMEILISEARCH_HOST=http://meilisearch:7700\n";
        }

        file_put_contents($this->basePath('.env'), $environment);
    }
}
