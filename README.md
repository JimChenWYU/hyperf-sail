<h1 align="center"> hyperf-sail </h1>

<p align="center"> 模仿laravel/sail，兼容hyperf.</p>


## Installing

```shell
$ composer require jimchen/hyperf-sail -vvv
```

## Usage

### Setup

After Sail has been installed, you may run the `sail:install` command. This command will publish Sail's `docker-compose.yml` file to the root of your application:

```shell
$ php bin/hyperf.php sail:install
```

Finally, you may start Sail. To continue learning how to use Sail, please continue reading the remainder of this documentation:

```shell
$ ./vendor/bin/sail up
```

However, instead of repeatedly typing `vendor/bin/sail` to execute Sail commands, you may wish to configure a Bash alias that allows you to execute Sail's commands more easily:

```shell
$ alias sail='bash vendor/bin/sail'
```

Once the Bash alias has been configured, you may execute Sail commands by simply typing `sail`. The remainder of this documentation's examples will assume that you have configured this alias:

```shell
$ sail up
```

### Starting & Stopping Sail

Before starting Sail, you should ensure that no other web servers or databases are running on your local computer. To start all of the Docker containers defined in your application's `docker-compose.yml` file, you should execute the up command:

```shell
$ sail up
```

To start all of the Docker containers in the background, you may start Sail in "detached" mode:

```shell
$ sail up -d
```

Once the application's containers have been started, you may access the project in your web browser at: `http://localhost:9501`.

To stop all of the containers, you may simply press Control + C to stop the container's execution. Or, if the containers are running in the background, you may use the `down` command:

```shell
$ sail down
```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/JimChenWYU/hyperf-sail/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/JimChenWYU/hyperf-sail/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT