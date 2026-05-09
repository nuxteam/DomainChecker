<?php
namespace App\Jobs;

use App\Models\Domain;
use App\Services\DomainCheckService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class CheckDomainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(public Domain $domain) {}

    public function handle(DomainCheckService $service): void
    {
        $domain = $this->domain->fresh(['user']);

        if (!$domain) return;

        $prevCheck = $domain->checks()->latest()->first();

        $result = $service->check($domain);

        $check = $domain->checks()->create([
            'status_code'   => $result['code'],
            'response_time' => (int) ($result['time'] * 1000),
            'is_up'         => $result['ok'],
            'error'         => $result['error'],
        ]);

        if (
            $domain->notify_on_down &&
            $prevCheck !== null &&
            $prevCheck->is_up === true &&
            $check->is_up === false
        ) {
            $this->sendTelegramAlert($domain, $check);
        }
    }

    private function sendTelegramAlert(Domain $domain, $check): void
    {
        $user = $domain->user;

        if (!$user?->telegram_token || !$user?->telegram_chat_id) return;

        Http::post("https://api.telegram.org/bot{$user->telegram_token}/sendMessage", [
            'chat_id' => $user->telegram_chat_id,
            'text'    => "DOMAIN DOWN\nURL: {$domain->url}\nCode: {$check->status_code}\nError: {$check->error}",
        ]);
    }
}