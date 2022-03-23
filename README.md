# Test tasks for employer

## Overview
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
- Default mode is output only results of processing CSV data. 
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

