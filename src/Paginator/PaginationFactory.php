<?php

namespace App\Paginator;

use Doctrine\ORM\Query;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class PaginationFactory
{
    public function __construct(protected RouterInterface $router)
    {
    }

    public function createPagination(Query $queryBuilder, Request $request): Pagerfanta
    {
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);

        $currentPage = $request->query->getInt('page', 1);
//        $pagerfanta->setMaxPerPage(); // todo: set max page from user settings
        $pagerfanta->setCurrentPage($currentPage);
        $pagerfanta->jsonSerialize();

        return $pagerfanta;
    }

}