<?php

namespace Fillincode\Swagger\Parser;

use Error;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use JsonException;
use Symfony\Component\Yaml\Yaml;
use Throwable;

class SwaggerGenerator
{
    /**
     * @param  BodyGenerator  $bodyGenerator Генератор тела запроса
     * @param  ResponseGenerator  $responseGenerator Генератор ответов
     * @param  ParametersGenerator  $parametersGenerator Генератор параметров
     * @param  array  $data Данные для создания файла
     * @param  string  $path Путь в адресной строке
     * @param  string  $method Метод запроса
     * @param  int  $code Код ответа
     * @param  array  $config Конфигурация swagger
     */
    public function __construct(
        protected BodyGenerator $bodyGenerator,
        protected ResponseGenerator $responseGenerator,
        protected ParametersGenerator $parametersGenerator,
        protected array $data = [],
        protected string $path = '',
        protected string $method = '',
        protected int $code = 200,
        protected array $config = []
    ) {
    }

    /**
     * Запуск генерации документации
     *
     * @throws JsonException
     * @throws Throwable
     */
    public function generate(): void
    {
        $this->parseFromConfig();

        $files = Storage::drive(config('swagger.storage.driver'))->files(config('swagger.storage.path'));

        foreach ($files as $file) {
            $this->parseFromFile(Yaml::parseFile('storage/app/'.$file));
        }

        $this->makeFile(
            config('l5-swagger.documentations.default.paths.format_to_use_for_docs')
        );
    }

    /**
     * Формирует основные данные из конфига
     *
     * @throws Throwable
     */
    protected function parseFromConfig(): void
    {
        $this->config = config('swagger');

        $this->data = [
            'openapi' => $this->config['openapi'],
            'info' => $this->config['info'],
            'servers' => $this->config['servers'],
            'paths' => [],
        ];

        if ($this->config['auth']['has_auth']) {
            $this->config['securitySchemes'][$this->config['auth']['security_schema']]['description'] .= $this->getTokenLine();
            $this->data['components'] = [
                'securitySchemes' => [
                    $this->config['auth']['security_schema'] => $this->config['securitySchemes'][$this->config['auth']['security_schema']],
                ],
            ];
        }
    }

    /**
     * Формирует данные по маршрутам из файлов
     *
     *
     * @throws JsonException
     */
    protected function parseFromFile(array $data): void
    {
        $this->path = '/'.$data['path'];
        $this->method = strtolower($data['method']);
        $this->code = $data['code'];

        if (isset($this->data['paths'][$this->path][$this->method]['responses'][$this->code])) {
            return;
        }

        if (isset($this->data['paths'][$this->path][$this->method])) {
            $this->addDataCode($data);

            return;
        }

        if (isset($this->data['paths'][$this->path])) {
            $this->addDataMethod($data);

            return;
        }

        $this->addDataPath($data);
    }

    /**
     * Добавляет данные только по ответу
     *
     *
     * @throws JsonException
     */
    protected function addDataCode(array $data): void
    {
        $this->data['paths'][$this->path][$this->method]['responses'][$this->code] = $this->responseGenerator->generateResponse($data, $this->code);

        if ($this->code >= 200 && $this->code < 300) {
            $this->data['paths'][$this->path][$this->method]['requestBody'] = $this->bodyGenerator->generateBody($data);
            $this->data['paths'][$this->path][$this->method]['parameters'] = $this->parametersGenerator->generateParameters($data);
        }
    }

    /**
     * Добавляет данные по ответу к текущему методу
     *
     *
     * @throws JsonException
     */
    protected function addDataMethod(array $data): void
    {
        $this->data['paths'][$this->path][$this->method] = [
            'tags' => [collect($data['method_attributes'] ?? [])->where('type', 'group')?->first()['group'] ?? 'default'],
            'summary' => collect($data['method_attributes'] ?? [])->where('type', 'summary')?->first()['summary'] ?? '',
            'description' => collect($data['method_attributes'] ?? [])->where('type', 'description')?->first()['description'] ?? '',
        ];

        if ($data['authorization'] && $this->config['auth']['has_auth']) {
            $this->data['paths'][$this->path][$this->method]['security'] = [
                [
                    $this->config['auth']['security_schema'] => $this->config['securitySchemes'][$this->config['auth']['security_schema']],
                ],
            ];
        }

        $this->addDataCode($data);
    }

    /**
     * Добавляет полную информацию по маршруту
     *
     *
     * @throws JsonException
     */
    protected function addDataPath(array $data): void
    {
        $this->data['paths'][$this->path] = [];

        $this->addDataMethod($data);
    }

    /**
     * Создание файла документации
     */
    protected function makeFile(string $type): void
    {
        switch ($type) {
            case 'json':
                File::put(
                    'storage/app/api-docs/'.config('l5-swagger.documentations.default.paths.docs_json'),
                    collect($this->data)->toJson()
                );
                break;
            case 'yaml':
                Storage::drive(config('swagger.storage.driver'))->put(
                    'api-docs/'.config('l5-swagger.documentations.default.paths.docs_yaml'),
                    Yaml::dump($this->data, 10, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK)
                );
                break;
            default:
                throw new Error('Unsupported format for documentation');
        }
    }

    /**
     * Получение токенов для авторизации
     *
     * @throws Throwable
     */
    protected function getTokenLine(): string
    {
        $action = config('swagger.auth.make_token.action');

        $is_class = class_exists($action);
        $is_function = function_exists($action);

        throw_unless(
            $is_class || $is_function,
            Error::class,
            "Action '$action' not found"
        );

        $token = $is_class ? (new $action)() : $action();

        if (is_array($token)) {
            $string = '';
            foreach ($token as $key => $value) {
                $string .= "<br><b>$key</b> - `$value` ";
            }

            return rtrim($string);
        }

        return "`$token`";
    }
}
