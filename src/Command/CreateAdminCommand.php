<?php

namespace App\Command;

use App\Interfaces\AuthInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'create:admin',
    description: 'Create admin user',
)]
class CreateAdminCommand extends Command
{

    private AuthInterface $authService;

    public function __construct(AuthInterface $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'Admin email')
            ->addArgument('password', InputArgument::OPTIONAL, 'Admin password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        if ($email) {
            $io->note(sprintf('You passed an email: %s', $email));
        }
        if ($password) {
            $io->note(sprintf('You passed an password: %s', $password));
        }

        try {
            $this->authService->registerOrUpdate(['email' => $email,'password' => $password, 'role'=>'ROLE_ADMIN']);
            $io->success('You have successfully generated a new admin user.');
            return Command::SUCCESS;
        } catch (Exception $e){
            $io->success($e->getMessage());
            return Command::FAILURE;
        }

    }
}
