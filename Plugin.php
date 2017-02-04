<?php namespace RainLab\Backpack;

use Backend;
use System\Classes\PluginBase;

/**
 * Backpack Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Backpack',
            'description' => 'A collection of useful front-end components',
            'author'      => 'RainLab',
            'icon'        => 'icon-square'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'RainLab\Backpack\Components\RecordList'    => 'recordList',
            'RainLab\Backpack\Components\RecordDetails' => 'recordDetails',
            'RainLab\Backpack\Components\Resources'     => 'resources',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'rainlab.backpack.some_permission' => [
                'tab' => 'Backpack',
                'label' => 'Some permission'
            ],
        ];
    }
}
