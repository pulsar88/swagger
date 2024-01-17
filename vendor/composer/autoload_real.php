<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit26e3f4aa3e59555f265c5e6d6f7ab8bf
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit26e3f4aa3e59555f265c5e6d6f7ab8bf', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit26e3f4aa3e59555f265c5e6d6f7ab8bf', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit26e3f4aa3e59555f265c5e6d6f7ab8bf::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
