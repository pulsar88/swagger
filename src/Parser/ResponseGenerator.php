<?php

namespace Fillincode\Swagger\Parser;

use Illuminate\Support\Arr;
use JsonException;

class ResponseGenerator
{
    /**
     * Код ответа
     */
    protected int $code;

    /**
     * @param  array  $data Данные для ответа
     * @param  array  $resourcesAttributes Атрибуты ресурсов
     * @param  array  $content Данные ответа запроса
     * @param  array  $testData Данные из файла
     */
    public function __construct(
        protected array $data = [],
        protected array $resourcesAttributes = [],
        protected array $content = [],
        protected array $testData = [],
    ) {
    }

    /**
     * Формирует данные об ответе
     *
     *
     * @throws JsonException
     */
    public function generateResponse(array $data, int $code): array
    {
        $this->code = $code;
        $this->testData = $data;
        $this->data['description'] = $this->makeDescriptionForCode();
        $this->data['content']['application/json']['schema']['type'] = 'object';
        $this->data['content']['application/json']['schema']['properties'] = [];

        if (empty($data['content']) || $this->code === 302) {
            return $this->data;
        }

        $this->content = (array) json_decode($this->testData['content'], false, 512, JSON_THROW_ON_ERROR);
        $this->resourcesAttributes = $data['resources_attributes'];
        $this->data['content']['application/json']['schema'] = $this->makeSchema();

        $result = $this->data;
        $this->data = [];
        $this->testData = [];

        return $result;
    }

    /**
     * Создает схему ответа
     */
    protected function makeSchema(): array
    {
        $schema = [];
        $schema['type'] = 'object';

        foreach ($this->content as $key => $value) {
            $type = gettype($value);

            $schema['properties'][$key] = $this->makeItems($type, $key, $value);
            $schema['example'][$key] = $this->makeExamples($type, $value);
        }

        return $schema;
    }

    /**
     * Возвращает описание кода
     */
    protected function makeDescriptionForCode(): string
    {
        $docblock = collect($this->testData['method_attributes'] ?? [])
            ->where('type', 'code')
            ->where('code', $this->code)
            ->first();

        if ($docblock) {
            return $docblock['description'];
        }

        return config('swagger.codes.'.$this->code) ?? '';
    }

    /**
     * Формирует схему контента
     */
    protected function makeItems(string $type, string $path, mixed $content): array
    {
        $parameter = $this->testData['resources_attributes'][$path][0] ?? null;

        $data = [
            'type' => $parameter['property_type'] ?? $type,
            'name' => $parameter['name'] ?? last(explode('.', $path)),
            'description' => $parameter['description'] ?? '',
        ];

        if (isset($parameter['required'])) {
            $data['required'] = $parameter['required'];
            $data['nullable'] = ! $parameter['required'];
        }

        if ($type === 'object') {
            $data['properties'] = $this->makePropertiesForObject($content, $path);
        }

        if ($type === 'array') {
            $data['items'] = $this->makeItemsForArray($content, $path);
        }

        return $data;
    }

    /**
     * Формирует пример данных
     */
    protected function makeExamples(string $type, mixed $value): mixed
    {
        if ($type === 'array' || $type === 'object') {
            $result = [];

            foreach ($value as $key => $item) {
                $result[$key] = $this->makeExamples(gettype($item), $item);
            }

            return $result;
        }

        return $value || in_array($value, [false, 0], true) ? $value : null;
    }

    /**
     * Формирует описание объекта
     */
    protected function makePropertiesForObject(object $object, string $path): array
    {
        $data = [];

        foreach ($object as $key => $property) {
            $path .= '.'.$key;
            $type = gettype($property);

            $data[$key] = $this->makeItems($type, $path, $property);

            $path = substr($path, 0, strrpos($path, '.', -1));
        }

        return $data;
    }

    /**
     * Формирует схему для массива
     *
     * @return array|string[]
     */
    protected function makeItemsForArray(array $data, string $path): array
    {
        if (count(array_filter($data, 'is_object'))) {
            return [
                'type' => 'object',
                'properties' => $this->makePropertiesForObject(Arr::first($data), $path),
            ];
        }

        return [
            'type' => 'string',
        ];
    }
}
