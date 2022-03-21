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
use Symfony\Component\Console\Style\SymfonyStyle;

class UserUploadCommand extends Command
{
    protected static $defaultName = 'catalyst:user_upload';
    protected static $defaultDescription = 'Import CSV file into database';

    protected function configure()
    {
        $this
            ->setDescription('Possible options')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('file', 'f', InputOption::VALUE_OPTIONAL, 'The name of the CSV to be parsed'),
                    new InputOption('create_table', null, InputOption::VALUE_OPTIONAL, 'This will cause the MySQL users table to be built'),
                    new InputOption('dry_run', null, InputOption::VALUE_OPTIONAL,'Do not alter database, just run the script'),

                    // Common service shortcuts will be capitalized because of conflict with 'h' for help
                    new InputOption('db_user', 'U', InputOption::VALUE_OPTIONAL,'Mysql username'),
                    new InputOption('db_password', 'P', InputOption::VALUE_OPTIONAL,'Mysql password'),
                    new InputOption('db_host', 'H', InputOption::VALUE_OPTIONAL,'Mysql database'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input,$output);
        if (count($input->getArguments()) === 1) {
            $io->title('Catalyst code competition');
            $io->text('Use -h or --help to display all possible options');
        }


        // for more comfortable reading
        $output->writeln('');
        $output->writeln('');
        return Command::SUCCESS;
    }
}

$application = new Application();
$userUploadCommand = new UserUploadCommand();
$application->add($userUploadCommand);
$application->setDefaultCommand($userUploadCommand->getName());

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