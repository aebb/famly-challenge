<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ChildRepository;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=ChildRepository::class)
 * @ORM\Table(name="children")
 */
class Child implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    protected string $name;

    /**
     * @ORM\OneToMany(targetEntity="Attendance", mappedBy="child")
     */
    protected Collection $attendances;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     */
    protected DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime", name="updated_at", nullable=false)
     */
    protected DateTime $updatedAt;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->attendances = new ArrayCollection();
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name
        ];
    }

    public function __toString(): string
    {
        return sprintf('[id : %s, name: %s]', $this->id, $this->name);
    }
}
