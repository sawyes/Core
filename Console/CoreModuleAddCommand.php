<?php

namespace Modules\Core\Console;

use Faker\Provider\File;
use Illuminate\Console\Command;
use PhpParser\Node\Expr\AssignOp\Mod;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CoreModuleAddCommand extends Command
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'core:module:add';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $module = strtolower($this->argument('module'));

        $this->info(vsprintf("Finding [%s] config in core module...", [$module]));

        // $module = \Module::find('core');
        // $module = app('modules')->findOrFail('core');

        $coreModulesConfig = config('core.modules.path');

        if (! key_exists($module, $coreModulesConfig)) {
            $this->error(vsprintf("Missing module [%s] config in core module, make sure!", [$module]));

            return false;
        }

        $gitModulePath = $coreModulesConfig[$module];

        if (! is_dir($this->getProjectPath($gitModulePath))) {

            $gitShell = vsprintf("git submodule add -f %s %s", [
                $gitModulePath,
                $this->getProjectPath($gitModulePath)
            ]);

            $this->info("Shell Executing:\r\n");
            $this->info($gitShell);
            $ret = shell_exec($gitShell);

            $this->info(vsprintf("Module %s add sucessful!\r\n", [$this->getProjectPath($gitModulePath)]));

        } elseif($this->isEmptyDir($this->getProjectPath($gitModulePath))) {

            $gitShell = vsprintf("rm -fr %s && git clone --recursive %s %s", [
                $this->getProjectPath($gitModulePath),
                $gitModulePath,
                $this->getProjectPath($gitModulePath),
            ]);

            $this->info("Shell Executing:\r\n");
            $this->info($gitShell);
            $ret = shell_exec($gitShell);
            $this->info(vsprintf("Module %s add sucessful!\r\n", [$this->getProjectPath($gitModulePath)]));

        } else {
            $this->warn("Modules is already exists and is not empty !");
        }

    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isEmptyDir($path)
    {
        if (! is_dir($path)) {
            return true;
        }

        if ($handle = @opendir($path)) {

            //读取文件夹里的文件
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && 0 !== strpos($file, '.')) {
                    return false;
                }

            }
            closedir($handle);//关闭文件夹
            return true;
        }

        return false;
    }

    /**
     * 返回项目下载路径
     *
     * @param $gitModulePath
     *
     * @return string
     */
    public function getProjectPath($gitModulePath)
    {
        $project = explode('.', basename($gitModulePath))[0];

        return vsprintf("Modules/%s", [$project]);
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['module', InputArgument::REQUIRED, '模块名称'],
        ];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [// ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
