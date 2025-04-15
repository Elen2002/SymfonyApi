<?php

namespace App\Command;

use App\Interfaces\AuthInterface;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'generate:api:token',
    description: 'Generate api token for users',
)]
class GenerateApiTokenCommand extends Command
{
    private AuthInterface $authService;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $encoder;

    public function __construct(AuthInterface $authService, UserRepository $userRepository, UserPasswordHasherInterface $encoder)
    {
        parent::__construct();
        $this->authService = $authService;
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'User email')
            ->addArgument('password', InputArgument::OPTIONAL, 'User password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        if ($email) {
            $io->note(sprintf('You passed an argument: %s', $email));
        }
        if ($password) {
            $io->note(sprintf('You passed an argument: %s', $password));
        }
        try {
            /** @var JsonResponse $response */
            $response = $this->authService->login($email, $password, $this->userRepository, $this->encoder);

            $res = $response->getContent();
            $data = json_decode($res, true);
            $io->success('You have successfully generated a api token.');

            $output->writeln([
                'api token',
                '============',
                $data["message"],
            ]);
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Command failed. ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
