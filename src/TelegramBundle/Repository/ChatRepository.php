<?php

namespace TelegramBundle\Repository;

use unreal4u\TelegramAPI\Telegram\Types\Chat as TelegramChat;
use TelegramBundle\Entity\Chat;

/**
 * ChatRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ChatRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param TelegramChat $telegramChat
     *
     * @return array|Chat
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getOrCreateChat(TelegramChat $telegramChat)
    {
        $user = $this->findBy(['id' => $telegramChat->id]);
        if (!$user)
        {
            $user = new Chat();
            $user->setTelegramId($telegramChat->id);

            $entityManager = $this->getEntityManager();
            $entityManager->persist($user);
            $entityManager->flush($user);
        }

        return $user;
    }
}
