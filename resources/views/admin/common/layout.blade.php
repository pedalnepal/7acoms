<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#003893">
    <title>
      @if(@$title)
        {{@$title}} - {{env('APP_NAME')}}
      @else
        {{env('APP_NAME')}}
      @endif
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{url('admin-assets/css/custom.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="{{url('images/favicon.svg')}}">

    <style>
    .sortable-handle {
      cursor: move;
      font-size: 20px;
      font-weight: 600;
    }
    .sortable-dragged-item{
      cursor: move;
      background-color: #fff !important;
    }
    .sortable-ghost{
      background-color: #ccc !important;
    }
    </style>
    <script type="text/javascript"> var site_url = "{{url('/')}}"; var site_token = "{{csrf_token()}}"; </script>
</head>
<body>
  <header class="navbar sticky-top flex-md-nowrap p-0">
    <div class="left-nav-section me-0 px-3 brand-img">
      <a class="navbar-brand" href="/"><img src="{{url('admin-assets/images/7th-acoms-mnemonic.svg')}}" width="60"></a>
    </div>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="right-main-content">
      <div class="navbar">
        <div class="nav-item text-nowrap dropdown">
          <a class="nav-link px-3 dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown" aria-expanded="false">@php($authUser = \Auth::user()) @if($authUser->media) <span style="width:50px;display: inline-block;border-radius: 50%;overflow: hidden;margin-right: 10px;">{!!$authUser->media->get_attachment('thumb')!!}</span> @endif {{$authUser->name}}</a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('user.edit', auth()->user()->id) }}">Profile</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}" name="LogoutForm" style="display: inline;"> @csrf </form>
                <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                    LogoutForm.submit();">
                    {{ __('Log Out') }}
                </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>
  <div id="AdminSection" data-v-app="">
   <div class="">
      <div class="row g-0">
         <nav id="sidebarMenu" class="left-nav-section admin-sidebar d-md-block sidebar collapse" aria-label="Admin navigation">
            <div class="position-sticky sidebar-sticky">
               <div class="sidebar-nav-header">
                  <span class="sidebar-nav-title">{{ config('app.name', 'Admin') }}</span>
                  <span class="sidebar-nav-subtitle">Navigation</span>
               </div>
               <ul class="nav flex-column sidebar-nav">

                  <li class="nav-item">
                     <a href="{{route('dashboard')}}" class="{{ Route::is('dashboard') ? 'bs-nav-active' : null }} router-link-exact-active nav-link" aria-current="page">
                        <i class="nav-icon">
                           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                              <path d="M13 21V11H21V21H13ZM3 13V3H11V13H3ZM9 11V5H5V11H9ZM3 21V15H11V21H3ZM5 19H9V17H5V19ZM15 19H19V13H15V19ZM13 3H21V9H13V3ZM15 5V7H19V5H15Z"></path>
                           </svg>
                        </i>
                        <span>Dashboard</span>
                     </a>
                  </li>

                  <!-- <li class="nav-item">
                    <a class="nav-link {{ Route::is('slider.*') ? 'bs-nav-active' : null }}" href="#NavSlider" data-bs-toggle="collapse" aria-expanded="{{ Route::is('slider.*') ? 'true' : 'false' }}">
                      <i class="nav-icon">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M160 80H512c8.8 0 16 7.2 16 16V320c0 8.8-7.2 16-16 16H490.8L388.1 178.9c-4.4-6.8-12-10.9-20.1-10.9s-15.7 4.1-20.1 10.9l-52.2 79.8-12.4-16.9c-4.5-6.2-11.7-9.8-19.4-9.8s-14.8 3.6-19.4 9.8L175.6 336H160c-8.8 0-16-7.2-16-16V96c0-8.8 7.2-16 16-16zM96 96V320c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H160c-35.3 0-64 28.7-64 64zM48 120c0-13.3-10.7-24-24-24S0 106.7 0 120V344c0 75.1 60.9 136 136 136H456c13.3 0 24-10.7 24-24s-10.7-24-24-24H136c-48.6 0-88-39.4-88-88V120zm208 24a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/></svg>
                      </i>
                      <span>Slider</span>
                    </a>
                    <div class="submenu collapse {{ Route::is('slider.*') ? 'show' : null }}" id="NavSlider">
                        <ul class="nav flex-column" >
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('slider.index') ? 'bs-nav-active' : null }}" href="{{route('slider.index')}}" >
                                    <span>All Sliders</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('slider.create')  ? 'bs-nav-active' : null }}" href="{{route('slider.create')}}" >
                                    <span>Add Slider</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li> -->

                <li class="nav-item">
                    <a class="nav-link {{ Route::is('page.*') ? 'bs-nav-active' : null }}" href="#NavPage" aria-expanded="{{ Route::is('page.*') ? 'true' : 'false' }}" data-bs-toggle="collapse">
                        <i class="nav-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM112 256H272c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H272c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H272c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>
                        </i>
                        <span>Pages</span>
                    </a>
                    <div class="submenu collapse {{ Route::is('page.*') ? 'show' : null }}" id="NavPage">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('page.index') ? 'bs-nav-active' : null }}" href="{{route('page.index')}}" >
                                    <span>All Pages</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('page.create')  ? 'bs-nav-active' : null }}" href="{{route('page.create')}}" >
                                    <span>Add Page</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                <!-- <li class="nav-item">
                    <a class="nav-link {{ Route::is('media.*') ? 'bs-nav-active' : null }}" href="{{ route('media.index') }}">
                        <i class="nav-icon fa fa-image"></i> <span>Media</span>
                    </a>
                </li> -->

                <li class="nav-item">
                    <a class="nav-link {{ Route::is('abstract.*') ? 'bs-nav-active' : null }}" href="{{ route('abstract.index') }}">
                        <i class="nav-icon fa fa-file-lines"></i> <span>Abstracts</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::is('registration.index') || Route::is('registration.admin.*') || Route::is('registration.download') || Route::is('registration.restore') || Route::is('registration.destroy') ? 'bs-nav-active' : null }}" href="{{ route('registration.index') }}">
                        <i class="nav-icon fa fa-user-plus"></i> <span>Registrations</span>
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link {{ Route::is('menu.*') ? 'bs-nav-active' : null }}" href="{{route('menu.index')}}" >
                        <i class="nav-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 103.17"><g><path d="M26,0h70.87c7.15,0,13.65,2.93,18.36,7.64l0.22,0.24c4.58,4.69,7.42,11.1,7.42,18.13v51.16c0,7.15-2.93,13.65-7.64,18.36 c-4.71,4.71-11.21,7.64-18.36,7.64H26c-7.14,0-13.64-2.93-18.35-7.64H7.64C2.93,90.82,0,84.32,0,77.16V26 c0-7.13,2.92-13.63,7.64-18.35l0.02-0.03C12.38,2.92,18.87,0,26,0L26,0z M41.31,29.74h40.26c2.25,0,4.09,1.84,4.09,4.09l0,0 c0,2.25-1.84,4.09-4.09,4.09H41.31c-2.25,0-4.09-1.84-4.09-4.09l0,0C37.22,31.58,39.06,29.74,41.31,29.74L41.31,29.74L41.31,29.74z M41.31,65.25h40.26c2.25,0,4.09,1.84,4.09,4.09l0,0c0,2.25-1.84,4.09-4.09,4.09l-40.26,0c-2.25,0-4.09-1.84-4.09-4.09l0,0 C37.22,67.09,39.06,65.25,41.31,65.25L41.31,65.25L41.31,65.25z M41.31,47.5h40.26c2.25,0,4.09,1.84,4.09,4.09l0,0 c0,2.25-1.84,4.09-4.09,4.09H41.31c-2.25,0-4.09-1.84-4.09-4.09l0,0C37.22,49.34,39.06,47.5,41.31,47.5L41.31,47.5L41.31,47.5z M96.88,8.2H26c-4.9,0-9.35,2-12.57,5.22l-0.02,0.02C10.2,16.65,8.2,21.1,8.2,26v51.16c0,4.89,2.01,9.34,5.23,12.56l-0.01,0.01 c3.23,3.22,7.68,5.23,12.57,5.23h70.87c4.88,0,9.33-2.01,12.56-5.24c3.23-3.23,5.24-7.68,5.24-12.56V26c0-4.8-1.93-9.17-5.04-12.39 l-0.19-0.18C106.21,10.21,101.77,8.2,96.88,8.2L96.88,8.2z"/></g></svg>
                        </i>
                        <span>Manage Menu</span>
                    </a>
                </li>

                <!-- <li class="nav-item">
                    <a class="nav-link {{ Route::is('team.*') || Route::is('team-category.*') ? 'bs-nav-active' : null }}" href="#NavTeam" data-bs-toggle="collapse" aria-expanded="{{ Route::is('team.*') || Route::is('team-category.*') ? 'true' : 'false' }}">
                        <i class="nav-icon fa fa-users"></i>
                        <span>Team</span>
                    </a>
                    <div class="submenu collapse {{ Route::is('team.*') || Route::is('team-category.*') ? 'show' : null }}" id="NavTeam">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('team.index') ? 'bs-nav-active' : null }}" href="{{route('team.index')}}">
                                    <span>All Members</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('team.create') ? 'bs-nav-active' : null }}" href="{{route('team.create')}}">
                                    <span>Add Member</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('team-category.index') ? 'bs-nav-active' : null }}" href="{{route('team-category.index')}}">
                                    <span>Categories</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('team-category.create') ? 'bs-nav-active' : null }}" href="{{route('team-category.create')}}">
                                    <span>Add Category</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li> -->

            <li class="nav-item">
                <a class="nav-link {{ Route::is('setting.*') ? 'bs-nav-active' : null }}" href="#NavSetting" aria-expanded="{{ Request::segment(2) === 'setting' ? 'true' : 'false' }}" data-bs-toggle="collapse">
                    <i class="nav-icon">
                    <svg id="Layer_2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 430.85 378.44"><g><path d="M286.24,223.33l10.99-45.64c.04-.14.07-.29.11-.43,2.91-10.87,11.47-19.43,22.34-22.34l33.35-8.94c-5.47-6.52-13.68-10.68-22.84-10.68h-9.84c-2.51-7.9-5.7-15.59-9.52-22.96l6.96-6.96c11.62-11.62,11.62-30.54,0-42.16l-21.04-21.04c-5.63-5.63-13.12-8.73-21.08-8.73s-15.45,3.1-21.08,8.73l-6.96,6.96c-7.37-3.81-15.06-7-22.96-9.52v-9.84c0-16.44-13.37-29.81-29.81-29.81h-29.76c-16.44,0-29.81,13.37-29.81,29.81v9.84c-7.89,2.51-15.59,5.7-22.96,9.52l-6.96-6.96c-5.63-5.63-13.12-8.73-21.08-8.73s-15.45,3.1-21.08,8.73l-21.04,21.04c-11.62,11.62-11.62,30.53,0,42.16l6.96,6.96c-3.81,7.37-7,15.06-9.51,22.96h-9.84c-16.44,0-29.81,13.37-29.81,29.81v29.76c0,16.44,13.37,29.81,29.81,29.81h9.84c2.51,7.89,5.7,15.59,9.51,22.96l-6.96,6.96c-11.62,11.62-11.62,30.53,0,42.16l21.04,21.04c5.63,5.63,13.12,8.73,21.08,8.73s15.45-3.1,21.08-8.73l6.96-6.96c7.37,3.82,15.06,7,22.96,9.51v9.84c0,16.44,13.37,29.81,29.81,29.81h25.81c-2.39-5.69-3.64-11.85-3.65-18.21,0-12.57,4.89-24.39,13.78-33.28l85.18-85.18ZM180,260c-44.11,0-80-35.89-80-80s35.89-80,80-80,80,35.89,80,80-35.89,80-80,80Z" style="stroke-width:0px;"/><path d="M425.27,192.53l-.32-.32c-.94-.94-2.19-1.43-3.54-1.47-1.33,0-2.6.52-3.54,1.46l-21.6,21.6c-3.3,3.3-7.7,5.12-12.38,5.12s-9.07-1.82-12.38-5.13c-3.31-3.3-5.13-7.7-5.13-12.38,0-4.68,1.82-9.07,5.13-12.38l21.6-21.6c.94-.94,1.46-2.21,1.46-3.54,0-1.33-.53-2.6-1.47-3.54l-.31-.31c-3.61-3.6-8.4-5.59-13.5-5.59-1.67,0-3.33.22-4.94.65l-44.35,11.88c-6.57,1.76-11.74,6.93-13.5,13.5-.01.04-.02.08-.03.12l-11.82,49.06-87.67,87.67c-6.53,6.53-10.12,15.21-10.12,24.44,0,9.23,3.6,17.91,10.13,24.44l2.09,2.09c6.53,6.53,15.21,10.12,24.44,10.12h0c9.23,0,17.91-3.59,24.44-10.12l87.67-87.67,49.06-11.82s.08-.02.12-.03c6.56-1.76,11.73-6.93,13.5-13.5l11.88-44.35c1.76-6.56-.14-13.63-4.94-18.43ZM257.26,342.2c-1.89,1.89-4.4,2.93-7.07,2.93s-5.18-1.04-7.07-2.93c-1.89-1.89-2.93-4.4-2.93-7.07,0-2.67,1.04-5.18,2.93-7.07,1.89-1.89,4.4-2.93,7.07-2.93s5.18,1.04,7.07,2.93c1.89,1.89,2.93,4.4,2.93,7.07,0,2.67-1.04,5.18-2.93,7.07ZM316.19,279.73l-41.37,41.38c-1.42,1.42-3.3,2.2-5.3,2.2s-3.89-.78-5.3-2.2c-1.42-1.42-2.2-3.3-2.2-5.3s.78-3.89,2.2-5.3l41.37-41.38c1.42-1.42,3.3-2.2,5.3-2.2s3.89.78,5.3,2.2c1.42,1.42,2.2,3.3,2.2,5.3s-.78,3.89-2.2,5.3Z" style="stroke-width:0px;"/></g></svg>
                    </i>
                    <span>Setting</span>
                </a>
                <div class="collapse submenu {{ Route::is('setting.*') ? 'show' : null }}" id="NavSetting">
                    <ul class="nav flex-column">
                        <!-- <li class="nav-item">
                            <a class="nav-link {{ Route::is('setting.home') ? 'bs-nav-active' : null }}" href="{{route('setting.home')}}" >
                                <span>Home Configuration</span>
                            </a>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('setting.index') ? 'bs-nav-active' : null }}" href="{{route('setting.index')}}" >
                                <span>Site Configuration</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

                @can('manage users')
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('user.*') ? 'bs-nav-active' : null }}" href="#NavUser" aria-expanded="{{ Request::segment(2) === 'user' ? 'true' : 'false' }}" data-bs-toggle="collapse">
                        <i class="nav-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 236.02 149.23"><g><path d="M166.12.13c-11.7,1.8-20.6,9.3-24,20.3-1.1,3.5-1.3,10-.5,13.8,1.2,5.6,3.6,9.9,7.8,14.2,4.3,4.3,8.6,6.7,14.1,7.8,4,.8,10.4.6,13.9-.5,10.6-3.3,18.2-11.9,20.1-22.7,2.4-13.7-6-27.5-19.2-31.7-3.4-1-9.5-1.6-12.2-1.2Z" style="stroke-width:0px;"/><path d="M69.72,1.63c-11.3,2.3-20.1,12.6-20.1,23.5,0,3.2-1.2,5.9-3.7,7.8-2.3,1.8-5.7,3.6-8,4-2,.4-2,.7,0,4.5,3.6,6.6,11.7,10.5,24.7,11.9,7.4.8,24.2.3,29.8-.9,10.2-2.2,16-5.9,19-12.3,1.2-2.5,1.1-2.9-.8-3.2-3.6-.7-9.1-4.4-10.5-7.1-.4-.6-.8-2.9-1-4.9-.6-7.2-3.1-12.5-8.2-17.2-5.4-5.2-13.8-7.6-21.2-6.1Z" style="stroke-width:0px;"/><path d="M133.62,57.83c-.4.1-1,.3-1.3.4-.5.1-.1.9,1.2,2.4,3.9,4.8,8.1,11.9,10.2,17.8.5,1.3,4.9,15.8,9.9,32.1,5,16.4,9.7,31.8,10.4,34.2l1.4,4.5h39.6l.1-26.2.1-26.2h7l.1,26.2.1,26.2h23.6v-28.1c0-31.1-.1-32.7-3-40.2-5.3-14.1-16-22.4-30-23.4l-3.7-.2-9.8,19.9-9.7,19.9h-4.7l-.5-2.4c-.2-1.3-.8-4.1-1.2-6.1s-.7-4.2-.7-4.8c0-.7,1.5-3.9,3.9-8,2.2-3.8,4-6.9,4-7.1,0-.1-4.9-.2-10.8-.2s-10.8.1-10.8.2,1.8,3.3,3.9,7.1c2.2,3.7,3.9,7.2,3.9,7.7,0,.4-.6,3.7-1.2,7.2l-1.2,6.4h-4.6l-9.8-19.8-9.8-19.8h-3c-1.6,0-3.2.1-3.6.3Z" style="stroke-width:0px;"/><path d="M43.02,61.63c-5.7,1.2-11,4.3-15.6,9.2-5.4,5.9-6.4,8.3-17.4,44.8C4.62,133.23.22,148.03.02,148.43c-.2.8.6.8,12,.8h12.2l.7-2.2c1-3,13.7-45.7,13.7-46,0-.1,1.4-.2,3-.2,2.7,0,3,.1,2.8.8-.6,1.8-13.7,47.4-13.7,47.5s18.9.1,42,.1c39.8,0,42,0,41.7-.8-.7-2.4-11.8-46.6-11.8-47.1,0-.4.9-.5,3.1-.4l3.1.1,7.2,24,7.2,24,12.1.1c9.5.1,12.1,0,12.1-.5,0-.3-4.3-14.7-9.5-32-9.9-32.7-11.6-37.2-15.6-42.9-2.6-3.7-6.9-7.6-10.4-9.4-3.5-1.9-9-3.3-12.9-3.3h-3.1l-5.3,13c-2.9,7.2-7.9,19.4-11,27.3l-5.7,14.3-.7-1.5c-.4-.8-5.4-13.1-11.1-27.2l-10.4-25.7h-3.5c-1.8,0-4.2.2-5.2.4Z" style="stroke-width:0px;"/></g></svg>
                        </i>
                        <span>All Users</span>
                    </a>
                    <div class="collapse submenu {{ Route::is('user.*') ? 'show' : null }}" id="NavUser">
                        <ul class="nav flex-column">
                            @can('view users')
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('user.index') ? 'bs-nav-active' : null }}" href="{{route('user.index')}}" >
                                    <span>All Users</span>
                                </a>
                            </li>
                            @endcan
                            @can('create users')
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('user.create')  ? 'bs-nav-active' : null }}" href="{{route('user.create')}}" >
                                    <span>Add User</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcan

                <li class="nav-item sidebar-nav-logout">
                    <a class="nav-link nav-link-logout" href="{{ route('logout') }}" onclick="event.preventDefault();
                                        LogoutForm.submit();">
                        <i class="nav-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/></svg>
                        </i>
                        <span>Logout</span>
                    </a>
                </li>

               </ul>
            </div>
         </nav>
         <main class="right-main-content ms-sm-auto py-3 admin-main-wrap">
            @yield('content')
         </main>
      </div>
   </div>


