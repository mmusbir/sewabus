<?php

if (!function_exists('setting')) {
    function setting($key, $default = null) {
        return \App\Models\Setting::getValue($key, $default);
    }
}
