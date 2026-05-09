<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Domain;
use App\Jobs\CheckDomainJob;

class CheckDomainsCommand extends Command
{
    protected $signature = 'domains:check';
    protected $description = 'Check all domains';

    public function handle(): void
    {
        Domain::chunk(100, function ($domains) {
            foreach ($domains as $domain) {
                dispatch(new CheckDomainJob($domain));
            }
        });
    }
}