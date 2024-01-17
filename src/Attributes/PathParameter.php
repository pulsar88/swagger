<?php

namespace Fillincode\Swagger\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class PathParameter
{
    /**
     * @param  string  $name        Имя параметра
     * @param  string  $type        Тип параметра
     * @param  string  $description Описание параметра
     * @param  string|array  $in          Возможные варианты параметра (нужен для enum типа)
     * @param  bool  $required    Обязательный или нет
     */
    public function __construct(
        public string $name,
        public string $type,
        public string $description = '',
        public string|array $in = '',
        public bool $required = true
    ) {
    }
}
