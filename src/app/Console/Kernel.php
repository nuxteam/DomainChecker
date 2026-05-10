<?php 

protected function schedule(Schedule $schedule): void
{
    $schedule->command('domains:check')->everyFiveMinutes();
}