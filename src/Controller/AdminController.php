<?php

namespace App\Controller;

use App\Service\QueryHelper;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin", options={"expose"=true})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/users/ajax", name="users_ajax", options={"expose"=true})
     *
     * @param QueryHelper $queryHelper
     * @param LoggerInterface $logger
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUsersAjax(
        QueryHelper $queryHelper,
        LoggerInterface $logger
    ) {
        $status = Response::HTTP_OK;
        $result = array();

        try {
            $users = $queryHelper->getUsersForAdmin();

            $result = array(
                'total' => count($users),
                'items' => $users,
            );
        } catch (\Exception $e) {
            $status = Response::HTTP_BAD_REQUEST;
            $logger->log(LogLevel::ERROR, $e->getMessage());
        }

        return new JsonResponse($result, $status);
    }

    /**
     * @Route("/users/toggle_restriction/{enable}/{id}", name="toggle_user_restriction", options={"expose"=true}, requirements={"id": "\d+", "enable": "\d+"})
     *
     * @param int $enable
     * @param int $id
     * @param QueryHelper $queryHelper
     * @param LoggerInterface $logger
     * @return Response
     */
    public function disableUserAddItem(
        int $enable,
        int $id,
        QueryHelper $queryHelper,
        LoggerInterface $logger
    ) {
        try {
            $user = $queryHelper->toggleUserItemRestriction($id, $enable);

            $result = array(
                'action_done' => true,
                'message' => sprintf(
                    'Successfully %s Add Item permission for %s',
                    $enable ? 'enabled' : 'disabled',
                    $user->getEmail()
                )
            );
        } catch (\Exception $e) {
            $result = array(
                'action_failed' => true,
                'message' => sprintf('Failed to %s Add Item permission', $enable ? 'enable' : 'disable'),
            );
            $logger->log(LogLevel::ERROR, $e->getMessage());
        }

        return $this->render('admin/disable_user_result.html.twig', $result);
    }
}