<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{

    public function __construct()
    {
        if(env('APP_ENV') == 'local'){
            \Artisan::call('view:clear');
        }
    }

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

}

