<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf166c7ee87056cf77c63303920c42a51
{
    public static $classMap = array (
        'DG\\ComposerCleaner\\Cleaner' => __DIR__ . '/..' . '/dg/composer-cleaner/src/ComposerCleaner/Cleaner.php',
        'DG\\ComposerCleaner\\Plugin' => __DIR__ . '/..' . '/dg/composer-cleaner/src/ComposerCleaner/Plugin.php',
        'RgPSCEModuleForm' => __DIR__ . '/../..' . '/classes/module/RgPSCEModuleForm.php',
        'RgPSCEModuleFormCatalog' => __DIR__ . '/../..' . '/classes/module/form/RgPSCEModuleFormCatalog.php',
        'RgPSCEModuleFormCron' => __DIR__ . '/../..' . '/classes/module/form/RgPSCEModuleFormCron.php',
        'RgPSCEModuleFormDashboard' => __DIR__ . '/../..' . '/classes/module/form/RgPSCEModuleFormDashboard.php',
        'RgPSCEModuleFormDatabase' => __DIR__ . '/../..' . '/classes/module/form/RgPSCEModuleFormDatabase.php',
        'RgPSCEModuleFormImages' => __DIR__ . '/../..' . '/classes/module/form/RgPSCEModuleFormImages.php',
        'RgPSCEModuleFormOrderscustomers' => __DIR__ . '/../..' . '/classes/module/form/RgPSCEModuleFormOrderscustomers.php',
        'RgPSCETools' => __DIR__ . '/../..' . '/classes/RgPSCETools.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitf166c7ee87056cf77c63303920c42a51::$classMap;

        }, null, ClassLoader::class);
    }
}