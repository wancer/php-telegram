<?php

namespace TelegramBundle\Repository;

use TelegramBundle\Entity\Setting;

/**
 * SettingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SettingRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return int
     */
    public function getLastUpdate(): int
    {
        /* @var $setting Setting */
        $setting = $this->findOneBy(['name' => 'update']);
        if (!$setting)
        {
            return 0;
        }

        return $setting->getValue() + 1;
    }

    /**
     * @param int $value
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setLastUpdate(int $value)
    {
        $setting = $this->findOneBy(['name' => 'update']);
        if (!$setting)
        {
            $setting = new Setting();
            $setting->setName('update');
        }
        $setting->setValue($value);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($setting);
        $entityManager->flush($setting);
    }
}
