#!/usr/bin/env php
<?php
// user_upload.php
namespace Command;

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
use Catalyst\Exception\CSVFileNotFoundException;
use Catalyst\Service\DbService;
use Catalyst\Service\CsvService;
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

    /** @var SymfonyStyle  */
    private $io;
    /** @var DbService */
    private $sDb;
    /** @var CsvService */
    private $sCsv;

    protected function configure()
    {
        $this
            ->setDescription('Possible options')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('file', 'f', InputOption::VALUE_REQUIRED, 'The name of the CSV to be parsed'),
                    new InputOption('create_table', null, null, 'This will cause the MySQL users table to be built'),
                    new InputOption('dry_run', null, null,'Do not alter database, just run the script'),

                    // Common mysql service shortcuts will be capitalized because of conflict with 'h' for help
                    new InputOption('db_user', 'U', InputOption::VALUE_REQUIRED,'Mysql username','root'),
                    new InputOption('db_password', 'P', InputOption::VALUE_REQUIRED,'Mysql password',''),
                    new InputOption('db_host', 'H', InputOption::VALUE_REQUIRED,'Mysql database host','localhost'),
                    new InputOption('db_name', 'N', InputOption::VALUE_REQUIRED,'Mysql database name','test'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input,$output);
        if (count($input->getArguments()) === 1) {
            $this->io->title('Catalyst code competition');
            $this->io->text('Use -h or --help to display all possible options');
            $output->writeln('');
            $output->writeln('');
        }

        $isReadOnly = true;
        if (!$input->getOption('dry_run')) {
            $isReadOnly = false;
            // database connection is not required in other cases
            $this->sDb = new DbService($input->getOption('db_user'),$input->getOption('db_password'),$input->getOption('db_host'),$input->getOption('db_name'));

            if ($input->getOption('create_table')) {
                $isCreated = $this->sDb->createTableUsers();
                if (!$isCreated) {
                    $answer = $this->io->ask('Table already exits. Recreate it?','no');
                    if ($answer == 'yes') {
                        $this->sDb->createTableUsers(true);
                    }
                }
            }
        }

        $rowsInserted = 0;
        if ($input->getOption('file')) {
            $pathCSV = $input->getOption('file');
            // my first thought was a security check for file location (for example /etc/passwd) but in case of console
            // script this check is not required because of permissions inherited from the system user.
            if (!file_exists($pathCSV)) {
                throw new CSVFileNotFoundException('Csv file ('.$pathCSV.') not found');
            }
            $this->sCsv = new CsvService();
            $this->sCsv->setOutputInterface($output);
            $readyForInsert = $this->sCsv->processFile($pathCSV);
            if (!$isReadOnly && count($readyForInsert)) {
                foreach ($readyForInsert as $row) {
                    $success = $this->sDb->insert($row[0],$row[1],$row[2]); // looks not pretty but uses less memory in case of large files to import
                    if ($output->isVerbose() && !$success) {
                        $this->io->error('Mysql error during insert');
                    }
                    if ($success) {
                        $rowsInserted++;
                    }
                }
            }
            $this->io->title($pathCSV);
            $this->io->table(['Total rows','Valid rows','Inserted rows'],[[$this->sCsv->fileRowsTotal,$this->sCsv->fileRowsValid,$rowsInserted]]);
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

} catch (CSVFileNotFoundException $e) {

} catch (\Exception $e) { // there is no way to get this error "legally", only if i've pushed broken code into git. Can't imagine how to push untested code (at least "by hands") on production server
    echo $e->getMessage()."\n\n";
    echo 'There is critical error in this tool, please contact Alexey Derenchenko ( zaartix@gmail.com ) and provide this code:'."\n\n";
    echo base64_encode($e->getMessage()."\n".$e->getFile().':'.$e->getLine()."\n\n".$e->getTraceAsString()); // simple obfuscation
    // it is possible to send this error automatically on my email or in Telegram messenger, but in this case i think it is too much

    exit;
    // no need to exit here, but in future there is a chance to extend functionality of this script and without this "exit" script will continue to run, and it is possible to get unexpected behavior
}