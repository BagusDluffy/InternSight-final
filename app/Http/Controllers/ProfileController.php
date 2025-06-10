<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    // Menampilkan form edit profil
    public function edit()
    {
        $user = Auth::user(); // Ambil data user yang sedang login
        return view('profile.edit', compact('user'));
    }

    // Menyimpan perubahan profil
    public function update(Request $request)
    {
        $user = User::find(Auth::id());

        // Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'current_email_confirmation' => 'nullable|string',
            'new_email' => 'nullable|email|unique:users,email,' . Auth::id(),
            'new_password' => 'nullable|string|min:8',
            'new_password_confirmation' => 'nullable|string|same:new_password',
            'cropped_avatar' => 'nullable|string',
        ]);

        // Update nama user
        $user->name = $request->name;

        // Proses perubahan email
        $emailChanged = false;
        if ($request->filled('current_email_confirmation') && $request->filled('new_email')) {
            // Verifikasi bahwa email lama yang dimasukkan cocok dengan email user saat ini
            if ($request->current_email_confirmation !== $user->email) {
                return redirect()->back()->withErrors(['email' => 'Email lama yang Anda masukkan tidak sesuai.'])->withInput();
            }

            // Update email jika verifikasi berhasil
            $user->email = $request->new_email;
            $emailChanged = true;

            // Simpan perubahan pada user sebelum logout
            try {
                $user->save(); // Pastikan ini ada
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan perubahan: ' . $e->getMessage()])->withInput();
            }

            // Logout user
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'Email berhasil diubah. Silakan login kembali dengan email baru Anda.');
        }

        // Proses gambar yang di-crop (dalam format base64)
        if ($request->has('cropped_avatar') && !empty($request->cropped_avatar)) {
            // Simpan path avatar lama sebelum diupdate
            $oldAvatarPath = null;
            if ($user->avatar) {
                $oldAvatarPath = public_path('storage/avatars/' . $user->avatar);
            }

            // Generate nama file baru
            $imageName = 'avatar_' . time() . '.png';
            $path = public_path('storage/avatars/' . $imageName);

            // Decode dan simpan gambar baru
            $imageData = $request->input('cropped_avatar');
            $image = str_replace('data:image/png;base64,', '', $imageData);
            $image = str_replace(' ', '+', $image);

            // Pastikan direktori tersedia
            if (!is_dir(public_path('storage/avatars'))) {
                mkdir(public_path('storage/avatars'), 0755, true);
            }

            // Simpan gambar baru
            file_put_contents($path, base64_decode($image));

            // Hapus avatar lama jika ada
            if ($oldAvatarPath && file_exists($oldAvatarPath)) {
                unlink($oldAvatarPath);
            }

            // Update nama avatar pada user
            $user->avatar = $imageName;
        }

        // Update password jika disediakan
        $passwordChanged = false;
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
            $passwordChanged = true;
        }

        // Simpan perubahan pada user
        try {
            $user->save();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan perubahan: ' . $e->getMessage()])->withInput();
        }

        // Jika password diubah, logout user
        if ($passwordChanged) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login kembali dengan password baru Anda.');
        }

        // Jika tidak ada perubahan yang dilakukan
        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}