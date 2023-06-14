<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9098624a1f028d02efa9d8de1a463b61
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/App',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9098624a1f028d02efa9d8de1a463b61::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9098624a1f028d02efa9d8de1a463b61::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit9098624a1f028d02efa9d8de1a463b61::$classMap;

        }, null, ClassLoader::class);
    }
}
