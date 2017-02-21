<?php namespace RainLab\Backpack\Components;

use Config;
use Cms\Classes\ComponentBase;
use System\Classes\CombineAssets;

/**
 * Assets component
 */
class Resources extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'           => 'Resources',
            'description'    => 'Easily reference theme assets for inclusion on a page.',
        ];
    }

    public function defineProperties()
    {
        return [
            'js' => [
                'title'       => 'js',
                'description' => 'JavaScript file(s) in the assets/js folder',
                'type'        => 'stringList',
            ],
            'less' => [
                'title'       => 'less',
                'description' => 'LESS file(s) in the assets/less folder',
                'type'        => 'stringList',
            ],
            'css' => [
                'title'       => 'css',
                'description' => 'Stylesheet file(s) in the assets/css folder',
                'type'        => 'stringList',
            ],
            'vars' => [
                'title'       => 'vars',
                'description' => 'Variables name(s) and value(s)',
                'type'        => 'dictionary',
            ]
        ];
    }

    public function init()
    {
        $this->assetPath = themes_path()
            .'/'
            .$this->getTheme()->getDirName()
            .'/assets';
    }

    public function onRun()
    {
        /*
         * JavaScript
         */
        $javascript = [];
        if ($assets = $this->property('js')) {
            $javascript += array_map([$this, 'prefixJs'], (array) $assets);
        }

        /*
         * Stylesheets
         */
        $stylesheet = [];
        if ($assets = $this->property('less')) {
            $stylesheet += array_map([$this, 'prefixLess'], (array) $assets);
        }

        if ($assets = $this->property('css')) {
            $stylesheet += array_map([$this, 'prefixCss'], (array) $assets);
        }

        if (count($javascript)) {
            $this->addJs(CombineAssets::combine($javascript, $this->assetPath));
        }

        if (count($stylesheet)) {
            $this->addCss(CombineAssets::combine($stylesheet, $this->assetPath));
        }

        /*
         * Variables
         */
        if ($vars = $this->property('vars')) {
            foreach ((array) $vars as $key => $value) {
                $this->page[$key] = $value;
            }
        }
    }

    protected function prefixJs($value)
    {
        return 'javascript/'.trim($value);
    }

    protected function prefixCss($value)
    {
        return 'css/'.trim($value);
    }

    protected function prefixLess($value)
    {
        return 'less/'.trim($value);
    }
}
