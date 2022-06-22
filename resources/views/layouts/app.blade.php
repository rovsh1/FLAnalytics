<?php
if(Auth::check()){


    $roles = Auth::user()->roles()->get();
    if(isset($roles[0])){
        $permissions = $roles[0]->permissions()->get();
        $fixinglist = false;
        $ustabor = false;
        foreach ($permissions as $item){
            if(strpos($item->slug, 'fixinglist') !==false){
                $fixinglist = true;
            }
            if(strpos($item->slug, 'ustabor') !==false){
                $ustabor = true;
            }
        }
    }

    if(strpos(request()->route()->uri, 'ustabor')!==false){
        $prefix = 'ustabor';
    }else{
        $prefix = 'fixinglist';
    }

    $fixinglistUrl = str_replace('/ustabor', '/fixinglist',request()->url());
    $ustaborUrl = str_replace('/fixinglist', '/ustabor',request()->url());

    $query  = request()->query();
}
?>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>@yield('template_title')</title>
    <!-- Bootstrap core CSS-->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Page level plugin CSS-->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/ui/calendar.css') }}" rel="stylesheet">
    @yield('template_linked_css')
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary fixed-top" id="mainNav">
    <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">

            @auth
            @if(Auth::user()->canDo($prefix))
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Home">
                    <a class="nav-link {{ (Request::is('fixinglist') || Request::is('ustabor')) ? 'active' : ''  }}" href="{{ route($prefix) }}"><i class="fa fa-dashboard fa-lg fa-fw sidebar-icon"></i><span class="nav-link-text">Home</span></a>
                </li>
            @endif
            <?php if(Auth::check() && (Auth::user()->canDo($prefix.'.categories.main')||Auth::user()->canDo($prefix.'.categories.tech') || Auth::user()->canDo($prefix.'.categories.auto') )): ?>
            <li  class="nav-item" data-toggle="tooltip" data-placement="right" title="Categories">
                <a class="nav-link nav-link-collapse {{ Request::is('*/categories*') ? '' : 'collapsed'  }}" data-toggle="collapse" href="#manage" data-parent="#exampleAccordion"><i class="fa fa-bar-chart fa-lg fa-fw sidebar-icon"></i> <span class="nav-link-text">Categories</span> <span class="arrow"></span></a>
                <ul class="sidenav-second-level collapse {{ Request::is('*/categories*') ? 'show' : ''  }}" id="manage">
                    @if(Auth::user()->canDo($prefix.'.categories.main'))
                        <li><a  {{ Request::is('*/categories') ? 'class=active' : ''  }} href="<?= route($prefix.'.categories') ?>"><i class="fa fa-angle-double-right"></i>Main</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.categories.tech'))
                        <li><a {{ Request::is('*/categories/tech*') ? 'class=active' : ''  }} href="<?= route($prefix.'.categories.query',  ['query' => 'tech']) ?>"><i class="fa fa-angle-double-right"></i>Tech</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.categories.auto'))
                        <li><a {{ Request::is('*/categories/auto*') ? 'class=active' : ''  }} href="<?= route($prefix.'.categories.query', ['query' => 'auto']) ?>"><i class="fa fa-angle-double-right"></i>Auto</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.categories.home'))
                        <li><a {{ Request::is('*/categories/home*') ? 'class=active' : ''  }} href="<?= route($prefix.'.categories.query', ['query' => 'home']) ?>"><i class="fa fa-angle-double-right"></i>Home</a></li>
                    @endif
                </ul>
            </li>
            <?php endif;?>

            <?php if(Auth::check() && (Auth::user()->canDo($prefix.'.catalog.main')||Auth::user()->canDo($prefix.'.catalog.tech') || Auth::user()->canDo($prefix.'.catalog.auto') )): ?>
            <li  class="nav-item" data-toggle="tooltip" data-placement="right" title="Catalog">
                <a class="nav-link nav-link-collapse {{ Request::is('*/catalog*') ? '' : 'collapsed'  }}" data-toggle="collapse" href="#catalog" data-parent="#exampleAccordion"><i class="fa fa-align-center fa-lg fa-fw sidebar-icon"></i> <span class="nav-link-text">Catalog</span> <span class="arrow"></span></a>
                <ul class="sidenav-second-level collapse {{ Request::is('*/catalog*') ? 'show' : ''  }}" id="catalog">
                    @if(Auth::user()->canDo($prefix.'.catalog.main'))
                        <li><a {{ Request::is('*/catalog') ? 'class=active' : ''  }} href="<?= route($prefix.'.catalog') ?>"><i class="fa fa-angle-double-right"></i>Main</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.catalog.tech'))
                        <li><a {{ Request::is('*/catalog/tech*') ? 'class=active' : ''  }} href="<?= route($prefix.'.catalog.query',  ['query' => 'tech']) ?>"><i class="fa fa-angle-double-right"></i>Tech</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.catalog.auto'))
                        <li><a {{ Request::is('*/catalog/auto*') ? 'class=active' : ''  }} href="<?= route($prefix.'.catalog.query', ['query' => 'auto']) ?>"><i class="fa fa-angle-double-right"></i>Auto</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.catalog.home'))
                        <li><a {{ Request::is('*/catalog/home*') ? 'class=active' : ''  }} href="<?= route($prefix.'.catalog.query', ['query' => 'home']) ?>"><i class="fa fa-angle-double-right"></i>Home</a></li>
                    @endif
                </ul>
            </li>
            <?php endif;?>

            <?php if(Auth::check() && (Auth::user()->canDo($prefix.'.master.main')||Auth::user()->canDo($prefix.'.master.tech') || Auth::user()->canDo($prefix.'.master.auto') )): ?>
            <li  class="nav-item" data-toggle="tooltip" data-placement="right" title="Master">
                <a class="nav-link nav-link-collapse {{ Request::is('*/master*') ? '' : 'collapsed'  }}" data-toggle="collapse" href="#master" data-parent="#exampleAccordion"><i class="fa fa-wrench fa-lg fa-fw sidebar-icon"></i>  <span class="nav-link-text">Master</span> <span class="arrow"></span></a>
                <ul class="sidenav-second-level collapse {{ Request::is('*/master*') ? 'show' : ''  }}" id="master">
                    @if(Auth::user()->canDo($prefix.'.master.main'))
                        <li><a {{ Request::is('*/master') ? 'class=active' : ''  }} href="<?= route($prefix.'.master') ?>"><i class="fa fa-angle-double-right"></i>Main</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.master.tech'))
                        <li><a {{ Request::is('*/master/tech*') ? 'class=active' : ''  }} href="<?= route($prefix.'.master.query',  ['query' => 'tech']) ?>"><i class="fa fa-angle-double-right"></i>Tech</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.master.auto'))
                        <li><a {{ Request::is('*/master/auto*') ? 'class=active' : ''  }} href="<?= route($prefix.'.master.query', ['query' => 'auto']) ?>"><i class="fa fa-angle-double-right"></i>Auto</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.master.home'))
                        <li><a {{ Request::is('*/master/home*') ? 'class=active' : ''  }} href="<?= route($prefix.'.master.query', ['query' => 'home']) ?>"><i class="fa fa-angle-double-right"></i>Home</a></li>
                    @endif
                </ul>
            </li>

            <?php endif;?>
                <li  class="nav-item" data-toggle="tooltip" data-placement="right" title="Заявки">
                    <a class="nav-link nav-link-collapse {{ Request::is('*/requests*') ? '' : 'collapsed'  }}" data-toggle="collapse" href="#requests" data-parent="#exampleAccordion"><i class="fa fa-wrench fa-lg fa-fw sidebar-icon"></i>  <span class="nav-link-text">Заявки</span> <span class="arrow"></span></a>
                    <ul class="sidenav-second-level collapse {{ Request::is('*/requests*') ? 'show' : ''  }}" id="requests">
                        <li><a {{ Request::is('*/requests') ? 'class=active' : ''  }} href="<?= route($prefix.'.requests') ?>"><i class="fa fa-angle-double-right"></i>Main</a></li>
                        <li><a {{ Request::is('*/requests/tech*') ? 'class=active' : ''  }} href="<?= route($prefix.'.requests.query',  ['query' => 'tech']) ?>"><i class="fa fa-angle-double-right"></i>Tech</a></li>
                        <li><a {{ Request::is('*/requests/auto*') ? 'class=active' : ''  }} href="<?= route($prefix.'.requests.query', ['query' => 'auto']) ?>"><i class="fa fa-angle-double-right"></i>Auto</a></li>
                        <li><a {{ Request::is('*/requests/home*') ? 'class=active' : ''  }} href="<?= route($prefix.'.requests.query', ['query' => 'home']) ?>"><i class="fa fa-angle-double-right"></i>Home</a></li>
                    </ul>
                </li>

            @if(Auth::user()->canDo($prefix.'.users'))
                <li class="nav-item" data-toggle="tooltip" data-placement="right" >
                    <a class="nav-link {{ Request::is('*/users*') ? 'active' : ''  }}" href="<?= route($prefix.'.users') ?>"><i class="fa fa-users fa-lg fa-fw sidebar-icon"></i><span class="nav-link-text">Users</span></a>
                </li>
            @endif
            @if(Auth::user()->canDo($prefix.'.clicks'))
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" >
                    <a class="nav-link {{Request::is('*/clicks*') ? 'active' : ''  }}"  href="<?= route($prefix.'.clicks') ?>"><i class="fa fa-check fa-lg fa-fw sidebar-icon"></i><span class="nav-link-text">Clicks</span></a>
                </li>
            @endif
            @if(Auth::user()->canDo($prefix.'.bounces'))
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" >
                    <a class="nav-link {{Request::is('*/bounces*') ? 'active' : ''  }}" href="<?= route($prefix.'.bounces') ?>"><i class="fa fa-sign-out fa-lg fa-fw sidebar-icon"></i><span class="nav-link-text">Bounces</span></a>
                </li>
            @endif

            <?php if(Auth::check() && (Auth::user()->canDo($prefix.'.channels.main')||Auth::user()->canDo($prefix.'.channels.tech') || Auth::user()->canDo($prefix.'.channels.auto') )): ?>
            <li  class="nav-item" data-toggle="tooltip" data-placement="right">
                <a class="nav-link nav-link-collapse {{ Request::is('*/channels*') ? '' : 'collapsed'  }}" data-toggle="collapse" href="#channels" data-parent="#exampleAccordion"><i class="fa fa-list-ul  fa-lg fa-fw sidebar-icon"></i> <span class="nav-link-text">Channels</span> <span class="arrow"></span></a>
                <ul class="sidenav-second-level collapse {{ Request::is('*/channels*') ? 'show' : ''  }}" id="channels">
                    @if(Auth::user()->canDo($prefix.'.channels.main'))
                        <li><a {{ Request::is('*/channels') ? 'class=active' : ''  }} href="<?= route($prefix.'.channels') ?>"><i class="fa fa-angle-double-right"></i>Main</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.channels.tech'))
                        <li><a {{ Request::is('*/channels/tech*') ? 'class=active' : ''  }} href="<?= route($prefix.'.channels.query',  ['query' => 'tech']) ?>"><i class="fa fa-angle-double-right"></i>Tech</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.channels.auto'))
                        <li><a {{ Request::is('*/channels/auto*') ? 'class=active' : ''  }} href="<?= route($prefix.'.channels.query', ['query' => 'auto']) ?>"><i class="fa fa-angle-double-right"></i>Auto</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.channels.home'))
                        <li><a {{ Request::is('*/channels/home*') ? 'class=active' : ''  }} href="<?= route($prefix.'.channels.query', ['query' => 'home']) ?>"><i class="fa fa-angle-double-right"></i>Home</a></li>
                    @endif
                </ul>
            </li>

            <?php endif;?>
            <?php if(Auth::check() && (Auth::user()->canDo($prefix.'.blog.main')||Auth::user()->canDo($prefix.'.blog.tech') || Auth::user()->canDo($prefix.'.blog.auto') )): ?>
                <li  class="nav-item" data-toggle="tooltip" data-placement="right">
                <a class="nav-link nav-link-collapse {{ Request::is('*/blog*') ? '' : 'collapsed'  }}" data-toggle="collapse" href="#blog" data-parent="#exampleAccordion"><i class="fa fa-book   fa-lg fa-fw sidebar-icon"></i> <span class="nav-link-text">Blog</span> <span class="arrow"></span></a>
                <ul class="sidenav-second-level collapse {{ Request::is('*/blog*') ? 'show' : ''  }}" id="blog">
                    @if(Auth::user()->canDo($prefix.'.blog.main'))
                        <li><a {{ Request::is('*/blog') ? 'class=active' : ''  }} href="<?= route($prefix.'.blog') ?>"><i class="fa fa-angle-double-right"></i>Main</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.blog.tech'))
                        <li><a {{ Request::is('*/blog/tech*') ? 'class=active' : ''  }} href="<?= route($prefix.'.blog.query',  ['query' => 'tech']) ?>"><i class="fa fa-angle-double-right"></i>Tech</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.blog.auto'))
                        <li><a {{ Request::is('*/blog/auto*') ? 'class=active' : ''  }}  href="<?= route($prefix.'.blog.query', ['query' => 'auto']) ?>"><i class="fa fa-angle-double-right"></i>Auto</a></li>
                    @endif
                    @if(Auth::user()->canDo($prefix.'.blog.home'))
                        <li><a {{ Request::is('*/blog/home*') ? 'class=active' : ''  }}  href="<?= route($prefix.'.blog.query', ['query' => 'home']) ?>"><i class="fa fa-angle-double-right"></i>home</a></li>
                    @endif
                </ul>
            </li>

            <?php endif;?>

                <?php if(Auth::check() && (Auth::user()->canDo($prefix.'.mobile.main')||Auth::user()->canDo($prefix.'.mobile.tech') || Auth::user()->canDo($prefix.'.mobile.auto') )): ?>
                <li  class="nav-item" data-toggle="tooltip" data-placement="right">
                    <a class="nav-link nav-link-collapse {{ (Request::is('*/mobile') || Request::is('*/mobile/*')) ? '' : 'collapsed'  }}" data-toggle="collapse" href="#mobile" data-parent="#exampleAccordion"><i class="fa fa-laptop   fa-lg fa-fw sidebar-icon"></i> <span class="nav-link-text">Devices overview</span><span class="arrow"></span></a>
                    <ul class="sidenav-second-level collapse {{ (Request::is('*/mobile') || Request::is('*/mobile/*')) ? 'show' : ''  }}" id="mobile">
                        @if(Auth::user()->canDo($prefix.'.mobile.main'))
                            <li><a {{ Request::is('*/mobile') ? 'class=active' : ''  }}  href="<?= route($prefix.'.mobile') ?>"><i class="fa fa-angle-double-right"></i>Main</a></li>
                        @endif
                            @if(Auth::user()->canDo($prefix.'.mobile.tech'))
                        <li><a {{ Request::is('*/mobile/tech*') ? 'class=active' : ''  }} href="<?= route($prefix.'.mobile.query',  ['query' => 'tech']) ?>"><i class="fa fa-angle-double-right"></i>Tech</a></li>
                                @endif
                                @if(Auth::user()->canDo($prefix.'.mobile.auto'))
                                <li><a {{ Request::is('*/mobile/auto*') ? 'class=active' : ''  }} href="<?= route($prefix.'.mobile.query', ['query' => 'auto']) ?>"><i class="fa fa-angle-double-right"></i>Auto</a></li>
                            @endif
                                @if(Auth::user()->canDo($prefix.'.mobile.home'))
                                    <li><a {{ Request::is('*/mobile/home*') ? 'class=active' : ''  }} href="<?= route($prefix.'.mobile.query', ['query' => 'home']) ?>"><i class="fa fa-angle-double-right"></i>Home</a></li>
                            @endif
                    </ul>
                </li>

            <?php endif;?>

                <?php if(Auth::check() && (Auth::user()->canDo($prefix.'.mobile-download') )): ?>
                <li  class="nav-item" data-toggle="tooltip" data-placement="right">
                    <a class="nav-link nav-link-collapse {{ Request::is('*/mobile-download*') ? '' : 'collapsed'  }}" data-toggle="collapse" href="#mobile-download" data-parent="#exampleAccordion"><i class="fa fa-mobile   fa-lg fa-fw sidebar-icon"></i> <span class="nav-link-text">Mobile app download</span><span class="arrow"></span></a>
                    <ul class="sidenav-second-level collapse {{ Request::is('*/mobile-download*') ? 'show' : ''  }}" id="mobile-download">
                        <li  class="nav-item" data-toggle="tooltip" data-placement="right">
                            <a class="nav-link {{ Request::is('*/mobile-download-custom') ? 'active' : ''  }} "href="<?= route($prefix.'.mobile-download-custom') ?>"><i class="fa fa-hand-paper-o fa-lg fa-fw sidebar-icon"></i> <span class="nav-link-text">Custom data</span><span class="arrow"></span></a>

                        </li>
                        <li  class="nav-item" data-toggle="tooltip" data-placement="right">
                            <a class="nav-link {{ Request::is('*/mobile-download') ? 'active' : ''  }} "href="<?= route($prefix.'.mobile-download') ?>"><i class="fa fa-google fa-lg fa-fw sidebar-icon"></i> <span class="nav-link-text">Data studio report</span><span class="arrow"></span></a>
                        </li>
                    </ul>
                </li>
                <?php endif;?>

                @if(Auth::user()->canDo($prefix.'.adwords.main'))

                    @if(Request::is('*fixinglist*'))

                        <li  class="nav-item" data-toggle="tooltip" data-placement="right">
                            <a class="nav-link nav-link-collapse {{ Request::is('*/adwords*') ? '' : 'collapsed'  }}" data-toggle="collapse" href="#Adwords" data-parent="#exampleAccordion"><i class="fa fa-mobile   fa-lg fa-fw sidebar-icon"></i> <span class="nav-link-text">Adwords</span><span class="arrow"></span></a>
                            <ul class="sidenav-second-level collapse {{ Request::is('*/adwords*') ? 'show' : ''  }}" id="Adwords">
                                <li class="nav-item" data-toggle="tooltip" data-placement="right" >
                                    <a class="nav-link {{(isset($query['filters'])&&$query['filters']=='ga:campaign=@Поиск,ga:campaign=@КМС;ga:campaign=@РФ') ? 'active' : ''  }}" href="<?= route($prefix.'.adwords', [
                                        'filters'=>'ga:campaign=@Поиск,ga:campaign=@КМС;ga:campaign=@РФ',
                                        'metrics'=>'ga:adClicks,ga:impressions,ga:CTR,ga:CPC,ga:adCost,ga:goal7Completions',
                                        'dimensions'=>'ga:isoWeek',
                                    ]) ?>"><i class="fa fa-sign-out fa-lg fa-fw sidebar-icon"></i><span class="nav-link-text">Россия</span></a>
                                </li>
                                <li class="nav-item" data-toggle="tooltip" data-placement="right" >

                                    <a class="nav-link {{(isset($query['filters'])&&$query['filters']=='ga:campaign=@Поиск,ga:campaign=@КМС;ga:campaign!@РФ') ? 'active' : ''  }}" href="<?= route($prefix.'.adwords', [
                                        'filters'=>'ga:campaign=@Поиск,ga:campaign=@КМС;ga:campaign!@РФ',
                                        'metrics'=>'ga:adClicks,ga:impressions,ga:CTR,ga:CPC,ga:adCost,ga:goal7Completions',
                                        'dimensions'=>'ga:isoWeek',
                                    ]) ?>"><i class="fa fa-sign-out fa-lg fa-fw sidebar-icon"></i><span class="nav-link-text">Казахтан</span></a>
                                </li>
                            </ul>
                        </li>



                    @else

                        <li class="nav-item" data-toggle="tooltip" data-placement="right" >
                            <a class="nav-link {{Request::is('*/adwords*') ? 'active' : ''  }}" href="<?= route($prefix.'.adwords', [
                                'filters'=>'ga:campaign=@Поиск,ga:campaign=@КМС;ga:campaign!@Клиент',
                                'metrics'=>'ga:adClicks,ga:impressions,ga:CTR,ga:CPC,ga:adCost,ga:goal9Completions',
                                'dimensions'=>'ga:isoWeek',
                            ]) ?>"><i class="fa fa-sign-out fa-lg fa-fw sidebar-icon"></i><span class="nav-link-text">Adwords</span></a>
                        </li>

                    @endif


                @endif



            @endauth
        </ul>
        <ul class="navbar-nav sidenav-toggler">
            <li class="nav-item">
                <a class="nav-link text-center" id="sidenavToggler">
                    <i class="fa fa-fw fa-angle-left"></i>
                </a>
            </li>
        </ul>

            <ul class="navbar-nav">
                @auth
                    <?php if($fixinglist): ?>
                    <li  class="nav-item"><a class="nav-link {{ Request::is('*fixinglist*') ? 'active' : ''  }}" href="<?=$fixinglistUrl?>">Fixinglist</a></li>

                    <?php endif;?>
                    <?php if($ustabor): ?>
                    <li  class="nav-item"><a class="nav-link {{ Request::is('*ustabor*') ? 'active' : ''  }}" href="<?=$ustaborUrl?>">Ustabor</a></li>
                    <?php endif;?>

                @endauth
            </ul>
        <ul class="navbar-nav ml-auto">


                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                @else
                    @ifUserIs('admin')
                    <li class="nav-item dropdown">
                        <a class="nav-link {{ Request::is('*url-list') ? 'active' : ''  }}" href="{{ route('url-list.index') }}" >Url List</a>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle mr-lg-2 {{ (Request::is('*users') || Request::is('*rbac*')) ? 'active' : ''  }}" id="messagesDropdown" href="#users" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        User management<span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" id="users" role="menu" aria-labelledby="messagesDropdown">
                            <li><a class="dropdown-item" href="{{ route('users') }}">Users</a></li>
                            <li><a class="dropdown-item" href="{{ route('roles') }}">Roles</a></li>
                            <?php if (App::isLocal()):?>
                            <li><a class="dropdown-item" href="{{ route('permissions') }}">Permissions</a></li>
                            <?php endif;?>
                            <li><a class="dropdown-item" href="{{ route('permission-groups') }}">Permission Groups</a></li>
                        </ul>
                    </li>

                            @endif
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle mr-lg-2" id="messagesDropdown" href="#logout" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu" id="logout" role="menu" aria-labelledby="messagesDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                Logout
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
                @endguest
        </ul>
    </div>
</nav>
<div class="content-wrapper">
    <div class="container-fluid">

        @yield('content')
    </div>
    <!-- /.container-fluid-->
    <!-- /.content-wrapper-->
    <footer class="sticky-footer">
        <div class="container">
            <div class="text-center">
                <small>Powered by <a href="http://rebus.digital" rel="external" target="_blank">Rebus</a></small>
            </div>
        </div>
    </footer>
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fa fa-angle-up"></i>
    </a>
    <!-- Logout Modal-->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <!-- Page level plugin JavaScript-->
    {{--<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>--}}
    <script src="{{ asset('vendor/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.js') }}"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin.min.js') }}"></script>
    <!-- Custom scripts for this page-->
    <script src="{{ asset('js/sb-admin-datatables.min.js') }}"></script>
    {{--<script src="{{ asset('js/sb-admin-charts.min.js') }}"></script>--}}

    <script src="{{asset('js/jquery.tablesorter.mod.js')}}"></script>
    <script src="{{asset('js/script.js')}}"></script>
    @yield('template_scripts')
</div>
</body>

</html>