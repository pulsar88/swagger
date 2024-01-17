<?php

namespace Fillincode\Swagger\Attributes;

use Attribute;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Resource
{
    /**
     * Если имя ресурса не предается, то оно будет получено автоматически
     *
     * @param  string  $path Путь к ресурсу
     * @param  string|null  $name Название ресурса
     * @param  bool  $use_wrap Использовать wrap в качестве имени
     * @param  bool|null  $has_key Есть ли ключи у того ресурса
     *
     * @throws ReflectionException
     */
    public function __construct(
        public string $path,
        public ?string $name = null,
        public ?bool $use_wrap = null,
        public ?bool $has_key = null
    ) {
        $this->use_wrap = $use_wrap ?: config('swagger.resources_keys.use_wrap');
        $this->has_key = $has_key ?: config('swagger.resources_keys.has_pre_key');
        $this->name = $this->makeName();
    }

    /**
     * Создает названия для ресурса
     *
     * @throws ReflectionException
     */
    protected function makeName(): string
    {
        if (! $this->has_key) {
            return '';
        }

        if ($this->use_wrap) {
            return (new ReflectionClass($this->path))->getProperty('wrap');
        }

        if (! $this->name) {
            $array = explode('\\', $this->path);

            return strtolower(
                Str::snake(
                    str_replace(['Resource', 'Collection'], '', end($array))
                )
            );
        }

        return $this->name;
    }
}
