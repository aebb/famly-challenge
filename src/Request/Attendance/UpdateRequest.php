<?php

namespace App\Request\Attendance;

use App\Request\RequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateRequest extends RequestModel
{
    /**
     * @Assert\NotBlank(message = "id parameter must be present")
     * @Assert\Regex(pattern = "/^\d+$/", message = "id parameter must be an integer")
     * @Assert\Positive(message = "id parameter must be a positive integer")
     */
    protected ?string $id;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->id = $request->attributes->get('id');
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }
}
