<?php

namespace YCMS\Modules\Commands;

use Pingpong\Support\Stub;
use YCMS\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class ControllerCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument being used.
     *
     * @var string
     */
    protected $argumentName = 'controller';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new restful controller for the specified module.';

    /**
     * Get controller name.
     *
     * @return string
     */
    public function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $controllerPath = $this->laravel['modules']->config('paths.generator.controller');

        return $path.$controllerPath.'/'.$this->getControllerName().'.php';
    }

    /**
     * @return Stub
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub('/controller.stub', [
            'MODULENAME' => $module->getStudlyName(),
            'CONTROLLERNAME' => $this->getControllerName(),
            'CLASS' => $this->getClass(),
            'NAMESPACE' => $module->getLowername(),
            'MODULE_NAMESPACE' => $this->laravel['modules']->config('namespace'),
            'CLASS_NAMESPACE' => $this->getClassNamespace($module),
        ]))->render();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('controller', InputArgument::REQUIRED, 'The name of the controller class.'),
            array('module', InputArgument::OPTIONAL, 'The name of module will be used.'),
        );
    }

    /**
     * @return array|string
     */
    protected function getControllerName()
    {
        $controller = studly_case($this->argument('controller'));

        if (!str_contains(strtolower($controller), 'controller')) {
            $controller = $controller.'Controller';
        }

        return $controller;
    }

    /**
     * Get default namespace.
     *
     * @return string
     */
    public function getDefaultNamespace()
    {
        return 'Http\Controllers';
    }
}
