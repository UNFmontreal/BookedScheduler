<?php

require_once(ROOT_DIR . 'lib/Email/Messages/ParticipantAddedEmail.php');

class ParticipantDeletedEmailNotification implements IReservationNotification
{
    /**
     * @var IUserRepository
     */
    private $userRepository;

    /**
     * @var IAttributeRepository
     */
    private $attributeRepository;

    public function __construct(IUserRepository $userRepository, IAttributeRepository $attributeRepository)
    {
        $this->userRepository = $userRepository;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @param ReservationSeries $reservationSeries
     */
    public function Notify($reservationSeries)
    {
        $instance = $reservationSeries->CurrentInstance();
        $owner = $this->userRepository->LoadById($reservationSeries->UserId());

        foreach ($instance->UnchangedParticipants() as $userId) {
            $participant = $this->userRepository->LoadById($userId);

            $message = new ParticipantDeletedEmail($owner, $participant, $reservationSeries, $this->attributeRepository, $this->userRepository);
            ServiceLocator::GetEmailService()->Send($message);
        }
    }
}
