<?php

namespace Fillincode\Swagger\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command as CommandAlias;

class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очищает директорию дата, запускает тесты, формирует новый yaml файл для swagger';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Artisan::call('swagger:destroy');

        Artisan::call('test');

        Artisan::call('swagger:parse');

        Artisan::call('swagger:destroy');

        return CommandAlias::SUCCESS;
    }
}
