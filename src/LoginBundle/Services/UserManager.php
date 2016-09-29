<?php
//src/AppBundle/Services/UserManager.php
namespace LoginBundle\Services;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class UserManager {
	/**
	 * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory
	 */
	private $encoderFactory;

	/**
	 * @param \Symfony\Component\Security\Core\Encoder\EncoderFactory
	 */
	public function __construct(EncoderFactory $encoderFactory)
	{
		$this->encoderFactory = $encoderFactory;
	}

	/**
	 * @param UserInterface $user
	 * @param string $sPasswordToEncode
	 */
	public function setUserPassword(UserInterface $user, $sPasswordToEncode)
	{
		//encode the given password
		$hashedPassword = $this->encoderFactory->getEncoder($user)
			->encodePassword($sPasswordToEncode, $user->getSalt());
			
		//set it in the eentity
		$user->setPassword($hashedPassword);
	}
}