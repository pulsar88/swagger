<?php

namespace Fillincode\Swagger\Parser;

use Illuminate\Support\Collection;

class ParametersGenerator
{
    /**
     * @param  array  $data Данные с параметрами запроса
     */
    public function __construct(
        protected array $data = []
    ) {
    }

    /**
     * Формирует параметры к методу
     */
    public function generateParameters(array $data): array
    {
        $query_params = collect($data['body_query']);

        $methodAttributes = collect(! empty($data['method_attributes']) ? $data['method_attributes'] : []);

        if ($query_params->count()) {
            $this->makeParameters($query_params, $methodAttributes->where('type', 'path_parameter'));
        }

        $this->makeQueryParameters($methodAttributes->where('type', 'query_parameter'));

        $result = $this->data;
        $this->data = [];

        return $result;
    }

    /**
     * Формирует данные параметров запроса
     */
    protected function makeParameters(Collection $query_params, Collection $docblock): void
    {
        foreach ($query_params as $key => $parameter) {
            $param_docblock = $docblock->where('name', $key)->first();

            $param_data = [
                'name' => $key,
                'in' => 'path',
                'description' => $param_docblock['description'] ?? '',
                'required' => $param_docblock['required'] ?? false,
                'schema' => [
                    'type' => $param_docblock['param_type'] ?? 'string',
                ],
                'examples' => [
                    $param_docblock['param_type'] ?? 'string' => [
                        'value' => $parameter,
                    ],
                ],
            ];

            $this->data[] = $param_data;
        }
    }

    /**
     * Формирует данные по параметрам в адресной строке
     */
    protected function makeQueryParameters(Collection $query_parameters): void
    {
        foreach ($query_parameters as $parameter) {
            $schema['type'] = $parameter['type'];

            if ($parameter['type'] === 'enum') {
                $schema['in'] = (array) $parameter['in'];
            }

            $param_data = [
                'name' => $parameter['name'],
                'in' => 'query',
                'description' => $parameter['description'],
                'required' => $parameter['required'],
                'nullable' => ! $parameter['required'],
                'schema' => $schema,
                'examples' => [
                    $parameter['type'] => [
                        'value' => $parameter['example'],
                    ],
                ],
            ];

            $this->data[] = $param_data;
        }
    }
}
