<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit05e69844b1b6cf1131c17cc2e3e30890
{
    public static $classMap = array (
        'Ps_ImageSlider' => __DIR__ . '/../..' . '/ps_imageslider.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit05e69844b1b6cf1131c17cc2e3e30890::$classMap;

        }, null, ClassLoader::class);
    }
}
