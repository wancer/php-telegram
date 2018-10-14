<?php

namespace TelegramBundle\Repository;

use TelegramBundle\Entity\User;
use unreal4u\TelegramAPI\Telegram\Types\User as TelegramUser;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param TelegramUser $telegramUser
     *
     * @return array|User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getOrCreateUser(TelegramUser $telegramUser)
    {
        $user = $this->findBy(['id' => $telegramUser->id]);
        if (!$user)
        {
            $user = new User();
            $user->setTelegramId($telegramUser->id);
            $user->setName($telegramUser->username);

            $entityManager = $this->getEntityManager();
            $entityManager->persist($user);
            $entityManager->flush($user);
        }

        return $user;
    }
}
