<?php

namespace App\Controller;

use League\CommonMark\GithubFlavoredMarkdownConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ReadController extends AbstractController
{
    public function __construct(private string $projectDir)
    {}

    #[Route('/{file}', name: 'read', requirements: ['file' => '.+'])]
    public function index(?string $file): Response
    {
        $fileName = $this->getFileName($file);

        $content = file_get_contents($fileName);
        if (!str_contains($content, 'public:: true')) {
            throw new NotFoundHttpException();
        }

        return $this->render('read/index.html.twig', [
            'file' => $file,
            'content' => $content,
        ]);
    }

    private function getFileName(string $file): string
    {
        if (!str_ends_with($file, '.md')) {
            $file .= '.md';
        }

        $file = str_replace('/', '.', $file);

        $fileLowerCase = strtolower($file);
        $fileList = scandir($this->projectDir . '/logseq/pages');
        foreach ($fileList as $pageFile) {
            if (strtolower($pageFile) === $fileLowerCase) {
                $file = $pageFile;
            }
        }

        return $this->projectDir . '/logseq/pages/' . $file;
    }
}
