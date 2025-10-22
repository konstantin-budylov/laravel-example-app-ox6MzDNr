<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('imports', function () {
    return true;
});
