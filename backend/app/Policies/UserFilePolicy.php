<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserFile;

class UserFilePolicy
{
    public function download(User $user, UserFile $userFile): bool
    {
        return $user->id === $userFile->user_id;
    }

    public function delete(User $user, UserFile $userFile): bool
    {
        return $user->id === $userFile->user_id;
    }
}
