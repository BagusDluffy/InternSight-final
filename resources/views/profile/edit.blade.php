@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12 text-center">
                    <h1 class="m-0">Edit Profil</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <!-- Form Edit Profil -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Profil</h3>
                        </div>
                        <div class="card-body">
                            <form id="profile-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-8">
                                        <!-- Nama -->
                                        <div class="form-group">
                                            <label for="name">Nama</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Tombol Reset Email & Password -->
                                        <div class="row mt-4 mb-4">
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-info btn-block" id="change-email-btn">
                                                    <i class="fas fa-envelope mr-2"></i> Ubah Email
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-warning btn-block" id="change-password-btn">
                                                    <i class="fas fa-key mr-2"></i> Ubah Password
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Hidden fields for email and password changes -->
                                        <input type="hidden" id="current_email_confirmation" name="current_email_confirmation">
                                        <input type="hidden" id="new_email" name="new_email">
                                        <input type="hidden" id="new_password" name="new_password">
                                        <input type="hidden" id="new_password_confirmation" name="new_password_confirmation">
                                    </div>

                                    <div class="col-md-4">
                                        <!-- Avatar -->
                                        <div class="form-group text-center">
                                            <label for="avatar">Avatar</label>
                                            <div class="text-center mb-3">
                                                <img src="{{ $user->avatar && file_exists(public_path('storage/avatars/' . $user->avatar))
                                                ? asset('storage/avatars/' . $user->avatar) 
                                                : asset('AdminLTE/dist/img/user2-160x160.jpg') }}"
                                                    alt="Avatar" class="profile-user-img img-fluid img-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="avatar" name="avatar" accept="image/jpeg, image/png">
                                                <label class="custom-file-label" for="avatar">Pilih foto (JPEG, PNG)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cropper Image Container -->
                                <div id="cropper-container" class="mt-4 mb-4" style="display: none;">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="m-0">Crop Avatar</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="img-container">
                                                        <img id="image" src="" alt="Image to crop" class="img-fluid">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div id="preview-container">
                                                        <h6>Preview Avatar</h6>
                                                        <img src="" id="preview-image" alt="Preview" class="img-thumbnail">
                                                    </div>

                                                </div>
                                            </div>
                                            <input type="hidden" id="cropped-avatar" name="cropped_avatar">
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Simpan Perubahan -->
                                <div class="row">
                                    <div class="col-md-6 offset-md-3">
                                        <button type="submit" class="btn btn-primary btn-block mt-4">
                                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Email Change Modal -->
<div class="modal fade" id="emailChangeModal" tabindex="-1" role="dialog" aria-labelledby="emailChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg rounded-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="emailChangeModalLabel">
                    <i class="fas fa-envelope mr-2"></i> Ubah Email
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="current_email_modal">Email Saat Ini</label>
                    <input type="email" class="form-control" id="current_email_modal" placeholder="Masukkan email saat ini untuk verifikasi">
                    <div id="current-email-error" class="invalid-feedback" style="display: none;">
                        Email yang Anda masukkan tidak cocok dengan email saat ini.
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label for="new_email_modal">Email Baru</label>
                    <input type="email" class="form-control" id="new_email_modal" placeholder="Masukkan email baru">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" id="save-email-change" class="btn btn-primary">
                    <i class="fas fa-check"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Password Change Modal -->
