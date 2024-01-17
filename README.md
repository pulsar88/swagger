# Swagger-parser

## Installation

```shell
composer require fillincode/swagger
```

Публикация конфигурации

```shell
php artisan vendor:publish --provider="Fillincode\Swagger\SwaggerServiceProvider"
```

## Config

### OpenApi version

```php
'openapi' => '3.0.0',
```

### Block configuration with information about your Api. You can define the title, description, version, terms of use, contact details and license

```php
'info' => [
    'title' => 'API documentation',
    'description' => 'API documentation',
    'version' => '1.0.0',
    'termsOfService' => 'https://example.com/terms',
    'contact' => [
        'name' => 'example',
        'url' => 'https://example.com',
        'email' => 'example@mail.ru',
    ],
    'license' => [
        'name' => 'CC Attribution-ShareAlike 4.0 (CC BY-SA 4.0)',
        'url' => 'https://openweathermap.org/price',
    ],
],
```

### Ready-made authorization schemes

```php
'securitySchemes' => [
    'passport' => [
        'type' => 'http',
        'in' => 'header',
        'name' => 'Authorization',
        'scheme' => 'Bearer',
        'description' => 'To authorize, use the key ',
    ],
    'sanctum' => [
        'type' => 'http',
        'in' => 'header',
        'name' => 'Authorization',
        'scheme' => 'Bearer',
        'description' => 'To authorize, use the key ',
    ],
],
```

### Information on the server to which requests will be sent

```php
'servers' => [
    [
        'url' => env('APP_URL'),
        'description' => 'Server for testing',
    ],
```

### Server authorization configuration

1. It is necessary to determine whether there are requests that require authorization
2. Middleware, which checks authorization
3. Select the desired authorization scheme
4. You need to define a function or Invokable class that will return the authorization token(s). You can return an array with a description of the token and its value or just a string token 

An example of such a class

```php
    public function __invoke(): string|array
    {
        $user = User::whereEmail('user@mail.ru')->first();

        return $user?->createToken('user-token')->accessToken;
    }

    ````

    public function __invoke(): string|array
    {
        $user = User::whereEmail('user@mail.ru')->first();
        $admin = User::whereEmail('admin@mail.ru')->first();

        return [
            'user' => $user?->createToken('user-token')->accessToken,
            'admin' => $admin?->createToken('admin-token')->accessToken
        ];
    }
```

### Authorization configuration

```php
'auth' => [
    'has_auth' => true,
    'middleware' => 'auth.api',
    'security_schema' => 'passport',
    'make_token' => [
        'action' => 'makeTokenFunction',
    ],
],
```

### Configuration for storing temporary files with test results. Driver and path can be determined

```php
'storage' => [
    'driver' => 'local',
    'path' => 'data',
],
```

### Configuration for resource keys

```php
'pre_key' => 'data',

'resources_keys' => [
    'has_pre_key' => false,
    'use_wrap' => true,
],
```

Examples of resource key configuration settings

For example, if there is an additional key that does not belong to a resource, the resources have keys, but they are not set via wrap

Response

```php
'data' => [
    'user' => [
        'id' => 12,
        'name' => 'user_name'
    ]
]
```

Resource

```php
    class UserResource extends JsonResource

    public function toArray(Request $request)
    {
        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->name
            ]           
        ]       
    }
```

Configuration

```php
'pre_key' => 'data',

'resources_keys' => [
    'has_pre_key' => true,
    'use_wrap' => false,
],
```

If there is no additional key in the response, and the resource uses the wrap property as a key

Response

```php
'user' => [
    'id' => 12,
    'name' => 'user_name'
]
```

Resource

```php
    class UserResource extends JsonResource

    public static $wrap = 'user';
    
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name        
        ]       
    }
```

Configuration

```php
'pre_key' => '',

'resources_keys' => [
    'has_pre_key' => true,
    'use_wrap' => true,
],
```

If the response contains just data without any keys

Response

```php
[
    'id' => 12,
    'name' => 'user_name'
]
```

Resource

```php
    class UserResource extends JsonResource
    
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name        
        ]       
    }

    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
```

Configuration

```php
'pre_key' => '',

'resources_keys' => [
    'has_pre_key' => false,
    'use_wrap' => false,
],
```

### Ready description of response codes. You can add your own response descriptions to this array

```php
'codes' => [
    200 => 'Request completed successfully',
    201 => 'Object created successfully',
    204 => 'Not content',
    401 => 'Not authentication',
    403 => 'Not authorization',
    404 => 'Not found',
    422 => 'Data validation error',
    500 => 'Server error',
],
```

## Commands

Clears the directory with test results

```shell
php artisan swagger:destroy
```

Generates a documentation file from all files with test results

```shell
php artisan swagger:parse
```

Runs tests, generates new documentation and deletes temporary files

```shell
php artisan swagger:generate
```

## Attributes

### Group