</div>

@if(!Route::is('media.*'))
<div class="modal fade" id="MediaModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="row w-100">
            <div class="col-md-3">
                <h4 class="modal-title">Media</h4>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" id="MediaKeyword" placeholder="Search">
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="AllMediaContainer">
        <label for="MediaAttachment" class="text-center align-content-center top-media-upload" style="height: 250px; width:100%; border: 5px dashed #B2D2D9; margin-bottom: 10px;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="50" height="50"><path fill="#69B4C4" d="M288 109.3V352c0 17.7-14.3 32-32 32s-32-14.3-32-32V109.3l-73.4 73.4c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l128-128c12.5-12.5 32.8-12.5 45.3 0l128 128c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L288 109.3zM64 352H192c0 35.3 28.7 64 64 64s64-28.7 64-64H448c35.3 0 64 28.7 64 64v32c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V416c0-35.3 28.7-64 64-64zM432 456a24 24 0 1 0 0-48 24 24 0 1 0 0 48z"/></svg><br>
        Upload Files...</label>
        <form id="add_file" style="display:none;">
            <div class="media-attachment-content">
                <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="50" height="50"><path fill="#005366" d="M288 109.3V352c0 17.7-14.3 32-32 32s-32-14.3-32-32V109.3l-73.4 73.4c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l128-128c12.5-12.5 32.8-12.5 45.3 0l128 128c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L288 109.3zM64 352H192c0 35.3 28.7 64 64 64s64-28.7 64-64H448c35.3 0 64 28.7 64 64v32c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V416c0-35.3 28.7-64 64-64zM432 456a24 24 0 1 0 0-48 24 24 0 1 0 0 48z"/></svg><br>Drag here to Upload...</span>
              <input type="file" multiple name="media[]" id="MediaAttachment" required class="form-control"/>
            </div>
            <div style="display:none;">
              <button class="btn btn-sm btn-primary" type="button" id="file_attachment_submit" data-action="{{ route('media.index') }}">
                  <i class="ti ti-check"></i>
              </button>
            </div>
        </form>
        <form name="mediaList">
            <div id="MediaContent" class="row row-cols-6 g-2">
                @php($mediaItems = \App\Models\Media::orderBy('id', 'desc')->take(40)->get())
                @foreach($mediaItems as $mediaItem)
                    <div class="media-item" >
                        <input style="display: none;" type="radio" id="media_{{$mediaItem->id}}" name="smedia" value="{{$mediaItem->id}}" data-url="{{$mediaItem->get_attachment_url()}}" data-alt="{{$mediaItem->alt}}">
                        <div class="media-inside">
                            <label for="media_{{$mediaItem->id}}" class="media-content">
                                {!!$mediaItem->get_attachment()!!}
                            </label>
                            <div class="media-bottom">
                                <a href="javascript:void(0);" class="edit-btn media-send-to-edit" data-media="{{$mediaItem->id}}"></a>
                                <a href="javascript:void(0);" class="delete-btn media-send-to-trash" data-media="{{$mediaItem->id}}"></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="waves-effect waves-dark btn btn-default" data-bs-dismiss="modal">Close</button>
        <button type="button" class="waves-effect waves-dark btn btn-primary" id="SelectAndInsertMedia">Insert</button>
      </div>
    </div>
  </div>
