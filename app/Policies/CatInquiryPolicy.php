<?php

namespace App\Policies;

use App\Models\CatInquiry;
use App\Models\User;

class CatInquiryPolicy
{
    /**
     * Buyer or listing owner can view an inquiry.
     */
    public function view(User $user, CatInquiry $catInquiry): bool
    {
        return $user->id === $catInquiry->buyer_id
            || $user->id === $catInquiry->listing->user_id;
    }

    /**
     * Only the listing owner (seller) can update status / add notes.
     */
    public function update(User $user, CatInquiry $catInquiry): bool
    {
        return $user->id === $catInquiry->listing->user_id;
    }

    /**
     * Buyer or listing owner can delete an inquiry.
     */
    public function delete(User $user, CatInquiry $catInquiry): bool
    {
        return $user->id === $catInquiry->buyer_id
            || $user->id === $catInquiry->listing->user_id;
    }
}
