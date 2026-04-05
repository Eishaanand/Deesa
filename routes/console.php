<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('app:refresh-demo', function (): void {
    $this->call('migrate:fresh', ['--seed' => true]);
})->purpose('Refresh the demo dataset for the UCAT platform.');
