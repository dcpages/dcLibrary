<?php

namespace SynapseTest\User;

use PHPUnit_Framework_TestCase;
use Synapse\User\UserService;
use Synapse\User\Entity\User as UserEntity;

class UserServiceTest extends PHPUnit_Framework_TestCase
{
    const CURRENT_PASSWORD = '12345';

    public function setUp()
    {
        $this->userService = new UserService();

        $this->mockUserMapper = $this->getMockBuilder('Synapse\User\Mapper\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->userService->setUserMapper($this->mockUserMapper);
    }

    public function getCurrentPasswordHash()
    {
        return password_hash(self::CURRENT_PASSWORD, PASSWORD_BCRYPT);
    }

    public function getUserEntity()
    {
        $user = new UserEntity();

        $user->fromArray(['password' => $this->getCurrentPasswordHash()]);

        return $user;
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testExceptionThrownIfAttemptingToSetEmailWithoutSpecifyingCurrentPassword()
    {
        $this->userService->update(
            $this->getUserEntity(),
            ['email' => 'new@email.com']
        );
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testExceptionThrownIfAttemptingToSetPasswordWithoutSpecifyingCurrentPassword()
    {
        $this->userService->update(
            $this->getUserEntity(),
            ['password' => 'new_password']
        );
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testExceptionThrownIfAttemptingToSetEmptyEmail()
    {
        $this->userService->update(
            $this->getUserEntity(),
            [
                'current_password' => self::CURRENT_PASSWORD,
                'email'            => ''
            ]
        );
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testExceptionThrownIfAttemptingToSetEmptyPassword()
    {
        $this->userService->update(
            $this->getUserEntity(),
            [
                'current_password' => self::CURRENT_PASSWORD,
                'password'         => ''
            ]
        );
    }

    public function testUpdateChangesUserEntityValues()
    {
        $user = $this->getUserEntity();

        $this->userService->update(
            $user,
            [
                'current_password' => self::CURRENT_PASSWORD,
                'password'         => 'new_password',
                'email'            => 'new@email.com',
            ]
        );

        $this->assertTrue(password_verify('new_password', $user->getPassword()));
        $this->assertEquals('new@email.com', $user->getEmail());
    }

    public function testUpdatePassesUserEntityToUpdateMethodOfMapperAndReturnsResult()
    {
        $user = $this->getUserEntity();

        $this->mockUserMapper->expects($this->once())
            ->method('update')
            ->with($this->equalTo($user))
            ->will($this->returnValue('returnValue'));

        $returnValue = $this->userService->update(
            $user,
            [
                'current_password' => self::CURRENT_PASSWORD,
                'password'         => 'new_password',
                'email'            => 'new@email.com',
            ]
        );

        $this->assertEquals('returnValue', $returnValue);
    }
}
