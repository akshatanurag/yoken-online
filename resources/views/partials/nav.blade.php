@if(!Auth::check())
    <ul id="student-my-account" class="dropdown-content">
        <li><a href="/login" class="green-text">Login</a></li>
        <li class="divider"></li>
        <li><a href="/register">Signup</a></li>
    </ul>
    @else
    <ul id="my-account-list" class="dropdown-content">
        <li><a class="green-text" href="/user/">Dashboard</a></li>
        <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
        </ul>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
@endif
<ul id="slide-out" class="side-nav" style="padding: 30px 0">
    <li>
        <a class="brand-logo center-align" href="/"><img style="height:100%" class="responsive-img" src="/img/yoken-logo.png" alt="Yoken Logo"></a>
    </li>
    <br>
    <div class="divider"></div>
    @if(!Auth::check())
        <li><a class="waves-effect center-align" href="/login">Login</a></li>
        <li><a class="waves-effect center-align" href="/register">Signup</a></li>
        <div class="divider"></div>
        <li><a class="waves-effect center-align" href="/webinars">Webinars</a></li>
        <div class="divider"></div>
        @else
        <li><a class="waves-effect center-align" href="/user">Dashboard</a></li>
        <div class="divider"></div>
        <li><a class="waves-effect center-align" href="/webinars">Webinars</a></li>
    @endif
        <div class="divider"></div>
        <li><a class="waves-effect center-align" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-side').submit();">Logout</a></li>
        <form id="logout-form-side" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
        <div class="divider"></div>
</ul>
<div class="navbar-fixed">
    <nav>
        <div class="nav-wrapper">
            <a href="/" class="brand-logo"><img src="/img/yoken-logo.png"></a>
            <a href="#" data-activates="slide-out" class="button-collapse"><i class="material-icons">menu</i></a>
            <ul id="nav-mobile" class="right hide-on-med-and-down">
                <li><a href="/webinars">Webinars</a></li>
                @if(!Auth::check())
                    <li><a class="dropdown-button" href="#!" data-activates="student-my-account" data-constrainwidth="false">My Account<i class="material-icons right">arrow_drop_down</i></a></li>
                @else
                    <li><a class="dropdown-button" href="#!" data-activates="my-account-list" data-constrainwidth="false">My Account<i class="material-icons right">arrow_drop_down</i></a></li>
                @endif
            </ul>
        </div>
    </nav>
</div>
