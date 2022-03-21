#!/usr/bin/env php
<?php
// user_upload.php
namespace Catalyst\Console\Command;

// This script using Symfony component called "Console"
// https://symfony.com/doc/current/components/console.html

const PATH_AUTOLOADER = __DIR__.'/vendor/autoload.php';
// simple check
if (file_exists(PATH_AUTOLOADER) && filesize(PATH_AUTOLOADER)) { //
    try {
        require PATH_AUTOLOADER;
    } catch (\Exception | \ErrorException $e) { // may be some network issue during composer install or HDD drive issue
        echo $e->getMessage()."\n";
        echo 'Error loading composer autoloader ('.PATH_AUTOLOADER.'), please try to reinstall it';
        exit;
    }
} else {
    echo 'Composer is required to run this script';
    exit;
}

// now we can use required components
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;

class UserUploadCommand extends Command
{
    protected static $defaultName = 'demo:args';

    protected function configure()
    {
        $this
            ->setDescription('Describe args behaviors')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('foo', 'f'),
                    new InputOption('bar', 'b', InputOption::VALUE_REQUIRED),
                    new InputOption('cat', 'c', InputOption::VALUE_OPTIONAL),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ...
    }
}

$application = new Application();
$application->add(new UserUploadCommand());

try {
    $application->run();

} catch (\Exception $e) { // there is no way to get this error "legally", only if i've pushed broken code into git. Can't imagine how to push untested code (at least "by hands") on production server
    echo $e->getMessage()."\n\n";
    echo 'There is critical error in this tool, please contact Alexey Derenchenko ( zaartix@gmail.com ) and provide this code:'."\n\n";
    echo base64_encode($e->getMessage()."\n".$e->getFile().':'.$e->getLine()."\n\n".$e->getTraceAsString()); // simple obfuscation
    // it is possible to send this error automatically on my email or in Telegram messenger, but in this case i think it is too much

    exit;
    // no need to exit here, but in future there is a chance to extend functionality of this script and without this "exit" script will continue to run and it is possible to get unexpected behavior
}