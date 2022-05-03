<?php

namespace App\Request\Child;

use App\Request\RequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ListRequest extends RequestModel
{
    protected ?string $search;

    /**
     * @Assert\Positive(message = "start parameter must be a positive integer")
     * @Assert\Regex(pattern = "/^\d+$/", message = "start parameter must be an integer")
     */
    protected ?string $start;

    /**
     * @Assert\Positive(message = "count parameter must be a positive integer")
     * @Assert\Regex(pattern = "/^\d+$/", message = "count parameter must be an integer")
     */
    protected ?string $count;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->search = $request->query->get('search');
        $this->start  = $request->query->get('start');
        $this->count  = $request->query->get('count');
    }

    public function getSearch(): ?string
    {
        return $this->search ?? null;
    }

    public function getStart(): ?int
    {
        return $this->start ?? null;
    }

    public function getCount(): ?int
    {
        return $this->count ?? null;
    }
}
