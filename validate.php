<?php

use Illuminate\Support\Collection;

include __DIR__ . '/vendor/autoload.php';

$exportUrl = "https://api.streamkit.gg/v1/giveaways/{$argv[1]}/export";

$export = json_decode(file_get_contents($exportUrl), false);

$users = new Collection($export->users);
$pool = new Collection();

$users->each(function ($user) use ($pool) {
    $amount = $user->pivot->tickets;

    for ($i = 0; $i < $amount; $i++) {
        $pool->push($user->id);
    }
});

$winnerId = $pool->shuffle($export->giveaway->seed)->first();
$winner = $users->where('id', '===', $winnerId)->first();

$giveawayHash = sha1($export->giveaway->id . $export->giveaway->seed . $export->giveaway->secret);

if($giveawayHash === $argv[2]) {
    print_r('Hash is: valid' . PHP_EOL);
} else {
    print_r('Hash is: invalid' . PHP_EOL);
}

print_r('The winner is: ' . $winner->name);
