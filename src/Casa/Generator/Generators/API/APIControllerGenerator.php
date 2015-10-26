<?php

namespace Casa\Generator\Generators\API;

use Config;
use Casa\Generator\CommandData;
use Casa\Generator\Generators\GeneratorProvider;
use Casa\Generator\Utils\GeneratorUtils;

class APIControllerGenerator implements GeneratorProvider
{
    /** @var  CommandData */
    private $commandData;

    /** @var string  */
    private $path;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = Config::get('generator.path_api_controller', app_path('Http/Controllers/API/'));
    }

    public function generate()
    {
        $templateData = $this->commandData->templatesHelper->getTemplate('Controller', 'api');

        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);

        $fileName = $this->commandData->modelName.'APIController.php';

        if (!file_exists($this->path)) {
            mkdir($this->path, 0755, true);
        }

        $path = $this->path.$fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->comment("\nAPI Controller created: ");
        $this->commandData->commandObj->info($fileName);
    }
}
