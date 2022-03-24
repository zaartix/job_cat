# Test task 1
## Script Task
Create a PHP script, that is executed from the command line, which accepts a CSV file as input (see command
line directives below) and processes the CSV file. The parsed file data is to be inserted into a MySQL database.
A CSV file is provided as part of this task that contains test data, the script must be able to process this file
appropriately.
The PHP script will need to correctly handle the following criteria:
- CSV file will contain user data and have three columns: name, surname, email (see table
definition below)
- CSV file will have an arbitrary list of users
- Script will iterate through the CSV rows and insert each record into a dedicated MySQL
database into the table “users”
- The users database table will need to be created/rebuilt as part of the PHP script. This will be
defined as a Command Line directive below
- Name and surname field should be set to be capitalised e.g. from “john” to “John” before being
inserted into DB
- Emails need to be set to be lower case before being inserted into DB
- The script should validate the email address before inserting, to make sure that it is valid (valid
means that it is a legal email format, e.g. “xxxx@asdf@asdf” is not a legal format). In case that
an email is invalid, no insert should be made to database and an error message should be
reported to STDOUT.

We are looking for a script that is robust and gracefully handles errors/exceptions.
The PHP script command line argument definition is outlined in 1.4 Script Command Line Directives .
However, user documentation will be looked upon favourably.

## Source Control
The code for the test is to be managed using “git” as the Version Control System, with the repository made
available via online repository: GitHub (github.com), bitbucket (bitbucket.org) etc. This will be how the sample
code is to be delivered to Catalyst at the completion of development.
A repository with only one commit is not acceptable. Showing the development process is just as important as
the task itself.

## Assumptions
- The deliverable will be a running PHP script – it will be executed on an Ubuntu 20.04 instance
- PHP version is: 7.4.x (or higher)
- Catalyst would like to see your development process history in git – not just a completed
script
- There may be some libraries that need to be installed via apt-get, pear or composer. This is
fine but these dependencies should be outlined in provided install documentation
- MySQL database server is already installed and is version 8.0 (higher versions are fine, as is
MariaDB 10.x) – DB user details should be configurable
- PHP script will be called – user_upload.php
- CSV file will be called users.csv and is provided with this document.
If there are any unclear details here, you are welcome to make assumptions as long as they are clearly stated
and documented as part of the deliverables.

## User Table Definition
The MySQL table should contain at least these fields:
- name
- surname
- email (email should be set to a UNIQUE index).

## Script Command Line Directives
The PHP script should include these command line options (directives):
- --file [csv file name] – this is the name of the CSV to be parsed
- --create_table – this will cause the MySQL users table to be built (and no further action will be taken)
- --dry_run – this will be used with the --file directive in case we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered
- -u – MySQL username
- -p – MySQL password
- -h – MySQL host
- --help – which will output the above list of directives with details.
# Test task 2
## Logic Test
Create a PHP script that is executed form the command line. The script should:
- Output the numbers from 1 to 100
- Where the number is divisible by three (3) output the word “foo”
- Where the number is divisible by five (5) output the word “bar”
- Where the number is divisible by three (3) and (5) output the word “foobar”
- Only be a single PHP file
## Example
An example output of the script would look like:
1, 2, foo, 4, bar, foo, 7, 8, foo, bar, 11, foo, 13, 14, foobar ...
## Deliverable
The deliverable for this task is a PHP script called foobar.php. Please include this script in the same source
control as the script test.

# Result
This is the result of a test task for one of the employers. The name of the employer is hidden for privacy reasons of their test tasks.

## Installation

To install you will need to clone this repository
```bash
git clone https://github.com/zaartix/job_cat.git
```
Go to `job_cat` directory
```bash
cd ./job_cat
```
After that you need to install composer
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```
Finally you need to run composer to download dependencies
```bash
php ./composer.phar install
```
## How to use
`user_upload.php` console script to process CSV file and insert data to database
`foobar.php` console script to output results of logic test

### user_upload.php
There is three different output modes. 
- Default mode is output only results of processed CSV data. 
- Verbose mode is output invalid rows. 
- Debug mode is output sql queries, pre and post processed rows, etec.

#### Script Command Line Directives

Available command line options (directives):
- `--file [csv file name]` – path to CSV file to be parsed
- `--create_table` – create MySQL users table
- `--dry_run` – do not alter database. Readonly mode
- `-U` – MySQL username
- `-P` – MySQL password
- `-H` – MySQL host
- `-N` – MySQL database name
- `-v` - verbose output mode
- `-vvv` - debug output mode
- `-h` or `--help` - display help
- `-q` or `--quiet` - do not output any message
- `-n` or `--no-interaction` - do not ask any interactive question

