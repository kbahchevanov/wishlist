<?php

namespace App\Controller;

use App\Entity\WishListItem;
use App\Repository\WishListItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishlistController extends AbstractController
{
    /**
     * @Route("/wishlist", name="wishlist")
     */
    public function index(WishListItemRepository $wishListItemRepository): Response
    {
        $items = $wishListItemRepository->findAll();

        return $this->render('wishlist/index.html.twig', [
            'items' => $items,
        ]);
    }

    /**
     * @Route("/xhr/wishlist_add", methods={"POST"}, name="wishlist_add")
     */
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        WishListItemRepository $wishListItemRepository
    ): Response
    {
        if (!$request->request->has('project') || !$request->request->has('platform') || null === $this->getUser()) {
            return new JsonResponse([]);
        }

        $project    = $request->request->get('project');
        $platform   = $request->request->get('platform');

        $wishListItem = $wishListItemRepository->findOneBy([
            'project'   => $project,
            'platform'  => $platform,
            'user'      => $this->getUser()
        ]);

        if (null !== $wishListItem) {
            return new JsonResponse(['error' => 'You have already added this item to your wish list!']);
        }

        $wishListItem = new WishListItem();
        $wishListItem->setProject($project);
        $wishListItem->setPlatform($platform);
        $wishListItem->setUser($this->getUser());

        $entityManager->persist($wishListItem);
        $entityManager->flush();

        return new JsonResponse([
            'item' => $wishListItem
        ]);
    }

    /**
     * @Route("/xhr/wishlist_remove", methods={"POST"}, name="wishlist_remove")
     */
    public function remove(
        Request $request,
        EntityManagerInterface $entityManager,
        WishListItemRepository $wishListItemRepository
    ): Response
    {
        if (!$request->request->has('id') || null === $this->getUser()) {
            return new JsonResponse([]);
        }

        $wishListItem = $wishListItemRepository->find($request->request->get('id'));

        if (null !== $wishListItem){
            $entityManager->remove($wishListItem);
            $entityManager->flush();
        }

        return new JsonResponse([
            'item' => $wishListItem
        ]);
    }
}
