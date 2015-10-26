<?php

namespace Casa\Generator\Commands;

use Casa\Generator\CommandData;
use Casa\Generator\Generators\Common\MigrationGenerator;
use Casa\Generator\Generators\Common\ModelGenerator;
use Casa\Generator\Generators\Common\RepositoryGenerator;
use Casa\Generator\Generators\Common\RequestGenerator;
use Casa\Generator\Generators\Common\RoutesGenerator;
use Casa\Generator\Generators\Scaffold\ViewControllerGenerator;
use Casa\Generator\Generators\Scaffold\ViewGenerator;
use Casa\Generator\Generators\Common\InterfaceGenerator;
use Casa\Generator\Generators\Common\ServiceGenerator;
use Casa\Generator\Generators\Common\ValidatorGenerator;

class ScaffoldGeneratorCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'casa.generator:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a full CRUD for given model with initial views';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_SCAFFOLD);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        if (!$this->commandData->skipMigration and !$this->commandData->fromTable) {
            $migrationGenerator = new MigrationGenerator($this->commandData);
            $migrationGenerator->generate();
        }

        $modelGenerator = new ModelGenerator($this->commandData);
        $modelGenerator->generate();

        $requestGenerator = new RequestGenerator($this->commandData);
        $requestGenerator->generate();

        $interfaceGenerator = new InterfaceGenerator($this->commandData);
        $interfaceGenerator->generate();

        $repositoryGenerator = new RepositoryGenerator($this->commandData);
        $repositoryGenerator->generate();

        $serviceGenerator = new ServiceGenerator($this->commandData);
        $serviceGenerator->generate();

        $validatorGenerator = new ValidatorGenerator($this->commandData);
        $validatorGenerator->generate();

        $repoControllerGenerator = new ViewControllerGenerator($this->commandData);
        $repoControllerGenerator->generate();

        $viewsGenerator = new ViewGenerator($this->commandData);
        $viewsGenerator->generate();

        $routeGenerator = new RoutesGenerator($this->commandData);
        $routeGenerator->generate();

        if ($this->confirm("\nDo you want to migrate database? [y|N]", false)) {
            $this->call('migrate');
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array_merge(parent::getArguments());
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return array_merge(parent::getOptions(), []);
    }
}
