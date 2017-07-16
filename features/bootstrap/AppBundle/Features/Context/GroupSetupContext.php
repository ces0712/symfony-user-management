<?php

namespace AppBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\GroupManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Defines application features from the specific context.
 */
class GroupSetupContext implements Context, SnippetAcceptingContext
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var GroupManagerInterface
     */
    private $groupManager;
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
    public function __construct(GroupManagerInterface $groupManager, UserManagerInterface $userManager, EntityManagerInterface $em)
    {
        $this->groupManager = $groupManager;
        $this->userManager = $userManager;
        $this->em = $em;
    }
    
    /**
     * @Given there are Groups with the following details:
     */
    public function thereAreGroupsWithTheFollowingDetails(TableNode $groups)
    {
        foreach ($groups->getColumnsHash() as $key => $value) {
            $confirmationToken = isset($value['confirmation_token']) && $value['confirmation_token'] != ''
                ? $value['confirmation_token']
                : null;

            $group = $this->groupManager->createGroup('');
            $group->setName($value['name']);
            $group->setRoles(explode(',',$value['roles']));

            $this->groupManager->updateGroup($group);
            if (!empty($value['username'])) {
                $user = $this->userManager->findUserByUsername($value['username']);
                $user->addGroup($group);
                $this->userManager->updateUser($user);                
            }
        }
    }

}
