<?php

Route::prefix('cms')->group(function() {

    Route::get('/bibleref/parse/{term}', [AscentCreative\CMS\Controllers\BibleRefController::class, 'parse']);

});