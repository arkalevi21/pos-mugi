@extends('layouts.app')

@section('title', 'Kelola Data Pegawai')

@section('styles')
<style>
    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #0d6efd;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }
    .role-badge {
        font-size: 0.8rem;
        padding: 4px 8px;
    }
    .table-actions {
        width: 120px;
        text-align: center;
    }
    .btn-action {
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 2px;
    }
    .password-field {
        position: relative;
    }
    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-people"></i> Kelola Data Pegawai
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPegawaiModal">
            <i class="bi bi-person-plus"></i> Tambah Pegawai
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Total Pegawai</div>
                        <div class="h3 mb-0">{{ $pegawai->count() }}</div>
                    </div>
                    <i class="bi bi-people" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Admin</div>
                        <div class="h3 mb-0">{{ $pegawai->where('role', 'admin')->count() }}</div>
                    </div>
                    <i class="bi bi-shield-check" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Kasir</div>
                        <div class="h3 mb-0">{{ $pegawai->where('role', 'kasir')->count() }}</div>
                    </div>
                    <i class="bi bi-cash-register" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="pegawaiTable">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th width="80">Avatar</th>
                        <th>Nama Pegawai</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Tanggal Bergabung</th>
                        <th class="table-actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawai as $index => $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="user-avatar">
                                {{ strtoupper(substr($user->nama_user, 0, 2)) }}
                            </div>
                        </td>
                        <td>
                            <strong>{{ $user->nama_user }}</strong>
                            @if(auth()->id() == $user->id_user)
                            <span class="badge bg-warning text-dark ms-2">Anda</span>
                            @endif
                        </td>
                        <td>
                            <code>{{ $user->username }}</code>
                        </td>
                        <td>
                            @if($user->role == 'admin')
                            <span class="badge bg-success role-badge">
                                <i class="bi bi-shield-check"></i> Admin
                            </span>
                            @else
                            <span class="badge bg-info role-badge">
                                <i class="bi bi-cash-register"></i> Kasir
                            </span>
                            @endif
                        </td>
                        <td>
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning btn-action" 
                                    onclick="editPegawai({{ $user->id_user }})"
                                    title="Edit" {{ auth()->id() == $user->id_user ? 'disabled' : '' }}>
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-action" 
                                    onclick="deletePegawai({{ $user->id_user }}, '{{ $user->nama_user }}')"
                                    title="Hapus" {{ auth()->id() == $user->id_user ? 'disabled' : '' }}>
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-person-x" style="font-size: 3rem;"></i>
                            <p class="mt-3">Belum ada data pegawai</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Pegawai -->
<div class="modal fade" id="addPegawaiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus"></i> Tambah Pegawai Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addPegawaiForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_user" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama_user" 
                               name="nama_user" required maxlength="100"
                               placeholder="Contoh: Budi Santoso">
                        <div class="invalid-feedback" id="nama_user_error"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" 
                               name="username" required maxlength="50"
                               placeholder="Contoh: budi123">
                        <div class="form-text">Username harus unik dan tidak boleh ada spasi</div>
                        <div class="invalid-feedback" id="username_error"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 password-field">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" 
                                       name="password" required minlength="6">
                                <span class="toggle-password" onclick="togglePassword('password')">
                                    <i class="bi bi-eye"></i>
                                </span>
                                <div class="invalid-feedback" id="password_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 password-field">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required minlength="6">
                                <span class="toggle-password" onclick="togglePassword('password_confirmation')">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Role/Jabatan</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="admin">Admin</option>
                            <option value="kasir" selected>Kasir</option>
                        </select>
                        <div class="invalid-feedback" id="role_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="submitAddPegawai">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Pegawai -->
