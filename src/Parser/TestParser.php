<?php

namespace Fillincode\Swagger\Parser;

use Fillincode\Swagger\Attributes\Code;
use Fillincode\Swagger\Attributes\Description;
use Fillincode\Swagger\Attributes\FormRequest;
use Fillincode\Swagger\Attributes\Group;
use Fillincode\Swagger\Attributes\PathParameter;
use Fillincode\Swagger\Attributes\Property;
use Fillincode\Swagger\Attributes\QueryParameter;
use Fillincode\Swagger\Attributes\Resource;
use Fillincode\Swagger\Attributes\Summary;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Yaml\Yaml;

class TestParser
{
    /**
     * Данные для формирования файла
     */
    protected array $data = [];

    /**
     * @param  TestResponse  $testResponse Ответ на запрос
     *
     * @throws ReflectionException
     */
    public function makeAutoDoc(TestResponse $testResponse): void
    {
        $route = request()->route();

        $this->data = [
            'code' => $testResponse->status(),
            'content' => $testResponse->getContent(),
            'authorization' => config('swagger.auth.has_auth') && in_array(config('swagger.auth.middleware'), $route?->middleware(), true),
            'path' => str_replace('?', '', $route?->uri),
            'body' => (array)json_decode(request()->getContent()),
            'body_query' => request()->route()->parameters(),
            'method' => $route?->methods()[0],
        ];

        $this->getAttributesInfo(
            explode('@', $route?->getActionName())
        );

        Storage::drive(config('swagger.storage.driver'))
            ->put(
                config('swagger.storage.path').'/'.$route->getName().Str::random(10).'.yaml',
                Yaml::dump($this->data)
            );
    }

    /**
     * Сбор данных по атрибутам
     *
     * @throws ReflectionException
     */
    private function getAttributesInfo(array $actionName): void
    {
        $controller = new ReflectionClass($actionName[0]);
        $method = $controller->getMethod($actionName[1]);

        foreach ($method->getAttributes() as $attribute) {
            $result = $this->parseAttribute($attribute);

            if ($result) {
                $this->data['method_attributes'][] = $result;
            }
        }

        $this->parseFormRequest(
            Arr::first($method->getAttributes(FormRequest::class))?->newInstance()
        );

        $this->data['resources_attributes'] = [];

        foreach ($method->getAttributes(Resource::class) as $attribute) {
            $this->data['resources_attributes'] = array_merge(
                $this->data['resources_attributes'],
                $this->parseResource(
                    $attribute?->newInstance(),
                    config('swagger.pre_key')
                )
            );
        }
    }

    /**
     * Собирает данные по formRequest
     *
     *
     * @throws ReflectionException
     */
    private function parseFormRequest(?object $attribute): void
    {
        if (! $attribute) {
            return;
        }

        foreach ((new ReflectionClass($attribute->form_request))->getAttributes() as $reflectionAttribute) {
            $result = $this->parseAttribute($reflectionAttribute);

            if ($result) {
                $this->data['form_request_attribute'][] = $result;
            }
        }
    }

    /**
     * Собирает данные по ресурсам
     *
     *
     * @throws ReflectionException
     */
    private function parseResource(?object $attribute, string $preString = ''): array
    {
        $data = [];

        if (! $attribute) {
            return $data;
        }

        $reflectionClass = (new ReflectionClass($attribute->path));

        if (! empty($preString)) {
            $preString .= '.'.$attribute->name;
        }

        foreach ($reflectionClass->getAttributes() as $reflectionAttribute) {
            $result = $this->parseAttribute($reflectionAttribute);

            if ($result) {
                $path = $preString.(! empty($result['name']) ? '.'.$result['name'] : '');

                $data[$path][] = $result;
            }
        }

        foreach ($reflectionClass->getAttributes(Resource::class) as $nestedAttribute) {
            $nestedAttribute = $nestedAttribute->newInstance();

            $data = array_merge(
                $data,
                $this->parseResource($nestedAttribute, $preString)
            );
        }

        return $data;
    }

    /**
     * Получение данных из атрибута
     */
    protected function parseAttribute(ReflectionAttribute $object): array|false
    {
        $object = $object->newInstance();

        $types = [
            Code::class => 'code',
            Description::class => 'description',
            Property::class => 'property',
            Group::class => 'group',
            Summary::class => 'summary',
            PathParameter::class => 'path_parameter',
            QueryParameter::class => 'query_parameter',
        ];

        $object->type = $types[get_class($object)] ?? '';

        return $object->type ? get_object_vars($object) : false;
    }
}
