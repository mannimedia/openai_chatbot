<?php
declare(strict_types=1);

namespace ManniMedia\OpenaiChatbot\Controller;

use ManniMedia\OpenaiChatbot\Domain\Repository\ChatThreadRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class AdminController extends ActionController
{
    protected ChatThreadRepository $chatThreadRepository;
    protected ModuleTemplateFactory $moduleTemplateFactory;

    public function __construct(
        ChatThreadRepository $chatThreadRepository,
        ModuleTemplateFactory $moduleTemplateFactory
    ) {
        $this->chatThreadRepository = $chatThreadRepository;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    public function listAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        
        $threads = $this->chatThreadRepository->findAll();
        $moduleTemplate->assignMultiple([
            'threads' => $threads,
            'statistics' => [
                'total' => count($threads),
                'active' => $this->chatThreadRepository->countActive(),
                'archived' => $this->chatThreadRepository->countArchived()
            ]
        ]);
        
        return $this->htmlResponse($moduleTemplate->render());
    }

    public function showAction(string $threadId): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        
        $thread = $this->chatThreadRepository->findByThreadId($threadId);
        if (!$thread) {
            $this->addFlashMessage('Thread not found', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
            return $this->redirect('list');
        }
        
        $moduleTemplate->assign('thread', $thread);
        return $this->htmlResponse($moduleTemplate->render());
    }

    public function deleteAction(string $threadId): ResponseInterface
    {
        $thread = $this->chatThreadRepository->findByThreadId($threadId);
        if ($thread) {
            $this->chatThreadRepository->remove($thread);
            $this->addFlashMessage('Thread deleted successfully');
        }
        
        return $this->redirect('list');
    }

    public function archiveAction(string $threadId): ResponseInterface
    {
        $thread = $this->chatThreadRepository->findByThreadId($threadId);
        if ($thread) {
            $thread->setIsArchived(true);
            $this->chatThreadRepository->update($thread);
            $this->addFlashMessage('Thread archived successfully');
        }
        
        return $this->redirect('list');
    }
}
