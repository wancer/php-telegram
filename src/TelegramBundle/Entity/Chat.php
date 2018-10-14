<?php

namespace TelegramBundle\Entity;

/**
 * Chat
 */
class Chat
{
    /** @var int */
    private $id;

    /** @var int */
    private $telegramId;

    /**
     * @param int $id
     */
    public function setTelegramId(int $id): void
    {
        $this->telegramId = $id;
    }
}
