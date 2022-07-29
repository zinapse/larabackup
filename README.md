# zinapse/laralocate
Backup a database using Laravel connections.

## Overview
This package iterates over every table in the source connection (you can define tables to ignore), gets all the records, then creates them on the target connection.

## Installation
Include it with composer: `composer require zinapse/larabackup`

## Usage
Example: `php artisan larabackup:backup --source=source_connection --target=target_connection --ignore=tables,to,ignore`
