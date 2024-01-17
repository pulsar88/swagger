<?php

namespace Fillincode\Swagger\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Summary
{
    /**
     * @param  string  $summary Короткое описание метода
     */
    public function __construct(public string $summary)
    {
    }
}
