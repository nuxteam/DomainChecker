<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Domain;
use App\Jobs\CheckDomainJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Domain::where('auto_check', true)
        ->get()
        ->each(function ($domain) {
            $last = $domain->checks()->latest()->first();

            $due = !$last
                || $last->created_at->diffInMinutes(now()) >= $domain->interval;

            \Log::info('CHECK', ['domain' => $domain->id, 'due' => $due]);

            if ($due) {
                dispatch(new CheckDomainJob($domain)); 
            }
        });

})->everyMinute();