<?php

namespace TelegramBundle\Entity;

/**
 * User
 */
class User
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var int */
    private $telegramId;

    /**
     * @param int $id
     */
    public function setTelegramId(int $id): void
    {
        $this->telegramId = $id;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
