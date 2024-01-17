<?php

namespace Fillincode\Swagger\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command as CommandAlias;

class DestroyDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger:destroy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очищает директорию с временными файлами';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Storage::disk(config('swagger.storage.driver'))->deleteDirectory(config('swagger.storage.path'));

        return CommandAlias::SUCCESS;
    }
}
