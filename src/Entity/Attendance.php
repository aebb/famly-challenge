<?php

namespace App\Entity;

use App\Repository\AttendanceRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=AttendanceRepository::class)
 * @ORM\Table(name="attendances",indexes={
 *     @ORM\Index(name="index_enter", columns={"entered_at","id"}),
 * })
 */
class Attendance implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Child", inversedBy="attendances")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id")
     */
    protected Child $child;

    /**
     * @ORM\Column(type="datetime", name="entered_at", nullable=false)
     */
    protected DateTime $enteredAt;

    /**
     * @ORM\Column(type="datetime", name="left_at", nullable=true)
     */
    protected ?DateTime $leftAt;

    public function __construct(Child $child)
    {
        $this->child = $child;
        $this->enteredAt = new DateTime();
        $this->leftAt = null;
    }

    public function getLeftAt(): ?DateTime
    {
        return $this->leftAt;
    }

    public function setEnteredAt(DateTime $date): Attendance
    {
        $this->enteredAt = $date;
        return $this;
    }

    public function setLeftAt(DateTime $date): Attendance
    {
        $this->leftAt = $date;
        return $this;
    }


    public function jsonSerialize(): array
    {
        $response = [
            'id'       => $this->id,
            'child'    => $this->child->getName(),
            'checkIn'  => $this->enteredAt->format('Y-m-d H:i:s')
        ];

        if ($this->leftAt) {
            $response['checkOut'] = $this->leftAt->format('Y-m-d H:i:s');
        }

        return $response;
    }
}
