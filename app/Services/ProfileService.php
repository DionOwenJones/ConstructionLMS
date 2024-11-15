<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function updateProfile(User $user, array $data): User
    {
        // Update basic information
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? $user->phone;
        $user->city = $data['city'] ?? $user->city;

        // Handle password update
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        // Handle avatar upload
        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            $this->updateAvatar($user, $data['avatar']);
        }

        $user->save();

        return $user;
    }

    private function updateAvatar(User $user, UploadedFile $avatar): void
    {
        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists("avatars/{$user->avatar}")) {
            Storage::disk('public')->delete("avatars/{$user->avatar}");
        }

        // Store new avatar
        $filename = time() . '.' . $avatar->getClientOriginalExtension();
        $avatar->storeAs('avatars', $filename, 'public');
        $user->avatar = $filename;
    }
}
