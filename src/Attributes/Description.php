<?php

namespace Fillincode\Swagger\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Description
{
    /**
     * @param  string  $description Подробное описание метода
     */
    public function __construct(public string $description)
    {

    }
}
