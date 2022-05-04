<?php

namespace App\Twig;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\MarkdownConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RenderLogSeqPageExtension extends AbstractExtension
{
    private CommonMarkConverter $markdownConverter;

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
        $this->markdownConverter = new CommonMarkConverter();
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('renderLogSeqPage', [$this, 'renderLogSeqPage']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('renderLogSeqPage', [$this, 'renderLogSeqPage']),
        ];
    }

    public function renderLogSeqPage($content): string
    {
        $content = preg_replace_callback(
            '/\[\[(.*?)]]/',
            function (array $matches) {
                $file = $matches[1];

                return sprintf(
                    '<a href="%s">%s</a>',
                    $this->urlGenerator->generate('read', ['file' => $file]),
                    $file
                );
            },
            $content
        );

        $content = preg_replace(
            '/^[^-].*/',
            '',
            $content
        );

        $content = str_replace(
            '<img src="../assets',
            '<img src="/logseq_assets',
            $content
        );

        $content = preg_replace_callback(
            '/<img\s[^>]*?src\s*=\s*[\"]([^\"]*?)[\"][^>]*?>({.*})/',
            static function (array $matches) {
                $sizes = $matches[2];
                $sizes = str_replace(
                    ['{', '}', ':', ', ', ' '],
                    ['', '', '', 'px;', ': '],
                    $sizes
                );
                $sizes .= 'px';

                $img = $matches[0];
                $img = str_replace(
                    ['<img ', $matches[2]],
                    [sprintf('<img %s ', 'style="' . $sizes . '" '), ''],
                    $img
                );

                return $img;
            },
            $content
        );

        return $content;
    }
}
