<?php

/**
 * Skeleton for single-file simple PHP/PHAR command line app, based on Symfony Console and Box.
 *
 * # Building
 *
 * - Download `box.phar` from https://github.com/box-project/box/releases
 * - Build your `app.phar` using this command: `composer install; php box.phar compile`.
 * - Make sure to initialize git repository, otherwise remove `git-commit-short` from `box.json`.
 *
 * # Code
 *
 * - Add new console commands into `Command` directory or any custom directory.
 * - Make sure your commands always extend App\Command class.
 * - Organize other classes in custom directories thanks to Composer autoloading.
 * - Initialize command dependencies manually since there is no dependency injection.
 * - Avoid launching `app.php` directly: some PHP features work differently in compiled PHAR apps.
 * - If launching `app.php` directly: run `composer dump-autoload` after creating new commands.
 *
 * # Config
 *
 * - Save app config in `config.yaml` file alongside compiled `app.phar` file.
 * - Use `./app.phar dump-config` command to generate `config.yaml` initial structure.
 * - Modify expected config file structure in `config_tree.php` to satisfy your needs.
 * - Consume app config in your commands using App\Command::$config object.
 */

use App\Command;
use App\Config;
use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

include __DIR__ . '/vendor/autoload.php';

set_error_handler(function ($severity, $message, $file, $line) {
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
});

$app = new Application('app', 'commit: @git_commit_short@');

$configTree = require __DIR__ . '/config_tree.php';
$config = new Config(dirname(Phar::running(false) ?: __FILE__) . '/config.yaml', $configTree);

$app->register('dump-config')
    ->setDescription('Dump config.yaml skeleton to stdout')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($configTree) {
        $output->writeln((new YamlReferenceDumper())->dumpNode($configTree));
    })
;

foreach (require __DIR__ . '/vendor/composer/autoload_classmap.php' as $class => $file) {
    if (!str_starts_with($file, __DIR__ . '/vendor/') && is_subclass_of($class, Command::class)) {
        $app->add(new $class($config));
    }
}

$app->get('list')->setHidden(true);
$app->get('help')->setHidden(true);
$app->get('completion')->setHidden(true);

$app->run();
