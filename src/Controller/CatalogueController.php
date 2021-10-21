<?php

namespace App\Controller;

use App\Repository\API\PlatformRepository;
use App\Repository\API\SearchRepository;
use App\Repository\WishListItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatalogueController extends AbstractController
{
    public const DEFAULT_SEARCH_LANGUAGE = 'php';
    public const DEFAULT_SEARCH_SORT     = 'latest_release_published_at';

    /**
     * @var array
     */
    private $wishListItems;

    /**
     * @var array
     */
    private $criteria;

    /**
     * @Route("/catalogue", name="catalogue")
     */
    public function index(
        SearchRepository $searchRepository,
        PlatformRepository $platformRepository,
        WishListItemRepository $wishListItemRepository
    ): Response
    {
        $this->setBaseVars($wishListItemRepository);

        $projects = \json_decode(
            $searchRepository->findBy($this->criteria, self::DEFAULT_SEARCH_SORT)->getContent()
        );

        $platforms = \json_decode(
            $platformRepository->findAll()->getContent()
        );

        return $this->render('catalogue/index.html.twig', [
            'platforms'     => $platforms,
            'projects'      => $projects,
            'userWishItems' => $this->wishListItems
        ]);
    }

    /**
     * @Route("/xhr/catalogue_search", methods={"POST"} ,name="catalogue_search")
     */
    public function search(
        Request $request,
        SearchRepository $searchRepository,
        WishListItemRepository $wishListItemRepository
    ): Response
    {
        if (null == $this->getUser()) {
            return new JsonResponse([]);
        }

        $this->setBaseVars($wishListItemRepository);

        $request = $request->request;

        if ($request->has('search')) {
            $this->criteria['q'] = $request->get('search');
        }

        if ($request->has('licenses')) {
            $this->criteria['licenses'] = \implode(',', $request->get('licenses'));
        }

        if ($request->has('platforms')) {
            $this->criteria['platforms'] = \implode(',', $request->get('platforms'));
        }

        if ($request->has('page')) {
            $this->criteria['page'] = $page = $request->get('page');
        }

        $projects = \json_decode(
            $searchRepository->findBy($this->criteria, self::DEFAULT_SEARCH_SORT)->getContent()
        );

        return new JsonResponse([
            'html' => $this->renderView('catalogue/parts/results.html.twig', [
                'projects'      => $projects,
                'page'          => $page ?? null,
                'userWishItems' => $this->wishListItems
            ])
        ]);
    }

    private function setBaseVars(WishListItemRepository $wishListItemRepository): void
    {
        $this->criteria = ['languages' => self::DEFAULT_SEARCH_LANGUAGE];

        $this->wishListItems = \array_map(function($item) {
            return $item->getProject();
        }, $wishListItemRepository->findBy(['user' => $this->getUser()]));
    }
}
