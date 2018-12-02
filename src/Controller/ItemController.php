<?php

namespace App\Controller;

use App\Entity\Item;
use App\Event\ItemEvent;
use App\Event\ItemEvents;
use App\Form\Type\ItemFormType;
use App\Service\QueryHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends AbstractController
{
    /**
     * @Route("/items", name="items", options={"expose"=true})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('item/index.html.twig');
    }

    /**
     * @Route("/items/ajax", name="items_ajax", options={"expose"=true})
     *
     * @param QueryHelper $queryHelper
     * @param LoggerInterface $logger
     *
     * @return JsonResponse
     */
    public function getItemsAjax(QueryHelper $queryHelper, LoggerInterface $logger)
    {
        $status = Response::HTTP_OK;
        $result = array();
        $user = $this->getUser();

        try {
            $items = $queryHelper->getItemsForUser($user);

            $result = array(
                'total' => count($items),
                'items' => $items,
            );
        } catch (\Exception $e) {
            $status = Response::HTTP_BAD_REQUEST;
            $logger->log(LogLevel::ERROR, $e->getMessage());
        }


        return new JsonResponse($result, $status);
    }


    /**
     * @Route("/item/add", name="item_add", options={"expose"=true})
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     *
     * @param EventDispatcherInterface $dispatcher
     * @return null|JsonResponse|Response
     */
    public function addItemAjaxAction(
        Request $request,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher
    ) {
        $user = $this->getUser();
        $form = $this->createForm(ItemFormType::class);
        $result = null;

        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var Item $item */
                $item = $form->getData();

                try {
                    $item
                        ->setCreated(new \DateTime())
                        ->setUserId($user);

                    $entityManager->persist($item);
                    $entityManager->flush();

                    $result = array('success' => true);

                    $dispatcher->dispatch(ItemEvents::NEW_ITEM, new ItemEvent($item));
                } catch (\Exception $exception) {
                    $logger->log(LogLevel::CRITICAL, (string)$exception);
                    $result = array('success' => false);
                }
            }
        }

        return
            $result
                ? $this->json($result)
                : $this->render(
                'item/add_item.html.twig',
                array(
                    'form' => $form->createView(),
                )
            );
    }
}