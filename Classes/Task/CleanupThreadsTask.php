<?php
declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Task;

use ManniMedia\OpenaiChatbot\Domain\Repository\ChatThreadRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider;
use TYPO3\CMS\Scheduler\Task\AbstractTask;
use TYPO3\CMS\Scheduler\ValidatorInterface;


class CleanupThreadsTask extends AbstractTask
{
    protected int $days = 30;

    public function execute(): bool
    {
        try {
            $repository = GeneralUtility::makeInstance(ChatThreadRepository::class);
            $repository->cleanupOldThreads($this->days);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function setDays(int $days): void
    {
        $this->days = $days;
    }

    public function getDays(): int
    {
        return $this->days;
    }

    public function validateAdditionalFields(array &$submittedData, AbstractAdditionalFieldProvider $parentObject): bool
    {
        return true;
    }

}
