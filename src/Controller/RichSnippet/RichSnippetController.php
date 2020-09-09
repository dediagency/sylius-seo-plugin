<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Controller\RichSnippet;

use Dedi\SyliusSEOPlugin\Context\RichSnippetContext;
use Dedi\SyliusSEOPlugin\Context\SubjectNotFoundException;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\RichSnippetFactoryChain;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\RichSnippetFactoryNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RichSnippetController extends AbstractController
{
    private RichSnippetFactoryChain $factoryChain;
    private RichSnippetContext $richSnippetContext;

    public function __construct(RichSnippetContext $richSnippetSubjectContext, RichSnippetFactoryChain $factoryChain)
    {
        $this->factoryChain = $factoryChain;
        $this->richSnippetContext = $richSnippetSubjectContext;
    }

    public function __invoke(string $richSnippetType, string $subjectType, ?int $id): JsonResponse
    {
        try {
            $subject = $this->richSnippetContext->getSubject($subjectType, $id);
            $factory = $this->factoryChain->getRichSnippetFactory($richSnippetType, $subject);
        } catch (RichSnippetFactoryNotFoundException | SubjectNotFoundException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }

        $richSnippet = $factory->buildRichSnippet($subject);

        return new JsonResponse($richSnippet->getData());
    }
}
