<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\BuildingRepository;
use App\Repository\OrganizationRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class OrganizationController extends AbstractController
{
    public function __construct(
        protected readonly OrganizationRepository $organizationRepository,
        protected readonly ActivityRepository     $activityRepository,
        protected readonly BuildingRepository     $buildingRepository,
        protected readonly SerializerInterface    $serializer
    ) { }

    #[Route(
        path: '/api/organization/building/{buildingId}',
        methods: ['GET']
    )]
    #[OA\Get(
        path: "/api/organization/building/{buildingId}",
        description: "Возвращает список организаций, находящихся в заданном здании.",
        summary: "Список организаций в здании",
        parameters: [
            new OA\Parameter(
                name: "buildingId",
                description: "ID здания",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список организаций",
                content: new OA\JsonContent(
                    example: [
                        [
                            "id" => 1,
                            "name" => "ООО Рога и Копыта",
                            "phoneNumbers" => ["+7-923-111-11-11"]
                        ],
                        [
                            "id" => 2,
                            "name" => "ЗАО Молочные продукты",
                            "phoneNumbers" => ["+7-923-333-33-33"]
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Здание не найдено",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Building not found")
                    ],
                    type: "object"
                )
            )
        ]
    )]
    #[OA\Tag(name: "Organizations")]
    public function organizationsByBuilding(int $buildingId): JsonResponse
    {
        $organizations = $this->organizationRepository->findByBuilding($buildingId);

        return $this->json($organizations);
    }

    #[Route('/api/organization/activity/{activityId}', methods: ['GET'])]
    #[OA\Get(
        path: "/api/organization/activity/{activityId}",
        description: "Возвращает список организаций, относящихся к указанному виду деятельности.",
        summary: "Список организаций по виду деятельности",
        parameters: [
            new OA\Parameter(
                name: "activityId",
                description: "ID вида деятельности",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список организаций",
                content: new OA\JsonContent(
                    example: [
                        [
                            "id" => 1,
                            "name" => "ООО Рога и Копыта",
                            "phoneNumbers" => ["+7-923-111-11-11"],
                        ],
                        [
                            "id" => 2,
                            "name" => "ЗАО Молочные продукты",
                            "phoneNumbers" => ["+7-923-333-33-33"],
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Вид деятельности не найден",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Activity not found")
                    ],
                    type: "object"
                )
            )
        ]
    )]
    #[OA\Tag(name: "Organizations")]
    public function organizationsByActivity(int $activityId): JsonResponse
    {
        $organizations = $this->organizationRepository->findByActivity($activityId, $this->activityRepository);

        return $this->json($organizations);
    }

    #[Route('/api/organization/radius', methods: ['GET'])]
    #[OA\Get(
        path: "/api/organization/radius",
        description: "Возвращает список организаций, находящихся в заданном радиусе от точки на карте.",
        summary: "Список организаций в радиусе",
        parameters: [
            new OA\Parameter(
                name: "latitude",
                description: "Широта центра радиуса",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "number", format: "float")
            ),
            new OA\Parameter(
                name: "longitude",
                description: "Долгота центра радиуса",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "number", format: "float")
            ),
            new OA\Parameter(
                name: "radius",
                description: "Радиус поиска",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "number", format: "float")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список организаций",
                content: new OA\JsonContent(
                    example: [
                        [
                            "id" => 1,
                            "name" => "ООО Рога и Копыта",
                            "phoneNumbers" => ["+7-923-111-11-11"],
                            "buildingId" => 2,
                            "buildingAddress" => "г. Москва, ул. Пушкина, д. 2",
                        ],
                        [
                            "id" => 2,
                            "name" => "ЗАО Молочные продукты",
                            "phoneNumbers" => ["+7-923-333-33-33"],
                            "buildingId" => 3,
                            "buildingAddress" => "г. Москва, ул. Варягина, д. 66",
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Ошибка запроса",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Invalid radius or coordinates")
                    ],
                    type: "object"
                )
            )
        ]
    )]
    #[OA\Tag(name: "Organizations")]
    public function organizationsInRadius(Request $request): JsonResponse
    {
        $latitude = (float)$request->query->get('latitude');
        $longitude = (float)$request->query->get('longitude');
        $radius = (float)$request->query->get('radius');

        if (!$latitude || !$longitude || !$radius) {
            return $this->json(['error' => 'Invalid radius or coordinates'], 400);
        }

        $organizations = $this->organizationRepository->findByGeoArea($latitude, $longitude, $radius);

        return $this->json($organizations);
    }


    #[Route('/api/organization/{id<\d+>}', methods: ['GET'])]
    #[OA\Get(
        path: "/api/organization/{id}",
        description: "Возвращает информацию об организации по её идентификатору.",
        summary: "Информация об организации",
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID организации",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Информация об организации",
                content: new OA\JsonContent(
                    example: [
                        "id" => 1,
                        "name" => "ООО Рога и Копыта",
                        "phoneNumbers" => [
                            "+7-923-111-11-11",
                            "+7-923-222-22-22"
                        ],
                        "building" => [
                            "id" => 1,
                            "address" => "г. Москва, ул. Ленина, 1",
                            "latitude" => 55.7558,
                            "longitude" => 37.6173
                        ],
                        "activity" => [
                            [
                                "id" => 2,
                                "name" => "Мясная продукция",
                                "children" => []
                            ]
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Организация не найдена",
                content: new OA\JsonContent(
                    example: [
                        "error" => "Organization not found"
                    ]
                )
            )
        ]
    )]
    #[OA\Tag(name: "Organizations")]
    public function organizationById(int $id): JsonResponse
    {
        $organization = $this->organizationRepository->find($id);

        if (!$organization) {
            return $this->json(['error' => 'Organization not found'], 404);
        }

        return $this->json($organization);
    }

    #[Route('/api/organization/activity-tree/{activityId}', methods: ['GET'])]
    #[OA\Get(
        path: "/api/organization/activity-tree/{activityId}",
        description: "Возвращает список организаций, связанных с указанным видом деятельности, включая вложенные виды.",
        summary: "Список организаций по дереву деятельности",
        parameters: [
            new OA\Parameter(
                name: "activityId",
                description: "ID вида деятельности",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список организаций по дереву деятельности",
                content: new OA\JsonContent(
                    example: [
                        [
                            "id" => 3,
                            "name" => "ИП Продукты питания",
                            "phoneNumbers" => [
                                "+7-923-444-44-44"
                            ],
                            "activityId" => 1,
                            "activityName" => "Еда"
                        ],
                        [
                            "id" => 1,
                            "name" => "ООО Рога и Копыта",
                            "phoneNumbers" => [
                                "+7-923-111-11-11",
                                "+7-923-222-22-22"
                            ],
                            "activityId" => 2,
                            "activityName" => "Мясная продукция"
                        ],
                        [
                            "id" => 2,
                            "name" => "ЗАО Молочные продукты",
                            "phoneNumbers" => [
                                "+7-923-333-33-33"
                            ],
                            "activityId" => 3,
                            "activityName" => "Молочная продукция"
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Вид деятельности не найден",
                content: new OA\JsonContent(
                    example: [
                        "error" => "Activity not found"
                    ]
                )
            )
        ]
    )]
    #[OA\Tag(name: "Organizations")]
    public function organizationsByActivityTree(int $activityId): JsonResponse
    {
        $organizations = $this->organizationRepository->findByActivityTree($activityId, $this->activityRepository);

        return $this->json($organizations);
    }

    #[Route('/api/organization/search', methods: ['GET'])]
    #[OA\Get(
        path: "/api/organization/search",
        description: "Возвращает список организаций по их имени.",
        summary: "Поиск организаций по имени",
        parameters: [
            new OA\Parameter(
                name: "name",
                description: "Часть или полное имя организации для поиска",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список найденных организаций",
                content: new OA\JsonContent(
                    example: [
                        [
                            "id" => 1,
                            "name" => "ООО Рога и Копыта",
                            "phoneNumbers" => [
                                "+7-923-111-11-11",
                                "+7-923-222-22-22"
                            ],
                            "building" => [
                                "id" => 1,
                                "address" => "г. Москва, ул. Ленина, 1",
                                "latitude" => 55.7558,
                                "longitude" => 37.6173
                            ],
                            "activity" => [
                                [
                                    "id" => 2,
                                    "name" => "Мясная продукция",
                                    "children" => []
                                ]
                            ]
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Организации не найдены",
                content: new OA\JsonContent(
                    example: [
                        "error" => "Organizations not found"
                    ]
                )
            )
        ]
    )]
    #[OA\Tag(name: "Organizations")]
    public function organizationsByName(Request $request): JsonResponse
    {
        $name = $request->query->get('name');

        $organizations = $this->organizationRepository->findByName($name);

        return $this->json($organizations);
    }
}
