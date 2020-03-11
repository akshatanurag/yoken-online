<header>
    <ul id="nav-mobile" class="side-nav fixed green">
        <li class="logo">
            <div id="logo-container" href="." class="brand-logo white">
                <img src="/img/yoken-logo.png">
            </div>
        </li>
        <li id="item-dashboard"><a href="/admin/view-institutes" class="collapsible-header waves-effect white-text">Institutes</a></li>
        <li id="item-coupons"><a href="/admin/coupons" class="collapsible-header waves-effect white-text">Coupons</a></li>
        <li id="item-webinars"><a href="/admin/webinars" class="collapsible-header waves-effect white-text">Webinars</a></li>
        <li id="item-resources"><a href="/admin/resources" class="collapsible-header waves-effect white-text">Resources</a></li>
        <li id="item-report"><a href="/admin/report" class="collapsible-header waves-effect white-text">Courses Report</a></li>
        <li id="item-report-webinar"><a href="/admin/report-webinar" class="collapsible-header waves-effect white-text">Webinar Report</a></li>
        <li id="item-logout"><a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="collapsible-header waves-effect white-text">Logout</a></li>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
    </ul>
</header>