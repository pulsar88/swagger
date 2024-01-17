<?php

namespace Fillincode\Swagger\Attributes;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class Property
{
    /**
     * @param  string  $name          Имя параметра
     * @param  string  $property_type Тип параметра
     * @param  string  $description   Описание параметра
     * @param  string|array  $in            Возможные варианты параметра (нужен для enum типа)
     * @param  bool  $required      Обязательный или нет
     */
    public function __construct(
        public string $name,
        public string $property_type,
        public string $description = '',
        public string|array $in = '',
        public bool $required = true
    ) {
    }
}
