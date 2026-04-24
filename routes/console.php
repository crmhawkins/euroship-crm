<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Euroship CRM ready.');
})->purpose('Display a greeting');
