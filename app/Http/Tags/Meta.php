<?php

namespace App\Http\Tags;

use Inertia\Inertia;
use URL;

class Meta
{
    protected static $meta = [];

    public static function addMeta($name, $content)
    {
        static::$meta[$name] = $content;
        if ($name == 'title') {
            static::$meta['og:title'] = $content;
            static::$meta['twitter:title'] = $content;
        }
        if ($name == 'description') {
            static::$meta['og:description'] = $content;
            static::$meta['twitter:description'] = $content;
        }
        Inertia::share(['meta' => static::meta()]);
    }


    public static function meta()
    {
        return [
            'twitter:site' => settings('site.app_name', 'Betn'),
            'og:site_name' => settings('site.app_name', 'Betn'),
            'og:type' => settings('site.app_name', 'Betn'),
            'twitter:card' => 'summary_large_image',
            'twitter:url' => URL::to('/'),
            'og:url' => URL::to('/'),
            ...static::$meta,
        ];
    }

    public static function render()
    {
        $html = '';
        $mta = static::meta();
        $title = isset($mta['title']) ? $mta['title'] . ' | ' . config('app.name') : config('app.name');
        $html .= '<title inertia>' . $title . '</title>' . PHP_EOL;
        foreach ($mta as $name => $content) {
            $html .= '<meta name="' . $name . '" content="' . $content . '" inertia />' . PHP_EOL;
        }
        //static::$meta = [];
        return $html;
    }
}
