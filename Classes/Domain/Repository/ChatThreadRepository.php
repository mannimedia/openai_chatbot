<?php

namespace ManniMedia\OpenaiChatbot\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

class ChatThreadRepository extends Repository
{
    public function findByThreadId(string $threadId)
    {
        $query = $this->createQuery();
        return $query->matching(
            $query->equals('threadId', $threadId)
        )->execute()->getFirst();
    }

    public function removeOldThreads(int $days = 30): void
    {
        $timestamp = time() - ($days * 86400);
        $query = $this->createQuery();
        $threads = $query->matching(
            $query->lessThan('lastActivity', $timestamp)
        )->execute();

        foreach ($threads as $thread) {
            $this->remove($thread);
        }
    }
}
