<?php

namespace Fillincode\Swagger\Console;

use Illuminate\Console\Command;
use Fillincode\Swagger\Parser\BodyGenerator;
use Fillincode\Swagger\Parser\ParametersGenerator;
use Fillincode\Swagger\Parser\ResponseGenerator;
use Fillincode\Swagger\Parser\SwaggerGenerator;
use JsonException;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class ParseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Собирает yaml файл из файлов в директории data';

    /**
     * Execute the console command.
     *
     * @throws JsonException
     * @throws Throwable
     */
    public function handle(): int
    {
        (new SwaggerGenerator(
            new BodyGenerator(),
            new ResponseGenerator(),
            new ParametersGenerator()
        ))->generate();

        return CommandAlias::SUCCESS;
    }
}
