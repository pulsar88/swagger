<?php

namespace Fillincode\Swagger\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Group
{
    /**
     * @param  string  $group Группа метода
     */
    public function __construct(public string $group)
    {
    }
}
