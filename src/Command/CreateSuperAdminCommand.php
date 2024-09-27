<?php

namespace App\Command;

use App\Entity\User;
use App\Exceptions\TenantUserDatabaseNotCreatedException;
use App\Repository\InstanceRepository;
use App\Services\MultiTenantDatabaseHandler;
use BadMethodCallException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'superadmin:create',
    description: 'Create a new Super Admin',
    aliases: [],
    hidden: false
)]
class CreateSuperAdminCommand extends Command
{
    public const EMAIL_ARGUMENT = "email";
    public const PASSWORD_ARGUMENT = "password";

    public function __construct(
        private readonly InstanceRepository $instanceRepository,
        private readonly MultiTenantDatabaseHandler $tenancyHandler,
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface $manager,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument(self::EMAIL_ARGUMENT, InputArgument::REQUIRED, 'email of the super admin')
            ->addArgument(self::PASSWORD_ARGUMENT, InputArgument::REQUIRED, 'password of the super admin');
    }

    /**
     * @throws BadMethodCallException
     */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument(self::EMAIL_ARGUMENT);
        $password = $input->getArgument(self::PASSWORD_ARGUMENT);
        
        $superAdmin = new User();
        $superAdmin->setEmail($email)
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setAddress('')
            ->setPassword($this->hasher->hashPassword($superAdmin, $password))
            ->setFirstName('Super')
            ->setLastName('Admin')
            ->setPhone('1234567890')
            ->setInstance(null);
        $this->manager->persist($superAdmin);
        $this->manager->flush();

        return Command::SUCCESS;
    }
}
