<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit26e3f4aa3e59555f265c5e6d6f7ab8bf
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Fillincode\\Swagger\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Fillincode\\Swagger\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit26e3f4aa3e59555f265c5e6d6f7ab8bf::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit26e3f4aa3e59555f265c5e6d6f7ab8bf::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit26e3f4aa3e59555f265c5e6d6f7ab8bf::$classMap;

        }, null, ClassLoader::class);
    }
}
