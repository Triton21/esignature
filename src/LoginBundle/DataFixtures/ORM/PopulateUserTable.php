<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Appbundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;



class PopulateUserTable implements FixtureInterface, ContainerAwareInterface {
    
    /**
     * 
     * @var ContainerInterface
     */
    private $container;
    
    public function load(ObjectManager $manager) {
        $user1 = new User();
        $user1->setUsername('cindy');
        $user1->setFirstname('cindy');
        $user1->setLastname('cindy');
        $user1->setEmail('cindy@cindy.com');
        $user1->setRoles('ROLE_USER');
        $user1->setIsAdmin(false);
        $user1->setPassword($this->hassPassword($user1, 'cindypass'));
        $manager->persist($user1);
        
        $user2 = new User();
        $user2->setUsername('Ray');
        $user2->setFirstname('Ray');
        $user2->setLastname('Donovan');
        $user2->setEmail('ray@donovan.com');
        $user2->setRoles('ROLE_ADMIN');
        $user2->setIsAdmin(true);
        $user2->setPassword($this->hassPassword($user2, 'raypass'));
        $manager->persist($user2);
        $manager->flush();
        
        $user3 = new User();
        $user3->setUsername('Ragnar');
        $user3->setFirstname('Ragnar');
        $user3->setLastname('Donovan');
        $user3->setEmail('Ragnar@ragnar.com');
        $user3->setRoles('ROLE_SUPER_ADMIN');
        $user3->setIsAdmin(true);
        $user3->setPassword($this->hassPassword($user2, 'raypass'));
        $manager->persist($user3);
        $manager->flush();
    }
    
    /**
     * 
     * @param ContainerInterface
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    
    
    /**
     * 
     * @param User $user, $thePassword
     * @return the newly hashed password
     */
    public function hassPassword(User $user, $thePassword) {
        $encoder = $this->container->get('security.encoder_factory')
                ->getEncoder($user);
        return $encoder->encodePassword($thePassword, $user->getSalt());
    }
}



