<?php

namespace App\Utils;

use App\Request\RequestModel;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestValidator
{
    protected ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @throws Exception
     */
    public function process(RequestModel $model): RequestModel
    {
        $errors = $this->validator->validate($model);
        if ($errors->count()) {
            $message = [];
            foreach ($errors as $error) {
                $message[] = $error->getMessage();
            }
            throw new AppException(
                implode(' & ', $message),
                ErrorCode::ERROR_CODE_AUTH,
                null,
                Response::HTTP_BAD_REQUEST
            );
        }

        return $model;
    }
}
