<?php

namespace TelegramBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use React\EventLoop\Factory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TelegramBundle\Entity\Chat;
use TelegramBundle\Entity\Message;
use TelegramBundle\Entity\Setting;
use TelegramBundle\Entity\User;
use TelegramBundle\Repository\ChatRepository;
use TelegramBundle\Repository\MessageRepository;
use TelegramBundle\Repository\SettingRepository;
use TelegramBundle\Repository\UserRepository;
use unreal4u\TelegramAPI\Abstracts\TraversableCustomType;
use unreal4u\TelegramAPI\HttpClientRequestHandler;
use unreal4u\TelegramAPI\Telegram\Methods\GetUpdates;
use unreal4u\TelegramAPI\TgLog;

/**
 * Class GetUpdatesCommand
 */
class GetUpdatesCommand extends ContainerAwareCommand
{
    private $entityManager;
    /* @var $settingsRepository SettingRepository */
    private $settingsRepository;
    /* @var $messageRepository MessageRepository */
    private $messageRepository;
    /* @var $userRepository UserRepository */
    private $userRepository;
    /* @var $chatRepository ChatRepository */
    private $chatRepository;

    public function __construct(?string $name = null, EntityManagerInterface $entityManager = null)
    {
        parent::__construct($name);

        $this->entityManager = $entityManager;
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('telegram:get_updates')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->messageRepository = $this->entityManager->getRepository(Message::class);
        $this->chatRepository = $this->entityManager->getRepository(Chat::class);
        $this->settingsRepository = $this->entityManager->getRepository(Setting::class);

        $bot_api_key = $this->getContainer()->getParameter('bot_api_key');

        $loop = Factory::create();

        $tgLog = new TgLog($bot_api_key, new HttpClientRequestHandler($loop));

        $lastUpdate = $this->settingsRepository->getLastUpdate();
        $output->writeln('Request for updates from last id = ' . $lastUpdate);
        $getUpdates = new GetUpdates();

        $getUpdates->offset = $lastUpdate;
        $updatePromise = $tgLog->performApiRequest($getUpdates);

        $updatePromise->then(
            function (TraversableCustomType $updatesArray) use ($output)
            {
                try
                {
                    $output->writeln('Got ' . count($updatesArray->data) . ' updates');

                    /** @var $update \unreal4u\TelegramAPI\Telegram\Types\Update */
                    foreach ($updatesArray as $update)
                    {
                        if ($update->message->chat->type == 'group') // ignoring group chats
                        {
                            $output->writeln('Group message ignored.');

                            continue;
                        }

                        $user = $this->userRepository->getOrCreateUser($update->message->from);
                        $chat = $this->chatRepository->getOrCreateChat($update->message->chat);
                        $this->messageRepository->saveMessage($update->message, $chat, $user);
                        $this->settingsRepository->setLastUpdate($update->update_id);

                        $output->writeln('Message saved.');
                    }
                }
                catch (\Throwable $e)
                {
                    $output->writeln($e->getFile() . ':' . $e->getLine() . $e->getMessage());
                }
            }
        );

        $loop->run();
    }

}
