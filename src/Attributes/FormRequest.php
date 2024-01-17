<?php

namespace Fillincode\Swagger\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class FormRequest
{
    /**
     * @param  string  $form_request Путь к formRequest
     */
    public function __construct(public string $form_request)
    {
    }
}
