<?php

namespace KBCrafted\Novelist\Commands;

use Exception;
use Illuminate\Console\Command;
use Novelist\Requirements\RequirementRegistry;

class NovelistCheck extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'novelist:check {--spec-dir=novelist}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Validate and verify the Novelist project configuration to ensure consistency and correctness.';

    /**
     * @return void
     */
    public function handle(): void
    {
        try {
            $specificationDirectory = $this->option('spec-dir');
            if(!file_exists(base_path($specificationDirectory))) {
                throw new Exception(sprintf("Specification directory [%s] does not exist.", base_path($specificationDirectory)));
            }
            RequirementRegistry::getInstance()->gatherFromDirectory($specificationDirectory);
        } catch (Exception $exception) {
            $this->error(sprintf("Failed to gather the application specifications: %", $exception->getMessage()));
        }
    }
}