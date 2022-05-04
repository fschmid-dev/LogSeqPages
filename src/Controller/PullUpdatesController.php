<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PullUpdatesController extends AbstractController
{
    public function __construct(private string $projectDir)
    {
    }

    #[Route('/_pull', name: 'app_pull_updates', priority: 20)]
    public function index(Request $request): Response
    {
        $pullToken = $this->getParameter('app.pull_token');

        $requestToken = $request->query->get('token');
        if (!$requestToken || $pullToken !== $requestToken) {
            return new Response('Access denied!', 403);
        }

        $output = null;
        $return = null;

        // $result = exec('cd ' . $this->projectDir  'git pull origin main', $output, $return);
        $result = exec('cd ' . $this->projectDir . '/logseq && git pull origin main', $output, $return);

        dd($result, $output, $return);
    }
}
