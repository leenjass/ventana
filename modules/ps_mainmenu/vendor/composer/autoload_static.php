<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfbae4b5e4cbb1302d7bdbd16cf8e9239
{
    public static $classMap = array (
        'Ps_MainMenu' => __DIR__ . '/../..' . '/ps_mainmenu.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitfbae4b5e4cbb1302d7bdbd16cf8e9239::$classMap;

        }, null, ClassLoader::class);
    }
}
