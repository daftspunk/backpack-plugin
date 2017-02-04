<?php namespace RainLab\Backpack\Classes;

use File;
use Cache;
use Input;
use Schema;
use System\Classes\PluginManager;
use DirectoryIterator;
use ApplicationException;
use Exception;

/**
 * Provides helper methods for Builder CMS components.
 *
 * @package rainlab\backpack
 * @author Alexey Bobkov, Samuel Georges
 */
class ComponentHelper
{
    use \October\Rain\Support\Traits\Singleton;

    protected $modelListCache = null;

    public function listGlobalModels()
    {
        if ($this->modelListCache !== null) {
            return $this->modelListCache;
        }

        $key = 'backpack-global-model-list';
        $cached = Cache::get($key, false);

        if ($cached !== false && ($cached = @unserialize($cached)) !== false) {
            return $this->modelListCache = $cached;
        }

        $manager = PluginManager::instance();
        $plugins = $manager->getPlugins();
        $plugins = array_keys($plugins);

        $result = [];
        foreach ($plugins as $pluginCode) {
            $path = $manager->getPluginPath($pluginCode) . '/models';

            $models = $this->listModelsFromPath($path);

            foreach ($models as $model) {
                $fullClassName = str_replace('.', '\\', $pluginCode) . '\Models' . '\\' . $model;
                $result[$fullClassName] = $pluginCode . ' - ' . $model;
            }

        }

        Cache::put($key, serialize($result), 1);

        return $this->modelListCache = $result;
    }

    public function listModelsFromPath($path)
    {
        $result = [];

        if (!File::isDirectory($path)) {
            return $result;
        }

        foreach (new DirectoryIterator($path) as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }

            if ($fileInfo->getExtension() != 'php') {
                continue;
            }

            $result[] = File::name($fileInfo->getFileName());
        }

        return $result;
    }

    public function getModelClassDesignTime()
    {
        $modelClass = trim(Input::get('modelClass'));

        if ($modelClass && !is_scalar($modelClass)) {
            throw new ApplicationException('Model class name should be a string.');
        }

        if (!strlen($modelClass)) {
            $models = $this->listGlobalModels();
            $modelClass = key($models);
        }

        if (!$this->validateModelClassName($modelClass, true)) {
            throw new ApplicationException('Invalid model class name.');
        }

        return $modelClass;
    }

    public function listModelColumnNames()
    {
        $modelClass = $this->getModelClassDesignTime();

        $key = 'backpack-global-model-list-'.md5($modelClass);
        $cached = Cache::get($key, false);

        if ($cached !== false && ($cached = @unserialize($cached)) !== false) {
            return $cached;
        }

        $model = new $modelClass();

        $tableName = $model->getTable();

        $columnNames = Schema::getColumnListing($tableName);

        $result = array_combine($columnNames, $columnNames);

        Cache::put($key, serialize($result), 1);

        return $result;
    }

    protected function validateModelClassName($className, $allowNamespaces = false)
    {
        if (!$allowNamespaces) {
            return preg_match('/^[A-Z]+[a-zA-Z0-9_]+$/', $className);
        }

        return preg_match('/^[A-Z]+[a-zA-Z0-9_\\\\]+$/', $className);
    }
}
