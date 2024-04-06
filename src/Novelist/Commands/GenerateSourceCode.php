<?php

namespace KBCrafted\Novelist\Commands;

use Illuminate\Console\Command;

class NovelistCraft extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'novelist:craft {--spec-dir=novelist}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Craft the Novelist project source code based on the provided specifications.';

    /**
     * @return void
     */
    public function handle(): void
    {

    }
}