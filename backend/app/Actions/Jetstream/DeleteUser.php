<?php

declare(strict_types=1);

namespace App\Actions\Jetstream;

use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(mixed $user): void
    {
        // @todo テストで事故るのでコメントアウトしている、もしプロフィール画像導入したら有効にする
        // $user->deleteProfilePhoto();
        $user->tokens->each->delete();
        $user->delete();
    }
}
