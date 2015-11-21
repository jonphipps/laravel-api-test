<?php

namespace Casa\Generator\Generators\Common;

use Casa\Generator\Utils\DataBaseHelper;
use Casa\Generator\Utils\StringUtils;
use Config;
use DB;
use Casa\Generator\CommandData;
use Casa\Generator\Generators\GeneratorProvider;
use Casa\Generator\Utils\GeneratorUtils;
use Illuminate\Support\Str;

class ModelGenerator implements GeneratorProvider
{
    /** @var  CommandData */
    private $commandData;

    /** @var string */
    private $path;

    /** @var  string */
    private $prefix;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = Config::get('generator.path_model', app_path('Entities/'));
        $this->prefix = DB::getConfig('prefix');
    }

    public function generate()
    {
        $templateName = 'Model';

        $templateData = $this->commandData->templatesHelper->getTemplate($templateName, 'common');

        $templateData = $this->fillTemplate($templateData);

        $fileName = $this->commandData->modelName.'.php';

        if (!file_exists($this->path)) {
            mkdir($this->path, 0755, true);
        }

        $path = $this->path.$fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->comment("\nModel created: ");
        $this->commandData->commandObj->info($fileName);
    }

    private function fillTemplate($templateData)
    {
        if (!$this->commandData->useSoftDelete) {
            $templateData = str_replace('$SOFT_DELETE_IMPORT$', '', $templateData);
            $templateData = str_replace('$SOFT_DELETE$', '', $templateData);
            $templateData = str_replace('$SOFT_DELETE_DATES$', '', $templateData);
        }
        $templateData = str_replace('$RELATIONS$', $this->generateRelations(), $templateData);

        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);

        $fillables = [];

        foreach ($this->commandData->inputFields as $field) {
            $fillables[] = '"'.$field['fieldName'].'"';
        }

        $templateData = str_replace('$FIELDS$', implode(",\n        ", $fillables), $templateData);

        $templateData = str_replace('$RULES$', implode(",\n        ", $this->generateRules()), $templateData);

        $templateData = str_replace('$CAST$', implode(",\n        ", $this->generateCasts()), $templateData);


        $templateData = str_replace('$DISPLAY_ATTRIBUTE$', $this->getDisplayAttr(), $templateData);

        return $templateData;
    }

    private function getDisplayAttr()
    {
        return DataBaseHelper::getColumnFromTable($this->commandData->tableName);
    }

    private function generateRelations()
    {
        if ($this->commandData->tableName == '') return '';
        $code = '';

        //Get what tables it belongs to
        $relations = DataBaseHelper::getForeignKeysFromTable($this->commandData->tableName);
        foreach($relations as $r)
        {
            $referencedTableName = preg_replace("/$this->prefix/uis", '', $r->REFERENCED_TABLE_NAME);
            $referencedTableName = ucfirst(Str::camel(StringUtils::singularize($referencedTableName)));

            $code .= "    public function " . $referencedTableName ."() {\n";
            $code .= "        " . 'return $this->belongsTo(' .  "'\$NAMESPACE_MODEL\$\\" . $referencedTableName ."', '". $r->COLUMN_NAME . "'); \n";
            $code .= "    }\n\n";
        }

        //Get what tables it is referenced
        $relations = DataBaseHelper::getReferencesFromTable($this->commandData->tableName);
        foreach($relations as $r)
        {
            $tableName = preg_replace("/$this->prefix/uis", '', $r->TABLE_NAME);
            $tableName = ucfirst(Str::camel(StringUtils::singularize($tableName)));

            $code .= "    public function " . Str::plural($tableName) ."() {\n";
            $code .= "        " . 'return $this->hasMany(' .  "'\$NAMESPACE_MODEL\$\\" . $tableName ."', '". $r->COLUMN_NAME . "'); \n";
            $code .= "    }\n\n";
        }

        return $code;

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

    public function generateCasts()
    {
        $casts = [];

        foreach ($this->commandData->inputFields as $field) {
            switch ($field['fieldType']) {
                case 'integer':
                    $rule = '"'.$field['fieldName'].'" => "integer"';
                    break;
                case 'double':
                    $rule = '"'.$field['fieldName'].'" => "double"';
                    break;
                case 'float':
                    $rule = '"'.$field['fieldName'].'" => "float"';
                    break;
                case 'boolean':
                    $rule = '"'.$field['fieldName'].'" => "boolean"';
                    break;
                case 'string':
                case 'char':
                case 'text':
                    $rule = '"'.$field['fieldName'].'" => "string"';
                    break;
                default:
                    $rule = '';
                    break;
            }

            if (!empty($rule)) {
                $casts[] = $rule;
            }
        }

        return $casts;
    }
}
