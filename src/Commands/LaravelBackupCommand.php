<?php

namespace Zinapse\LaraBackup\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LaravelBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larabackup:backup
                            {--source= : The source connection to use}
                            {--target= : The target connection to use}
                            {--ignore= : A comma separated list of tables to ignore}
                            {--V : Be more verbose}';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate your Laravel application';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        // Check if we should be verbose
        $verbose = !empty($this->option('V'));

        // Output
        $this->info('Starting...');

        // Define our connections
        $source_connection = $this->option('source') ?? null;
        $target_connection = $this->option('target') ?? null;

        // Find tables to ignore
        $ignore = $this->option('ignore') ?? null;
        if(!empty($ignore)) {
            $ignore = explode(',', $ignore);
        }

        // Verbose output
        if($verbose) {
            foreach($ignore as $ignored) $this->info('Ignoring: ' . $ignored);
        }

        // Get database tables
        $tables = [];
        $tables_raw = DB::select('SHOW TABLES');
        foreach($tables_raw as $table_array) {
            foreach($table_array as $table_name) {
                // If the table isn't in the ignore list then add it to the array
                if((is_array($ignore) && !in_array($table_name, $ignore)) || !is_array($ignore)) {
                    $tables[] = $table_name;

                    // Verbose output
                    if($verbose) $this->info('Table found: ' . $table_name);
                }
            }
        }

        // Define column information
        $table_data = [];
        foreach($tables as $table) {
            // Get the column information
            $describe = DB::connection($source_connection)->select('DESCRIBE ' . $table);

            // Add to the new $table_data array
            foreach($describe as $row) {
                // Make sure we have a name
                if(empty($row->Field)) continue;

                // Add the name to the array
                $table_data[$table] = $row->Field;

                // Verbose output
                if($verbose) $this->info('Column: ' . $row->Field);
            }
        }

        // Iterate over the table data
        foreach($table_data as $name => $columns) {
            // Get all the data from the table
            $all = DB::connection($source_connection)->table($name)->get();

            // Iterate through the records
            foreach($all as $record) {
                // Define a structure array
                $structure = [];

                // Add data to the array
                foreach($record as $key => $value) $structure[$key] = $value;

                // Backup data
                DB::connection($target_connection)->table($name)->insertOrIgnore($structure);
            }
        }
    }
}