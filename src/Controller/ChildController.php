<?php

namespace App\Controller;

use App\Request\Child\ListRequest;
use App\Service\ChildService;
use App\Utils\AppException;
use App\Utils\RequestValidator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChildController extends AbstractController
{
    private RequestValidator $validator;

    private ChildService $service;

    public function __construct(RequestValidator $validator, ChildService $service)
    {
        $this->validator = $validator;
        $this->service   = $service;
    }

    /**
     * @Route("/child", name="children.list.check.in", methods={"GET"})
     */
    public function executeListCheckedIn(Request $request): Response
    {
        try {
            return $this->json(
                $this->service->listChildrenCheckedIn($this->validator->process(new ListRequest($request))),
                Response::HTTP_OK
            );
        } catch (AppException $appException) {
            return $this->json($appException, $appException->getStatusCode());
        } catch (Exception $exception) {
            $appException = new AppException();
            return $this->json($appException, $appException->getStatusCode());
        }
    }
}
