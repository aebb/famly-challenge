<?php

namespace App\Controller;

use App\Request\Attendance\CreateRequest;
use App\Request\Attendance\UpdateRequest;
use App\Service\AttendanceService;
use App\Utils\AppException;
use App\Utils\RequestValidator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AttendanceController extends AbstractController
{
    private RequestValidator $validator;

    private AttendanceService $service;

    public function __construct(RequestValidator $validator, AttendanceService $service)
    {
        $this->validator = $validator;
        $this->service   = $service;
    }

    /**
     * @Route("/attendance", name="attendance.check.in", methods={"POST"})
     */
    public function executeCheckIn(Request $request): Response
    {
        try {
            return $this->json(
                $this->service->checkIn($this->validator->process(new CreateRequest($request))),
                Response::HTTP_CREATED
            );
        } catch (AppException $appException) {
            return $this->json($appException, $appException->getStatusCode());
        } catch (Exception $exception) {
            $appException = new AppException();
            return $this->json($appException, $appException->getStatusCode());
        }
    }

    /**
     * @Route("/attendance/{id}", name="attendance.check.out", methods={"PATCH"})
     */
    public function executeCheckOut(Request $request): Response
    {
        try {
            return $this->json(
                $this->service->checkOut($this->validator->process(new UpdateRequest($request))),
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
