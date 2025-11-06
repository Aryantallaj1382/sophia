<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('private-chat.type', function () {
    \Log::info('dsfdf');
    return 1;
});
