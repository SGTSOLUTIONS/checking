@extends('layouts.')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4 text-center">User Management</h3>

    <!-- Button -->
    <button class="btn btn-primary mb-3" id="addUserBtn">Add New User</button>

    <!-- Table -->
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Role</th>
                <th>Gender</th><th>Status</th><th>Profile</th><th>Action</th>
            </tr>
        </thead>
        <tbody id="userTable">
            @foreach($users as $user)
                <tr data-id="{{ $user->id }}">
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->gender }}</td>
                    <td>{{ $user->status }}</td>
                    <td>
                        @if($user->profile)
                            <img src="{{ asset('storage/'.$user->profile) }}" width="40" height="40" class="rounded-circle">
                        @else
                            <span class="text-muted">No Image</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info editUser" data-id="{{ $user->id }}">Edit</button>
                        <button class="btn btn-sm btn-danger deleteUser" data-id="{{ $user->id }}">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="userForm" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add/Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="user_id" name="user_id">
            <div class="mb-2">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-2" id="passwordDiv">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-2">
                <label>Role</label>
                <select name="role" class="form-select" required>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
            </div>
            <div class="mb-2">
                <label>Gender</label>
                <select name="gender" class="form-select">
                    <option value="">Select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="mb-2">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="mb-2">
                <label>Profile Photo</label>
                <input type="file" name="profile" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- jQuery + Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function(){
    let modal = new bootstrap.Modal($('#userModal'));

    // Add
    $('#addUserBtn').click(function(){
        $('#userForm')[0].reset();
        $('#user_id').val('');
        $('#passwordDiv').show();
        modal.show();
    });

    // Edit
    $('.editUser').click(function(){
        let id = $(this).data('id');
        $.get('/users/' + id, function(user){
            $('#user_id').val(user.id);
            $('[name="name"]').val(user.name);
            $('[name="email"]').val(user.email);
            $('[name="role"]').val(user.role);
            $('[name="gender"]').val(user.gender);
            $('[name="status"]').val(user.status);
            $('#passwordDiv').hide();
            modal.show();
        });
    });

    // Save (Create/Update)
    $('#userForm').submit(function(e){
        e.preventDefault();
        let id = $('#user_id').val();
        let formData = new FormData(this);
        let url = id ? '/users/' + id : '/users';
        let method = id ? 'POST' : 'POST';
        if (id) formData.append('_method', 'PUT');

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            success: res => location.reload(),
            error: err => alert('Error saving user')
        });
    });

    // Delete
    $('.deleteUser').click(function(){
        if (!confirm('Are you sure to delete?')) return;
        let id = $(this).data('id');
        $.ajax({
            url: '/users/' + id,
            method: 'POST',
            data: {_method: 'DELETE', _token: '{{ csrf_token() }}'},
            success: res => location.reload(),
            error: err => alert('Error deleting user')
        });
    });
});
</script>
@endsection
