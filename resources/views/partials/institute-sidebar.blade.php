<header>
    <ul id="nav-mobile" class="side-nav fixed green">
        <li class="logo">
            <div id="logo-container" href="." class="brand-logo white">
                <img src="/img/yoken-logo.png">
            </div>
        </li>
        <li id="item-dashboard"><a href="/institute" class="collapsible-header waves-effect white-text">Dashboard</a></li>
        <li id="item-my-institute"><a href="/institute/my-institute" class="collapsible-header waves-effect white-text">My Institute</a></li>
        <li id="item-courses"><a href="/institute/courses" class="collapsible-header waves-effect white-text">Courses</a></li>
        <li id="item-coupons"><a href="/institute/coupons" class="collapsible-header waves-effect white-text">Coupons</a></li>
        <li id="item-report"><a href="/institute/report" class="collapsible-header waves-effect white-text">Report</a></li>
       <!-- <li id="item-my-institute"><a href="/institute/coupons" class="collapsible-header waves-effect white-text">Coupons</a></li>-->
        <!--
        <li class="bold">
            <ul class="collapsible collapsible-accordion">
                <li class="bold">
                    <a class="collapsible-header waves-effect white-text">Packages</a>
                    <div class="collapsible-body">
                        <ul>
                            <li id="item-add-packages"><a href="add-packages.php" class="grey-text text-darken-2">Add packages</a></li>
                            <li id="item-remove-packages"><a href="remove-packages.php" class="grey-text text-darken-2">View/Remove packages</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </li>
        -->

        <li id="item-change"><a href="/institute/change-password" class="collapsible-header waves-effect white-text">Change Password</a></li>
        <li id="item-logout"><a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="collapsible-header waves-effect white-text">Logout</a></li>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
    </ul>
</header>