</div>
@endif
<style>
#AllMediaContainer.active-dropzone #add_file{
    display:block !important;
    opacity: 0.75;
    text-align: center;
}
#AllMediaContainer.active-dropzone .top-media-upload{
    opacity: 0;
}
.media-inside {
  border: 3px solid #B2D2D9;
  height: 100%;
  width: 100%;
  border-radius: 10px;
  overflow: hidden;
  background-color: #d9f8ff;
  position: relative;
}
.media-content{
  cursor: pointer;
  display: block;
  aspect-ratio: 1/1;
}
.media-content img {
  aspect-ratio: 1/1;
  width: 100%;
  object-fit: contain;
}
.media-item input:checked + .media-inside {
  border-color: #1A7185;
}
.media-bottom {
  display: flex;
  justify-items: ;
  align-items: ;
  padding: 10px;
  background-color: #B2D2D9;
  justify-content: space-between;
  border-top: 1px solid #8BC9D7;
}
.media-bottom a{
    color: #000;
    text-decoration: none;
}
.media-bottom a.edit-btn {
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>');
}
.media-bottom a.delete-btn {
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M170.5 51.6L151.5 80h145l-19-28.4c-1.5-2.2-4-3.6-6.7-3.6H177.1c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80H368h48 8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8V432c0 44.2-35.8 80-80 80H112c-44.2 0-80-35.8-80-80V128H24c-13.3 0-24-10.7-24-24S10.7 80 24 80h8H80 93.8l36.7-55.1C140.9 9.4 158.4 0 177.1 0h93.7c18.7 0 36.2 9.4 46.6 24.9zM80 128V432c0 17.7 14.3 32 32 32H336c17.7 0 32-14.3 32-32V128H80zm80 64V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"/></svg>');
}
.media-bottom a {
  width: 15px;
  height: 15px;
  background-size: contain  !important;
  background-repeat: no-repeat !important;
  opacity: 0.6;
  color: #005366;
  text-decoration: none;
}
.media-bottom a.delete-btn:hover{
    opacity: 0.8;
}
.media-attachment-content {
  background-color: #c5c5c5;
  width: 100%;
  display: block;
  height: 100%;
  position: relative;
  border: 4px dashed #7C85A8;
  min-height: 110px;
}
.media-attachment-content input {
  position: absolute;
  width: 100%;
  height: 100%;
  opacity: 0;
  z-index: 3;
}
/*.media-attachment-content::after {
  content: "\f093";
  font-family: "Font Awesome 6 Free";
  font-weight: 600;
  left: 50%;
  position: absolute;
  top: 46%;
  font-size: 28px;
  color: #1D368F;
  transform: translate(-50%, -50%);
}
.media-attachment-content::before {
  content: "Drag here to upload...";
  position: absolute;
  top: calc( 50% + 15px );
  left: 0;
  text-align: center;
  width: 100%;
  font-weight: 600;
}*/
.media-attachment-content {
  display: flex;
  justify-content: center;
  font-weight: 600;
  align-items: center;
  flex-wrap: wrap;
}
.media-attachment-content *{
    width: 100%;
}
#add_file {
  position: absolute;
  width: 100%;
  height: 100%;
  left: 0;
  top: 0;
  z-index: 99999;
  display: none;
}
.progressing-bar {
  position: absolute;
  width: 100%;
  height: 36px;
  text-align: center;
  font-size: 16px;
  display: flex;
  z-index: 2;
  background-color: #B2D2D9;
}
.progressing-text {
  text-align: center;
  width: 100%;
  align-self: center;
}
.progressing-bg {
  position: absolute;
  z-index: -1;
  background-color: #3068AC;
  height: 100%;
  left: 0;
  top: 0;
  opacity: 0.2;
}
.error-occured .progressing-bg {
    background-color: #FF3A6E;
}
.error-occured .progressing-text {
  color: #FF3A6E;
  font-weight: 600;
}
.abort-request {
  position: absolute;
  right: 3px;
  top: 8px;
  font-size: 15px;
  background-color: #FF3A6E;
  width: 20px;
  height: 20px;
  color: #fff;
  line-height: 18px;
  font-style: normal;
  border-radius: 50%;
  box-shadow: 2px 2px 2px -1px rgba(0, 0, 0, 0.5);
  cursor: pointer;
}
#MediaEditModal {
  z-index: 999999;
  background-color: rgba(0, 0, 0, 0.7);
}
</style>
<div class="modal fade" id="MediaEditModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                        <h5 class="modal-title">Edit Media</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="MediaEditBody">
                <form id="MediaEditForm">
                    {{csrf_field()}}
                    <input type="hidden" name="mediaid" id="MediaIdForEdit">
                    <input type="hidden" name="action" value="edit_media_with_filename">
                    <div class="form-group form-float">
                      <div class="form-line focused">
                          <label class="form-label">Title / Alt</label>
                          <input value="" id="main_media_title" name="main_media_title" class="form-control" type="text">
                      </div>
                    </div>
                    <div class="form-group form-float">
                      <div class="form-line">
                          <label class="form-label">File Name</label>
                          <input value="" id="main_media_file_name" name="main_media_file_name" class="form-control" type="text">
                      </div>
                    </div>
                    <button type="button" class="waves-effect waves-dark btn btn-primary" id="UpdateAltInMedia">Update</button>
                </form>
            </div>
        </div>
      </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script type="text/javascript" src="{{url('admin-assets/vendors/tinymce/tinymce.min.js')}}"></script>
<script src="{{url('/admin-assets/js/script.js')}}?s"></script>

<script type="text/javascript">
    tinymce.init({
        selector: ".WYSWIYG",
        height: 480,
        setup : media_editor_setups,
        relative_urls : false,
        browser_spellcheck: true,
        branding: false,
        content_css: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css, {{url('admin-assets/css/tiny-css.css')}}?s',
        //paste_as_text: true,


        plugins: [
            'advlist pageembed autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'template paste textcolor colorpicker textpattern'
        ],
        toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        toolbar2: ' media | forecolor backcolor | mybutton | addClassButton | bootstrapRow bootstrapColumn'
    });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            }
        });
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  // Enable hover dropdown only on desktop
  if (window.matchMedia("(min-width: 992px)").matches) {
    document.querySelectorAll(".navbar .dropdown").forEach(function (dd) {
      const toggle = dd.querySelector('[data-bs-toggle="dropdown"]');
      const instance = bootstrap.Dropdown.getOrCreateInstance(toggle);

      dd.addEventListener("mouseenter", function () {
        instance.show();
      });

      dd.addEventListener("mouseleave", function () {
        instance.hide();
      });
    });
  }
});
</script>

@stack('scripts')
</body>
</html>
