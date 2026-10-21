<?php

use App\Helpers\Qs;
$request = request();

$req_uri1 = rtrim(preg_replace('/[0-9]+/', '', substr(strtok($_SERVER['REQUEST_URI'],'?'),1)),"/");
$req_uri2 = rtrim(preg_replace('/vms/i', '', $req_uri1),"/");
$page_id = \App\Models\SubMenu::where('m_controller_name','like','%'.substr($req_uri2,1).'%')->value('id');

if(Auth::user()->user_type != 'super_admin'){
  
       
     $sub_menu_ids = explode(',',Qs::getUserRole()->sub_menu_ids);

}

Qs::storelogs($request);

?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta id="csrf-token" name="csrf-token" content="{{ csrf_token() }}">
    <meta id="base_url" name="base_url" content="{{ url('/') }}">
    <meta id="user_id" name="user_id" content="{{ Auth::user()->id }}">
    <meta id="ip_address" name="ip_address" content="{{ Request::ip() }}">
    <title> @yield('page_title') | {{ config('app.name') }} </title>
    @include('partials.inc_top')
</head>

<body class="{{ in_array(Route::currentRouteName(), ['payments.invoice', 'marks.tabulation', 'marks.show', 'ttr.manage', 'ttr.show']) ? 'sidebar-xs' : '' }}">

@include('partials.top_menu')
<div class="page-content">
    @include('partials.menu')
    <div class="content-wrapper">
        @include('partials.header')
        <div class="content">
            {{--Error Alert Area--}}
            @if($errors->any())
                <div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        @foreach($errors->all() as $er)
                            <span><i class="icon-arrow-right5"></i> {{ $er }}</span> <br>
                        @endforeach
                </div>
            @endif
            <div id="ajax-alert" style="display: none"></div>

            @if(Auth::user()->user_type != 'super_admin')

                @if(in_array($page_id,$sub_menu_ids))

                    @yield('content')

                @else

                    <p style="color:red;">Permission denied...</p>

                @endif

            @else

                @yield('content')

            @endif
        </div>

    </div>
</div>

@include('partials.inc_bottom')
@yield('scripts')
</body>
</html>
