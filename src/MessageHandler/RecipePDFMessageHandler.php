<?php

namespace App\MessageHandler;

use App\Message\RecipePDFMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler]
final readonly class RecipePDFMessageHandler
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/pdfs')]
        private string $path,
        private readonly UrlGeneratorInterface $urlGenerator,
        #[Autowire('%app.gotenberg_endpoint%')]
        private readonly string $gotenbergEndpoint
    ) {
    }

    public function __invoke(RecipePDFMessage $message): void
    {
        $process = new Process([
            'curl',
            '--request',
            'POST',
            sprintf('%s/forms/chromium/convert/url', $this->gotenbergEndpoint),
            '--form',
            sprintf(
                "url=%s",
                'https://localhost:8001' . $this->urlGenerator->generate(
                    'recipe.show',
                    ['id' => $message->recipeId],
                    UrlGeneratorInterface::ABSOLUTE_PATH
                )
            ),
            '-o',
            sprintf("%s/%s", $this->path, sprintf('recipe-%d.pdf', $message->recipeId))
        ]);

        $process->run();

        dump($process->getOutput(), $process->getCommandLine());

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
