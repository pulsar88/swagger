<?php

namespace Fillincode\Swagger\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD|\Attribute::TARGET_CLASS)]
class Group
{
    /**
     * @param  string  $group Группа метода
     */
    public function __construct(public string $group)
    {
    }
}
