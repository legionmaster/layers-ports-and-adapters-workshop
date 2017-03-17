<?php
declare(strict_types = 1);

namespace Meetup\Infrastructure\Persistence\Common;

use Meetup\Domain\Model\Meetup;
use Meetup\Domain\Model\MeetupId;
use Ramsey\Uuid\Uuid;

trait AbstractMeetupRepository
{
    public function byId(MeetupId $id): Meetup
    {
        foreach ($this->persistedMeetups() as $meetup) {
            if ($meetup->id()->equals($id)) {
                return $meetup;
            }
        }

        throw new \RuntimeException('Meetup not found');
    }

    /**
     * @param \DateTimeImmutable $now
     * @return Meetup[]
     */
    public function upcomingMeetups(\DateTimeImmutable $now): array
    {
        return array_values(array_filter($this->persistedMeetups(), function (Meetup $meetup) use ($now) {
            return $meetup->isUpcoming($now);
        }));
    }

    /**
     * @param \DateTimeImmutable $now
     * @return Meetup[]
     */
    public function pastMeetups(\DateTimeImmutable $now): array
    {
        return array_values(array_filter($this->persistedMeetups(), function (Meetup $meetup) use ($now) {
            return !$meetup->isUpcoming($now);
        }));
    }

    /**
     * @return Meetup[]
     */
    abstract protected function persistedMeetups(): array;

    public function nextIdentity(): MeetupId
    {
        return MeetupId::fromString((string)Uuid::uuid4());
    }
}