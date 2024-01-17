<?php

namespace Fillincode\Swagger\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Code
{
    /**
     * @param  string  $code        Код
     * @param  string  $description Описание кода
     */
    public function __construct(
        public string $code,
        public string $description
    ) {
    }
}
