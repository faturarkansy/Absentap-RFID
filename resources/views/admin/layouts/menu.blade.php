<div class="side-menu">
    <div class="login-info">
        <img src="{{ !empty(auth()->user()->image) ? 'avatar/' . auth()->user()->image : 'images/man.png' }}"
            alt="">
        <h1>{{ auth()->user()->name }}</h1>
        <p class="mb-2">{{ auth()->user()->email }}</p>
        <a href="admin/profile" class="btn btn-outline-primary" style="padding: 3px 12px; font-size: 10px;">Edit
            Profile</a>
    </div>
    <ul>
        <li class="list-title">Configuration</li>
        <li class="{{ request()->segment(2) == 'dashboard' || request()->segment(2) == null ? 'active' : null }}"><a
                href="admin/dashboard"><i class="fa fa-home"></i> Dashboard</a></li>
        @if (auth()->user()->hasPermissionTo('settings') || auth()->user()->hasRole('Super Admin'))
            <li class="{{ request()->segment(2) == 'settings' ? 'active' : null }}"><a href="admin/settings"><i
                        class="fa-solid fa-wrench"></i> Settings</a></li>
        @endif
        <li class="list-title">Master Data</li>
        @if (auth()->user()->hasPermissionTo('user-list') || auth()->user()->hasRole('Super Admin'))
            <li class="{{ request()->segment(2) == 'users' ? 'active' : null }}"><a href="admin/users"><i
                        class="fa-solid fa-user-gear"></i> Users</a></li>
        @endif
        @if (auth()->user()->hasPermissionTo('HolidayDate') || auth()->user()->hasRole('Super Admin'))
            <li class="{{ request()->segment(2) == 'holidays' ? 'active' : null }}"><a href="admin/holidays"><i
                        class="fa-regular fa-calendar-days"></i> Holidays</a></li>
        @endif
        <li class="list-title">Data List</li>
        @if (auth()->user()->hasPermissionTo('student') || auth()->user()->hasRole('Super Admin'))
            <li class="{{ request()->segment(2) == 'students' ? 'active' : null }}"><a href="admin/students"><i
                        class="fa-solid fa-users"></i> Students</a></li>
        @endif
        @if (auth()->user()->hasPermissionTo('SchoolTime') || auth()->user()->hasRole('Super Admin'))
            <li class="{{ request()->segment(2) == 'school-time' ? 'active' : null }}"><a href="admin/school-time"><i
                        class="fa-solid fa-clock"></i> School Time</a></li>
        @endif
        @if (auth()->user()->hasRole('Super Admin'))
            <li class="{{ request()->segment(2) == 'parent-report' ? 'active' : null }}"><a
                    href="admin/parent-report"><i class="fa-solid fa-list"></i> Presence</a></li>
        @endif
    </ul>
</div>
