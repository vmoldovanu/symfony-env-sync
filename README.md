

Symfony Env Sync
-------------

This is the clone of [Laravel Env Sync](https://github.com/JulienTant/Laravel-Env-Sync) for Symfony

Keep your .env in sync with your .env.dist or vice versa.

It reads the .env.dist file and makes suggestions to fill your .env accordingly. 

Installation
-------------

Add [`vmoldovanu/symfony-env-sync`](https://packagist.org/packages/vmoldovanu/symfony-env-sync) to your `composer.json` file:

```
composer require vmoldovanu/symfony-env-sync
```

#### Register the bundle: 
**Symfony 3 Version:**  
Register bundle into `app/AppKernel.php`:

``` php
public function registerBundles()
{
    return array(
        // ...
        new SymEnvSync\SymfonyEnvSync\SymfonyEnvSyncBundle(),
    );
}
```
**Symfony 4 Version:**   
Register bundle into `config/bundles.php`:  
```php 
return [
    //...
    SymEnvSync\SymfonyEnvSync\SymfonyEnvSyncBundle::class => ['all' => true],
];
```

Usage
-------------

### Sync your envs files

You can populate your .env file from the .env.example by using the `php bin/console env:sync` command.

The command will tell you if there's anything not in sync between your files and will propose values to add into the .env file.

You can launch the command with the option `--reverse` to fill the .env.example file from the .env file

You can also use `--src` and `--dest` to specify which file you want to use. You must use either both flags, or none.

If you use the `--no-interaction` flag, the command will copy all new keys with their default values.

### Check for diff in your envs files

You can check if your .env is missing some variables from your .env.example by using the `php bin/console env:check` command.

The command simply show you which keys are not present in your .env file. This command will return 0 if your files are in sync, and 1 if they are not, so you can use this in a script

Again, you can launch the command with the option `--reverse` or with `--src` and `--dest`.

### Show diff between your envs files

You can show a table that compares the content of your env files by using the `php bin/console env:diff` command.

The command will print a table that compares the content of both .env and .env.example files, and will highlight the missing keys.

You can launch the command with the options `--src` and `--dest`.

