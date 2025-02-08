<?php
declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Task;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class CleanupThreadsAdditionalFieldProvider extends AbstractAdditionalFieldProvider
{
    public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $schedulerModule): array
    {
        $additionalFields = [];
        
        if ($schedulerModule->CMD === 'edit') {
            $task = $schedulerModule->getCurrentTask();
            $taskInfo['days'] = $task->getDays();
        }

        $fieldId = 'task_days';
        $fieldCode = '<input type="number" class="form-control" name="tx_scheduler[days]" id="' . $fieldId . '" value="' . ($taskInfo['days'] ?? 30) . '" />';
        
        $additionalFields[$fieldId] = [
            'code' => $fieldCode,
            'label' => 'LLL:EXT:openai_chatbot/Resources/Private/Language/locallang.xlf:task.cleanup.days',
            'cshKey' => '_MOD_system_txschedulerM1',
            'cshLabel' => $fieldId
        ];

        return $additionalFields;
    }

    public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $schedulerModule): bool
    {
        $days = (int)$submittedData['days'];
        
        if ($days < 1) {
            $this->addMessage(
                'Days must be greater than 0',
                FlashMessage::ERROR
            );
            return false;
        }
        
        return true;
    }

    public function saveAdditionalFields(array $submittedData, AbstractTask $task): void
    {
        if ($task instanceof CleanupThreadsTask) {
            $task->setDays((int)$submittedData['days']);
        }
    }
}
