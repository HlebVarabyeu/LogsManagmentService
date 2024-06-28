<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Http;

use App\Application\CountLogs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

class LogsCountController extends AbstractController
{
    public function __construct(private readonly CountLogs $countLogs)
    {
    }

    #[Route('/count', name: 'logs_count', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $searchParams = [
            'serviceNames' => $request->query->all('serviceNames'),
            'startDate' => $request->query->get('startDate'),
            'endDate' => $request->query->get('endDate'),
            'statusCode' => (int) $request->query->get('statusCode')
        ];
        if (count($violations = $this->validatedParams($searchParams)) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $startDate = null;
        $endDate = null;
        $serviceNames = $searchParams['serviceNames'] ?? [];
        try {
            if (! empty($searchParams['startDate'])) {
                $startDate = new \DateTimeImmutable($searchParams['startDate']);
            }

            if (! empty($searchParams['endDate'])) {
                $endDate = new \DateTimeImmutable($searchParams['endDate']);
            }
        } catch (\Exception) {
            return $this->json(['errors' => 'Invalid date format.'], Response::HTTP_BAD_REQUEST);
        }

        $count = $this->countLogs->process(
            $serviceNames,
            $startDate,
            $endDate,
            $searchParams['statusCode'],
        );

        return $this->json(['counter' => $count], Response::HTTP_OK);
    }

    /**
     * Validate input data according to spec.
     *
     * @param array $searchParams
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    private function validatedParams(array $searchParams): ConstraintViolationListInterface
    {
        $constraints = new Assert\Collection([
            'serviceNames' => new Assert\Optional([
                new Assert\Type('array'),
                new Assert\All([new Assert\Type('string')])
            ]),
            'startDate' => new Assert\Optional([
                new Assert\DateTime(['format' => 'Y-m-d\TH:i:sO'])
            ]),
            'endDate' => new Assert\Optional([
                new Assert\DateTime(['format' => 'Y-m-d\TH:i:sO'])
            ]),
            'statusCode' => new Assert\Optional([
                new Assert\Type('integer')
            ])
        ]);
        $validator = Validation::createValidator();
        return $validator->validate($searchParams, $constraints);
    }
}
