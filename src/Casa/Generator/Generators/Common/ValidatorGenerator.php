<?php

namespace Casa\Generator\Generators\Common;

use Config;
use Casa\Generator\CommandData;
use Casa\Generator\Generators\GeneratorProvider;
use Casa\Generator\Utils\GeneratorUtils;

class ValidatorGenerator implements GeneratorProvider
{
    /** @var  CommandData */
    private $commandData;

    /** @var string */
    private $path;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = Config::get('generator.path_validator', app_path('Validators/'));
    }

    public function generate()
    {
        $templateData = $this->commandData->templatesHelper->getTemplate('Validator', 'common');

        $templateData = str_replace('$RULES$', implode(",\n\t\t", $this->generateRules()), $templateData);

        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);

        $fileName = $this->commandData->modelName.'Validator.php';

        if (!file_exists($this->path)) {
            mkdir($this->path, 0755, true);
        }

        $path = $this->path.$fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->comment("\nValidato created: ");
        $this->commandData->commandObj->info($fileName);
    }

    private function generateRules()
    {
        $rules = [];

        foreach ($this->commandData->inputFields as $field) {
            if (!empty($field['validations'])) {
                $rule = '"'.$field['fieldName'].'" => "'.$field['validations'].'"';
                $rules[] = $rule;
            }
        }

        return $rules;
    }

}
