@extends('home')

@section('subtitle', 'Permissions')

@section('content_body')
    <div class="row pt-3">
        <div class="col-12">
            <a href="{{ route('permissions.create') }}" class="btn btn-dark mb-3">Create New Permission</a>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Permissions list</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Created at</th>
                                <th>Updated at</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td>{{ $permission->id }}</td>
                                    <td>{{ $permission->name }}</td>
                                    <td>{{ $permission->created_at->format('d.m.Y') }}</td>
                                    <td>{{ $permission->updated_at->format('d.m.Y') }}</td>
                                    <td class="d-flex align-items-center">
                                        <a href="{{ route('permissions.edit', $permission->id) }}"><i class="fas fa-pen text-dark"></i></a>
                                        <form action="{{ route('permissions.delete', $permission->id) }}" method="post" onsubmit="return confirm('Are you sure want to delete this permission?');">
                                            @method('DELETE')
                                            @csrf
                                            <button class="btn" type="submit"><i class="fas fa-trash text-dark"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <h4>Assign Permissions to Users</h4>
    <form action="{{ route('permissions.assign') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="user_id">User</label>
            <select name="user_id" id="user_id" class="form-control @error('user_id') border-danger @enderror">
                <option value="">Select User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            @error('user_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="permissions" class="form-label">Permissions</label>
            <select name="permissions[]" id="permissions" class="form-control @error('permissions') border-danger @enderror" multiple>
                @foreach($permissions as $permission)
                    <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                @endforeach
            </select>
            @error('permissions')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-dark">Assign Permissions</button>
    </form>

    @push('js')
        <script>
            const users = @json($users); // Pass users with their permissions to JavaScript
        
            document.getElementById('user_id').addEventListener('change', function () {
                const userId = this.value;
                const user = users.find(u => u.id == userId);
        
                // Clear existing selections
                const permissionsSelect = document.getElementById('permissions');
                [...permissionsSelect.options].forEach(option => {
                    option.selected = false;
                });
        
                // Select user's current permissions
                if (user) {
                    user.permissions.forEach(permission => {
                        const option = [...permissionsSelect.options].find(opt => opt.value === permission.name);
                        if (option) option.selected = true;
                    });
                }
            });
        </script>
    @endpush
@stop
