@foreach($users as $u)
<tr>
    <td>
        <div style="display:flex;align-items:center;gap:8px">
            <div class="sidebar-avatar" style="width:28px;height:28px;font-size:11px;border-radius:7px">
                @if($u->avatar)
                    <img src="{{ asset($u->avatar) }}" style="width:100%;height:100%;border-radius:7px;object-fit:cover;">
                @else
                    {{ $u->initials }}
                @endif
            </div>
            <strong>{{ $u->name }}</strong>
        </div>
    </td>
    <td>{{ $u->email }}</td>
    <td>
        @php $role = $u->roles->first()->name ?? 'N/A'; @endphp
        <span class="badge {{ $role === 'admin' ? 'badge-red' : ($role === 'supervisor' ? 'badge-amber' : 'badge-blue') }}">
            {{ ucfirst($role) }}
        </span>
    </td>
    <td>{{ $u->members->first()->group->name ?? '—' }}</td>
    <td><span class="badge badge-green">{{ ucfirst($u->status ?? 'active') }}</span></td>
    <td>{{ $u->created_at->format('M d') }}</td>
    <td>
        <div style="display:flex;gap:4px">
            <button class="btn btn-ghost btn-sm" onclick="showUserPreview({{ $u->id }})" title="View Profile"><i class="uil uil-eye"></i></button>
            <button class="btn btn-ghost btn-sm" onclick="toast('Edit user modal…','<i class=\'uil uil-edit\'></i>')"><i class="uil uil-edit"></i></button>
            <button class="btn btn-ghost btn-sm" onclick="toast('User deactivated','<i class=\'uil uil-user-times\'></i>')"><i class="uil uil-user-times"></i></button>
        </div>
    </td>
</tr>
@endforeach
