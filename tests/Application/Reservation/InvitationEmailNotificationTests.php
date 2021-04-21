<?php

require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/Notification/namespace.php');

class InvitationEmailNotificationTests extends TestBase
{
	public function setUp(): void
	{
		parent::setup();
	}

	public function teardown(): void
	{
		parent::teardown();
	}

	public function testSendsInvitationEmailToNewInvitees()
	{
		$ownerId = 828;
		$owner = new User();
		$inviteeId1 = 50;
		$invitee1 = new User();
		$inviteeId2 = 60;
		$invitee2 = new User();

		$instance1 = new TestReservation();
		$instance1->WithAddedInvitees(array($inviteeId1, $inviteeId2));

		$series = new TestReservationSeries();
		$series->WithCurrentInstance($instance1);
		$series->WithOwnerId($ownerId);

		$userRepo = $this->createMock('IUserRepository');
		$attributeRepo = $this->createMock('IAttributeRepository');

		$userRepo->expects($this->at(0))
			->method('LoadById')
			->with($this->equalTo($ownerId))
			->will($this->returnValue($owner));

		$userRepo->expects($this->at(1))
			->method('LoadById')
			->with($this->equalTo($inviteeId1))
			->will($this->returnValue($invitee1));

		$userRepo->expects($this->at(2))
			->method('LoadById')
			->with($this->equalTo($inviteeId2))
			->will($this->returnValue($invitee2));

		$notification = new InviteeAddedEmailNotification($userRepo, $attributeRepo);
		$notification->Notify($series);

		$this->assertEquals(2, count($this->fakeEmailService->_Messages));
		$lastExpectedMessage = new InviteeAddedEmail($owner, $invitee2, $series, $attributeRepo, $userRepo);
        $this->assertInstanceOf('InviteeAddedEmail', $this->fakeEmailService->_LastMessage);
//		$this->assertEquals($lastExpectedMessage, $this->fakeEmailService->_LastMessage);

	}
}
