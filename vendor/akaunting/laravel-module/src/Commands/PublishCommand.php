<?php

namespace Akaunting\Module\Commands;

use Illuminate\Console\Command;
use Akaunting\Module\Module;
use Akaunting\Module\Publishing\AssetPublisher;
use Symfony\Component\Console\Input\InputArgument;

class PublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish a module\'s assets to the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($alias = $this->argument('alias')) {
            $this->publish($alias);

            return;
        }

        $this->publishAll();
    }

    /**
     * Publish assets from all modules.
     */
    public function publishAll()
    {
        foreach ($this->laravel['module']->allEnabled() as $module) {
            $this->publish($module);
        }
    }

    /**
     * Publish assets from the specified module.
     *
     * @param string $alias
     */
    public function publish($alias)
    {
        if ($alias instanceof Module) {
            $module = $alias;
        } else {
            $module = $this->laravel['module']->findOrFail($alias);
        }

        with(new AssetPublisher($module))
            ->setRepository($this->laravel['module'])
            ->setConsole($this)
            ->publish();

        $this->line("<info>Published</info>: {$alias}");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['alias', InputArgument::OPTIONAL, 'The alias of module will be used.'],
        ];
    }
}
