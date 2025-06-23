<?php

namespace App\Doctrine\EntityListener;

use App\Entity\Post;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Mapping\PreFlush;
use Doctrine\ORM\Mapping\PreRemove;

class PostListener
{
    use EntityListenerTrait;

    #[PreFlush]
    #[PreRemove]
    public function refreshLastActivityDateOfUser(Post $post, EventArgs $args): void
    {
        $user = $post->getAuthor();

        $user->refreshLastActivityDate();
        $this->forceEntityUpdate($user, $args);
    }
}