The attribute is used to group routes

Applicable:

1. Controller method

The parameter accepts:

1. Group name (string). __Required__

Example:

```php
    use Fillincode\Swagger\Attributes\Group;
    
    #[Group('group_name')]
    public function example_method()
    {
        //
    }
```

### Summary

Brief description of the route

Applicable:

1. Controller method

The parameter accepts:

1. Summary (string). __Required__

Example:

```php
    use Fillincode\Swagger\Attributes\Summary;
     
    #[Summary('summary')]
    public function example_method()
    {
        //
    }
```

### Description

Detailed description of the route

Applicable:

1. Controller method

The parameter accepts:

1. Description (string). __Required__

Example:

```php
    use Fillincode\Swagger\Attributes\Description;
     
    #[Description('description')]
    public function example_method()
    {
        //
    }
```

### Code

The attribute is needed for a custom code description

Applicable:

1. Controller method

The parameter accepts:

1. Code (integer). __Required__
2. Description code (string). __Required__

Example:

```php
    use Fillincode\Swagger\Attributes\Code;
     
    #[Code(201, 'Object update')]
    public function example_method()
    {
        //
    }
```

### PathParameter

The attribute is needed to describe the parameters that are passed in the address bar

Applicable:

1. Controller method

The parameter accepts:

1. Parameter name (string). __Required__
2. Type (string). __Required__
3. Description (string)
4. Available values (string|array). The parameter is only needed for the enum type 
5. Required (boolean). Is the parameter required. By default, true

Example:

```php
    use Fillincode\Swagger\Attributes\PathParameter;
     
    #[PathParameter('parameter_name', 'enum', 'description parameter', ['string_1', 'string_2', 12], false)]
    public function example_method()
    {
        //
    }
```

### QueryParameter

The attribute is needed to describe the parameters that are passed in the GET parameter

Applicable:

1. Controller method

The parameter accepts:

1. Parameter name (string). __Required__
2. Type (string). __Required__
3. Example (string/integer/boolean). __Required__
4. Description (string)
5. Available values (string|array). The parameter is only needed for the enum type
6. Required (boolean). Is the parameter required. By default, true

Example:

```php
    use Fillincode\Swagger\Attributes\QueryParameter;
     
    #[QueryParameter('parameter_name', 'string', 'example_string', 'description parameter')]
    public function example_method()
    {
        //
    }
```

### Resource

The attribute is used to indicate the resource that is returned in the response. This resource will collect information based on the response data.
To describe nested resources, you must define this attribute on the parent resource

Applicable:

1. Controller method
2. Resource class

The parameter accepts:

1. Class (string). __Required__
2. Name (string|null). This parameter is only needed if the resource object has a key, and it is different from the resource's wrap property.
3. use_wrap (boolean|null). If a resource uses wrap as a key, then you need to pass true (Can be activated for all resources in the configuration)
4. has_key (boolean|null). If the resource has a key, then you need to pass true (Can be configured for all resources in the configuration)

Example:

```php
    use Fillincode\Swagger\Attributes\Resource;
     
    #[Resource(ProjectResource::class)]
    public function example_method()
    {
        //
    }

    ```` 
    
    #[Resource(CategoryResource::class, 'categories')]
    class ExampleResource extends JsonResource
    {
        //
    }
```

### FormRequest

The attribute is needed to indicate the FormRequest class by which data will be collected to describe the parameters passed in the request

Applicable:

1. Controller method

The parameter accepts:

1. Class (string). __Required__

Example:

```php
    use Fillincode\Swagger\Attributes\FormRequest;
     
    #[FormRequest(FormRequest::class)]
    public function example_method()
    {
        //
    }
```

### Property

The attribute describes the parameter that is passed in the request or returned in the response

Applicable:

1. FormRequest class
2. Resource class

The parameter accepts:

1. Name (string). __Required__
2. Type (string). __Required__
3. Description (string).  
4. In (string|array). Available values, parameter needed only for enum type
5. is_required (bool). Default true

Example:

```php
    use Fillincode\Swagger\Attributes\Property;
     
    #[Property('age', 'integer', 'student age')]
    class ProjectRequest extends FormRequest
    {
        //
    }

    ````
    #[Property('age', 'integer', 'student age')]
    class ProjectResource extends JsonResource
    {
        //
    }
```

## Additionally

An example when a resource has an array whose keys need to be documented

```php
    use Fillincode\Swagger\Attributes\Property;
    
    #[Property('id', 'string', 'user id')]
    #[Property('data.info_1', 'string', 'user info 1')]
    #[Property('data.info_2', 'string', 'user info 2')]
    class UserResource extends JsonResource
    {
        public function toArray(Request $request)
        {
            return [
                'id' => $this->id,
                'data' => [
                    'info_1' => $this->data['info_1'],
                    'info_2' => $this->data['info_2'],
                ]       
            ]       
        }
    }
```