<div class="modal fade" id="passwordChangeModal" tabindex="-1" role="dialog" aria-labelledby="passwordChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg rounded-lg">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="passwordChangeModalLabel">
                    <i class="fas fa-key mr-2"></i> Ubah Password
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="new_password_modal">Password Baru</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="new_password_modal" placeholder="Masukkan password baru">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password_modal">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label for="confirm_password_modal">Konfirmasi Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="confirm_password_modal" placeholder="Konfirmasi password baru">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password_modal">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div id="password-mismatch-error" class="invalid-feedback" style="display: none;">
                        Password dan konfirmasi password tidak cocok.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" id="save-password-change" class="btn btn-primary">
                    <i class="fas fa-check"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg rounded-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="confirmationModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i> <span id="confirmation-title">Konfirmasi</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p id="confirmation-message" class="mb-0"></p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" id="confirm-changes" class="btn btn-danger">
                    <i class="fas fa-check"></i> Ya, Saya Yakin
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Container untuk preview */
    #preview-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    /* Styling untuk teks preview */
    #preview-container h6 {
        font-size: 14px;
        font-weight: bold;
        color: #555;
        margin-bottom: 10px;
    }

    /* Styling untuk gambar preview */
    #preview-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        /* Membuat bulat */
        border: 3px solid #007bff;
        /* Border biru */
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        /* Tambahkan efek shadow */
        object-fit: cover;
        transition: transform 0.3s ease-in-out;
    }

    /* Efek hover agar lebih interaktif */
    #preview-image:hover {
        transform: scale(1.1);
    }

    .modal-content {
        border-radius: 12px;
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/cropperjs@1.5.12/dist/cropper.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.5.12/dist/cropper.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Document loaded');

        // Avatar cropper functionality
        const avatarInput = document.getElementById('avatar');
        const cropperContainer = document.getElementById('cropper-container');
        const imageElement = document.getElementById('image');
        const previewImage = document.getElementById('preview-image');
        const croppedAvatarInput = document.getElementById('cropped-avatar');
        let cropper;

        // Tombol Ubah Email dan Password
        const changeEmailBtn = document.getElementById('change-email-btn');
        const changePasswordBtn = document.getElementById('change-password-btn');
        
        // Form dan hidden fields
        const profileForm = document.getElementById('profile-form');
        const currentEmailConfirmation = document.getElementById('current_email_confirmation');
        const newEmailField = document.getElementById('new_email');
        const newPasswordField = document.getElementById('new_password');
        const newPasswordConfirmation = document.getElementById('new_password_confirmation');
        
        // Email dan password dalam modal
        const currentEmailModal = document.getElementById('current_email_modal');
        const newEmailModal = document.getElementById('new_email_modal');
        const newPasswordModal = document.getElementById('new_password_modal');
        const confirmPasswordModal = document.getElementById('confirm_password_modal');
        
        // Tombol save di modal
        const saveEmailChange = document.getElementById('save-email-change');
        const savePasswordChange = document.getElementById('save-password-change');
        
        // Current user email
        const currentEmail = "{{ $user->email }}";
        
        // Change email button click
        changeEmailBtn.addEventListener('click', function() {
            // Reset form dan error messages
            currentEmailModal.value = '';
            newEmailModal.value = '';
            document.getElementById('current-email-error').style.display = 'none';
            
            // Show email change modal
            $('#emailChangeModal').modal('show');
        });
        
        // Change password button click
        changePasswordBtn.addEventListener('click', function() {
            // Reset form dan error messages
            newPasswordModal.value = '';
            confirmPasswordModal.value = '';
            document.getElementById('password-mismatch-error').style.display = 'none';
            
            // Show password change modal
            $('#passwordChangeModal').modal('show');
        });
        
        // Save email changes
        saveEmailChange.addEventListener('click', function() {
            // Validate current email
            if (currentEmailModal.value !== currentEmail) {
                document.getElementById('current-email-error').style.display = 'block';
                return;
            }
            
            // Check if new email is provided
            if (!newEmailModal.value) {
                alert('Silakan masukkan email baru.');
                return;
            }
            
            // Hide email modal
            $('#emailChangeModal').modal('hide');
            
            // Prepare confirmation modal
            document.getElementById('confirmation-title').textContent = 'Konfirmasi Perubahan Email';
            document.getElementById('confirmation-message').innerHTML = `
                <p class="text-muted">
                    Anda akan mengubah email akun Anda dari <span class="font-weight-bold">${currentEmail}</span> 
                    menjadi <span class="font-weight-bold">${newEmailModal.value}</span>.
                </p>
                <p class="text-muted">
                    Setelah email diubah, Anda mungkin perlu login kembali dengan email baru.
                </p>
                <p class="font-weight-bold">Apakah Anda yakin ingin melanjutkan?</p>
            `;
            
            // Set change type for confirmation handler
            document.getElementById('confirm-changes').setAttribute('data-change-type', 'email');
            
            // Show confirmation modal
            $('#confirmationModal').modal('show');
        });
        
        // Save password changes
        savePasswordChange.addEventListener('click', function() {
            // Check if password is provided
            if (!newPasswordModal.value) {
                alert('Silakan masukkan password baru.');
                return;
            }
            
            // Check if passwords match
            if (newPasswordModal.value !== confirmPasswordModal.value) {
                document.getElementById('password-mismatch-error').style.display = 'block';
                return;
            }
            
            // Hide password modal
            $('#passwordChangeModal').modal('hide');
            
            // Prepare confirmation modal
            document.getElementById('confirmation-title').textContent = 'Konfirmasi Perubahan Password';
            document.getElementById('confirmation-message').innerHTML = `
                <p class="text-muted">
                    Anda akan mengubah password akun Anda. Setelah password diubah, 
                    Anda mungkin perlu login kembali dengan password baru.
                </p>
                <p class="font-weight-bold">Apakah Anda yakin ingin melanjutkan?</p>
            `;
            
            // Set change type for confirmation handler
            document.getElementById('confirm-changes').setAttribute('data-change-type', 'password');
            
            // Show confirmation modal
            $('#confirmationModal').modal('show');
        });
        
        // Handle final confirmation
        document.getElementById('confirm-changes').addEventListener('click', function() {
            const changeType = this.getAttribute('data-change-type');
            
            if (changeType === 'email') {
                // Set hidden fields for email change
                currentEmailConfirmation.value = currentEmailModal.value;
                newEmailField.value = newEmailModal.value;
            } else if (changeType === 'password') {
                // Set hidden fields for password change
                newPasswordField.value = newPasswordModal.value;
                newPasswordConfirmation.value = confirmPasswordModal.value;
            }
            
            // Hide confirmation modal
            $('#confirmationModal').modal('hide');
            
            // Submit the form
            profileForm.submit();
        });

        // Avatar handling
        avatarInput.addEventListener('change', function(event) {
            console.log('Avatar input changed');
            const file = event.target.files[0];
            if (file) {
                // Update file label
                const fileLabel = document.querySelector('.custom-file-label');
                if (fileLabel) {
                    fileLabel.textContent = file.name;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    console.log('File reader loaded');
                    imageElement.src = e.target.result;

                    // Use the `load` event to ensure the image has been loaded
                    imageElement.onload = function() {
                        console.log('Image loaded');
                        cropperContainer.style.display = 'block'; // Show the cropper area

                        // Initialize Cropper
                        if (cropper) {
                            cropper.destroy(); // Destroy the old cropper if it exists
                        }
                        cropper = new Cropper(imageElement, {
                            aspectRatio: 1, // 1:1 ratio (square)
                            viewMode: 2, // Prevent the image from exceeding the container
                            responsive: true,
                            autoCropArea: 0.8,
                            background: false,
                            cropBoxResizable: true,
                            ready: function() {
                                console.log('Cropper ready');
                                updatePreview();
                            },
                            cropend: function() {
                                console.log('Crop ended');
                                updatePreview();
                            }
                        });
                    };
                };
                reader.readAsDataURL(file);
            }
        });

        // Function to update the preview image after cropping
        function updatePreview() {
            console.log('Updating preview');
            if (!cropper) return;
            
            const canvas = cropper.getCroppedCanvas({
                width: 200, // Preview size
                height: 200
            });
            const base64Image = canvas.toDataURL(); // Image in base64 format
            previewImage.src = base64Image; // Update the preview
            croppedAvatarInput.value = base64Image; // Save the cropped image
        }

        // Password visibility toggle functionality
        const togglePasswordButtons = document.querySelectorAll('.toggle-password');
        togglePasswordButtons.forEach(button => {
            button.addEventListener('click', function() {
                console.log('Password toggle clicked');
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('i');

                // Toggle password visibility
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    });
</script>
@endsection