<div class="modal fade" id="editPegawaiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil"></i> Edit Data Pegawai
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPegawaiForm">
                <input type="hidden" id="edit_id_user" name="id_user">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_user" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="edit_nama_user" 
                               name="nama_user" required maxlength="100">
                        <div class="invalid-feedback" id="edit_nama_user_error"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit_username" 
                               name="username" required maxlength="50">
                        <div class="form-text">Username harus unik dan tidak boleh ada spasi</div>
                        <div class="invalid-feedback" id="edit_username_error"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 password-field">
                                <label for="edit_password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="edit_password" 
                                       name="password" minlength="6">
                                <span class="toggle-password" onclick="togglePassword('edit_password')">
                                    <i class="bi bi-eye"></i>
                                </span>
                                <div class="form-text">Kosongkan jika tidak ingin mengubah</div>
                                <div class="invalid-feedback" id="edit_password_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 password-field">
                                <label for="edit_password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="edit_password_confirmation" 
                                       name="password_confirmation" minlength="6">
                                <span class="toggle-password" onclick="togglePassword('edit_password_confirmation')">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Role/Jabatan</label>
                        <select class="form-select" id="edit_role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="kasir">Kasir</option>
                        </select>
                        <div class="invalid-feedback" id="edit_role_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning" id="submitEditPegawai">
                        <i class="bi bi-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle form submit - Tambah Pegawai
        $('#addPegawaiForm').submit(function(e) {
            e.preventDefault();
            
            const formData = {
                _token: '{{ csrf_token() }}',
                nama_user: $('#nama_user').val(),
                username: $('#username').val(),
                password: $('#password').val(),
                password_confirmation: $('#password_confirmation').val(),
                role: $('#role').val()
            };
            
            $('#submitAddPegawai').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
            
            $.ajax({
                url: '{{ route("pemilik.pegawai.store") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showToast('Sukses', response.message, 'success');
                        $('#addPegawaiModal').modal('hide');
                        $('#addPegawaiForm')[0].reset();
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(field => {
                            $(`#${field}`).addClass('is-invalid');
                            $(`#${field}_error`).text(errors[field][0]);
                        });
                    } else {
                        showToast('Error', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                    }
                },
                complete: function() {
                    $('#submitAddPegawai').prop('disabled', false)
                        .html('<i class="bi bi-save"></i> Simpan');
                }
            });
        });
        
        // Reset validation on modal hide
        $('#addPegawaiModal').on('hidden.bs.modal', function() {
            $('#addPegawaiForm')[0].reset();
            $('#addPegawaiForm input, #addPegawaiForm select').removeClass('is-invalid');
            $('#role').val('kasir');
        });
        
        $('#editPegawaiModal').on('hidden.bs.modal', function() {
            $('#editPegawaiForm input, #editPegawaiForm select').removeClass('is-invalid');
        });
        
        // Password validation
        $('#password_confirmation').on('input', function() {
            const password = $('#password').val();
            const confirmPassword = $(this).val();
            
            if (password !== confirmPassword) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        $('#edit_password_confirmation').on('input', function() {
            const password = $('#edit_password').val();
            const confirmPassword = $(this).val();
            
            if (password && password !== confirmPassword) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
    });
    
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = $('#' + fieldId);
        const icon = field.next('.toggle-password').find('i');
        
        if (field.attr('type') === 'password') {
            field.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            field.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    }
    
    // Fungsi untuk edit pegawai
    function editPegawai(id) {
        $.ajax({
            url: '/pemilik/pegawai/' + id + '/edit',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const pegawai = response.data;
                    
                    $('#edit_id_user').val(pegawai.id_user);
                    $('#edit_nama_user').val(pegawai.nama_user);
                    $('#edit_username').val(pegawai.username);
                    $('#edit_role').val(pegawai.role);
                    
                    $('#editPegawaiModal').modal('show');
                } else {
                    showToast('Error', 'Gagal mengambil data pegawai', 'error');
                }
            },
            error: function() {
                showToast('Error', 'Terjadi kesalahan', 'error');
            }
        });
    }
    
    // Handle form submit - Edit Pegawai
    $('#editPegawaiForm').submit(function(e) {
        e.preventDefault();
        
        const id = $('#edit_id_user').val();
        const formData = {
            _token: '{{ csrf_token() }}',
            nama_user: $('#edit_nama_user').val(),
            username: $('#edit_username').val(),
            role: $('#edit_role').val(),
            _method: 'PUT'
        };
        
        // Tambahkan password jika diisi
        const password = $('#edit_password').val();
        if (password) {
            formData.password = password;
            formData.password_confirmation = $('#edit_password_confirmation').val();
        }
        
        $('#submitEditPegawai').prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
        
        $.ajax({
            url: '{{ route("pemilik.pegawai.update", ":id") }}'.replace(':id', id),
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showToast('Sukses', response.message, 'success');
                    $('#editPegawaiModal').modal('hide');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(field => {
                        $(`#edit_${field}`).addClass('is-invalid');
                        $(`#edit_${field}_error`).text(errors[field][0]);
                    });
                } else {
                    showToast('Error', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                }
            },
            complete: function() {
                $('#submitEditPegawai').prop('disabled', false)
                    .html('<i class="bi bi-save"></i> Update');
            }
        });
    });
    
    // Fungsi untuk hapus pegawai
    function deletePegawai(id, nama) {
        if (confirm(`Yakin ingin menghapus pegawai "${nama}"?`)) {
            $.ajax({
                url: '{{ route("pemilik.pegawai.destroy", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        showToast('Sukses', response.message, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    showToast('Error', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                }
            });
        }
    }
    
    // Helper function untuk toast/alert
    function showToast(title, message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 'alert-info';
        
        const icon = type === 'success' ? 'bi-check-circle' :
                    type === 'error' ? 'bi-exclamation-triangle' : 'bi-info-circle';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="bi ${icon}"></i>
                <strong>${title}</strong><br>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.alert-fixed').remove();
        $('body').append(alertHtml);
        
        setTimeout(() => {
            $('.alert-fixed').alert('close');
        }, 5000);
    }
</script>
@endsection