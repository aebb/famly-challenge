<?php

namespace App\Request\Attendance;

use App\Request\RequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class CreateRequest extends RequestModel
{
    /**
     * @Assert\NotBlank(message = "childId parameter must be present")
     * @Assert\Regex(pattern = "/^\d+$/", message = "childId parameter must be an integer")
     * @Assert\Positive(message = "childId parameter must be a positive integer")
     */
    protected ?string $childId;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $body = json_decode($request->getContent(), true);
        $this->childId  = $body['childId'] ?? null;
    }

    public function getId(): ?string
    {
        return $this->childId ?? null;
    }
}
