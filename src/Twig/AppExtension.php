<?php 

namespace App\Twig;

use App\Services\GlobalVariableService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private GlobalVariableService $globalVariableService
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('current_instance', [$this, 'currentInstance']),
        ];
    }


    public function currentInstance(): string
    {

        $current_instance = $this->globalVariableService->get('current_instance')->getName();

        return $current_instance;
    }
}