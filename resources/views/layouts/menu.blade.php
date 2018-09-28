<li class="{{ Request::is('customers*') ? 'active' : '' }}">
    <a href="{!! route('admin.customers.index') !!}"><i class="fa fa-edit"></i><span>Customers</span></a>
</li>

@hasrole('admin')
    <li class="{{ Request::is('users*') ? 'active' : '' }}">
        <a href="{!! route('admin.users.index') !!}"><i class="fa fa-edit"></i><span>Users</span></a>
    </li>
@endhasrole
