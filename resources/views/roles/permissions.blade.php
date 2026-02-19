@extends('layouts.app')

@section('title', 'Manage Permissions — {{ $role->name }} - AklatBayon')

@push('styles')
<style>
    .permission-group {
        border: 1px solid var(--light-gray);
        border-radius: 10px;
        margin-bottom: 20px;
        overflow: hidden;
    }
    .permission-group-header {
        background: #f8f9fa;
        padding: 14px 20px;
        font-weight: 700;
        font-size: 15px;
        color: var(--primary-navy);
        border-bottom: 1px solid var(--light-gray);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .permission-group-header .group-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 14px;
    }
    .permission-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.15s;
    }
    .permission-item:last-child {
        border-bottom: none;
    }
    .permission-item:hover {
        background: #fafbfc;
    }
    .permission-item .perm-info {
        display: flex;
        flex-direction: column;
    }
    .permission-item .perm-name {
        font-weight: 600;
        font-size: 14px;
        color: var(--primary-navy);
    }
    .permission-item .perm-key {
        font-size: 12px;
        color: var(--gray);
        font-family: 'Consolas', 'Courier New', monospace;
    }

    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        width: 48px;
        height: 26px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #ccc;
        transition: 0.3s;
        border-radius: 26px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .toggle-switch input:checked + .toggle-slider {
        background-color: var(--success);
    }
    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(22px);
    }
    .toggle-switch input:focus + .toggle-slider {
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.25);
    }

    /* Toggle All */
    .toggle-all-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        background: #f0f7f0;
        border-bottom: 1px solid #d4edda;
    }
    .toggle-all-bar label {
        font-weight: 600;
        font-size: 13px;
        color: #2d6a2e;
        cursor: pointer;
    }

    /* Summary strip */
    .permission-summary {
        background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-blue) 100%);
        color: #fff;
        border-radius: 10px;
        padding: 20px 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .permission-summary .summary-label {
        font-size: 14px;
        opacity: 0.8;
    }
    .permission-summary .summary-count {
        font-size: 28px;
        font-weight: 700;
    }

    /* Group colors */
    .group-books    { background: var(--primary-red); }
    .group-catalog  { background: var(--info); }
    .group-reports  { background: var(--purple); }
    .group-users    { background: var(--primary-blue); }
    .group-system   { background: #333; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-sliders-h me-2"></i>Manage Permissions</h1>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Roles
    </a>
</div>

{{-- Role Summary --}}
<div class="permission-summary">
    <div>
        <div class="summary-label">Managing permissions for</div>
        <div class="summary-count">{{ $role->name }}</div>
    </div>
    <div class="text-end">
        <div class="summary-label">Permissions enabled</div>
        <div class="summary-count" id="perm-counter">{{ count($rolePermissionIds) }}</div>
    </div>
</div>

<form action="{{ route('roles.permissions.update', $role) }}" method="POST" id="permissions-form">
    @csrf
    @method('PUT')

    @foreach($permissions as $groupName => $groupPermissions)
    <div class="permission-group">
        <div class="permission-group-header">
            @php
                $groupIcons = [
                    'Books'   => 'fa-book',
                    'Catalog' => 'fa-tags',
                    'Reports' => 'fa-chart-bar',
                    'Users'   => 'fa-users',
                    'System'  => 'fa-cog',
                ];
                $groupColors = [
                    'Books'   => 'group-books',
                    'Catalog' => 'group-catalog',
                    'Reports' => 'group-reports',
                    'Users'   => 'group-users',
                    'System'  => 'group-system',
                ];
            @endphp
            <div class="group-icon {{ $groupColors[$groupName] ?? 'group-system' }}">
                <i class="fas {{ $groupIcons[$groupName] ?? 'fa-lock' }}"></i>
            </div>
            {{ $groupName }}
            <span class="badge bg-secondary ms-auto group-count" data-group="{{ $groupName }}">
                {{ $groupPermissions->filter(fn($p) => in_array($p->id, $rolePermissionIds))->count() }}/{{ $groupPermissions->count() }}
            </span>
        </div>

        {{-- Toggle All for this group --}}
        <div class="toggle-all-bar">
            <label for="toggle-all-{{ Str::slug($groupName) }}">Toggle all {{ $groupName }} permissions</label>
            <label class="toggle-switch">
                <input type="checkbox"
                       id="toggle-all-{{ Str::slug($groupName) }}"
                       class="toggle-all-group"
                       data-group="{{ Str::slug($groupName) }}"
                       {{ $groupPermissions->every(fn($p) => in_array($p->id, $rolePermissionIds)) ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        @foreach($groupPermissions as $permission)
        <div class="permission-item">
            <div class="perm-info">
                <span class="perm-name">{{ $permission->label }}</span>
                <span class="perm-key">{{ $permission->description ?? $permission->name }}</span>
            </div>
            <label class="toggle-switch">
                <input type="checkbox"
                       name="permissions[]"
                       value="{{ $permission->id }}"
                       class="perm-toggle perm-group-{{ Str::slug($groupName) }}"
                       data-group="{{ Str::slug($groupName) }}"
                       {{ in_array($permission->id, $rolePermissionIds) ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>
        @endforeach
    </div>
    @endforeach

    <div class="d-flex gap-2 mt-4 mb-4">
        <button type="submit" class="btn btn-success btn-lg">
            <i class="fas fa-save me-1"></i> Save Permissions
        </button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-lg">
            <i class="fas fa-times me-1"></i> Cancel
        </a>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const counter = document.getElementById('perm-counter');

    function updateCounter() {
        counter.textContent = document.querySelectorAll('.perm-toggle:checked').length;
    }

    // Individual toggle
    document.querySelectorAll('.perm-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            updateGroupToggle(this.dataset.group);
            updateCounter();
        });
    });

    // Group toggle-all
    document.querySelectorAll('.toggle-all-group').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const group = this.dataset.group;
            const checked = this.checked;
            document.querySelectorAll('.perm-group-' + group).forEach(cb => {
                cb.checked = checked;
            });
            updateCounter();
        });
    });

    function updateGroupToggle(group) {
        const items = document.querySelectorAll('.perm-group-' + group);
        const allChecked = Array.from(items).every(cb => cb.checked);
        const toggleAll = document.getElementById('toggle-all-' + group);
        if (toggleAll) toggleAll.checked = allChecked;
    }
});
</script>
@endpush
