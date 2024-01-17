<?php

namespace Fillincode\Swagger\Parser;

use Illuminate\Support\Collection;

class BodyGenerator
{
    /**
     * @param  array  $data Данные с телом запроса
     */
    public function __construct(
        protected array $data = []
    ) {
    }

    /**
     * Формирует тело запроса
     */
    public function generateBody(array $data): array
    {
        if (empty($data['body'])) {
            return $this->data;
        }

        $query_params = collect($data['body']);
        $docblock = ! empty($data['form_request_attribute']) ? $data['form_request_attribute'] : [];
        $docblock = collect($docblock);

        $this->data['content']['application/json']['schema'] = $this->makeSchema($query_params, $docblock);

        $result = $this->data;
        $this->data = [];

        return $result;
    }

    /**
     * Генерирует схему тела запроса
     */
    protected function makeSchema(Collection $query_params, Collection $docblock): array
    {
        foreach ($query_params as $key => $example) {
            $dataFromDocblock = $docblock->where('name', $key)->first();

            $items[$key] = [
                'type' => $dataFromDocblock['property_type'] ?? 'string',
                'description' => $dataFromDocblock['description'] ?? '',
                'required' => $dataFromDocblock['required'] ?? false,
                'nullable' => isset($dataFromDocblock['required']) && ! $dataFromDocblock['required'],
            ];

            if ($items[$key]['type'] === 'enum') {
                $items[$key]['type'] = 'string';
                $items[$key]['enum'] = $dataFromDocblock['in'] ?? '';
            }

            if ($items[$key]['type'] === 'date') {
                $items[$key]['type'] = 'string';
                $items[$key]['format'] = 'date';
            }

            $examples[$key] = $example;
        }

        return [
            'properties' => $items ?? [],
            'example' => $examples ?? [],
            'type' => 'object',
        ];
    }
}
