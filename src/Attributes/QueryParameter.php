<?php

namespace Fillincode\Swagger\Attributes;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
class QueryParameter
{
    /**
     * @param  string  $name        Имя параметра
     * @param  string  $type        Тип параметра
     * @param  string  $example     Пример
     * @param  string  $description Описание параметра
     * @param  string|array  $in          Возможные варианты параметра (нужен для enum типа)
     * @param  bool  $required    Обязательный или нет
     */
    public function __construct(
        public string $name,
        public string $type,
        public mixed $example,
        public string $description = '',
        public string|array $in = '',
        public bool $required = true
    ) {
    }
}
