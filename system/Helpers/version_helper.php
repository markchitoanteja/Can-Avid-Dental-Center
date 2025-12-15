<?php

use Config\App;

if (! function_exists('app_version')) {
    /**
     * Returns the application version from Config\App.
     *
     * @return string
     */
    function app_version(): string
    {
        $config = new App();
        return $config->app_version;
    }
}
