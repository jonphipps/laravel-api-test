<?php

namespace Casa\Generator\Generators\Scaffold;

use Config;
use Casa\Generator\CommandData;
use Casa\Generator\Generators\GeneratorProvider;
use Casa\Generator\Utils\GeneratorUtils;

class ViewControllerGenerator implements GeneratorProvider
{
    /** @var  CommandData */
    private $commandData;

    /** @var string */
    private $path;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = Config::get('generator.path_controller', app_path('Http/Controllers/'));
    }

    public function generate()
    {
        $templateData = $this->commandData->templatesHelper->getTemplate('Controller', 'scaffold');

        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);

        if ($this->commandData->paginate) {
            $templateData = str_replace('$RENDER_TYPE$', 'paginate('.$this->commandData->paginate.')', $templateData);
        } else {
            $templateData = str_replace('$RENDER_TYPE$', 'all()', $templateData);
        }

        $fileName = $this->commandData->modelName.'Controller.php';

        $path = $this->path.$fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->comment("\nController created: ");
        $this->commandData->commandObj->info($fileName);
    }
}
