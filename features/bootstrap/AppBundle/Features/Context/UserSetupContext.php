<?php

namespace AppBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Defines application features from the specific context.
 */
class UserSetupContext implements Context, SnippetAcceptingContext
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;
    /**
     * @var EntityManagerInterface
     */
    private $em;    

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(UserManagerInterface $userManager, EntityManagerInterface $em)
    {
        $this->userManager = $userManager;
        $this->em = $em;
    }
    
    /**
     * @Given there are Users with the following details:
     */
    public function thereAreUsersWithTheFollowingDetails(TableNode $users)
    {
        foreach ($users->getColumnsHash() as $key => $value) {
            $confirmationToken = isset($value['confirmation_token']) && $value['confirmation_token'] != ''
                ? $value['confirmation_token']
                : null;

            $user = $this->userManager->createUser();
            

            $user->setEnabled(true);
            $user->setUsername($value['username']);
            $user->setEmail($value['email']);
            $user->setPlainPassword($value['password']);
            $user->setName($value['name']);
            // $user->addRole($value['role']);
            $user->setConfirmationToken($confirmationToken);

            if ( ! empty($confirmationToken)) {
                $user->setPasswordRequestedAt(new \DateTime('now'));
            }

            $this->userManager->updateUser($user);
        }
    }

}
