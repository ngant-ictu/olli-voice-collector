<?php
namespace Record\Command;

use Core\Command\BaseCommand;
use Shirou\Interfaces\ICommand;
use Shirou\Console\ConsoleUtil;

/**
 * @CommandName(['script'])
 * @CommandDescription('Script command.')
 */
class Script extends BaseCommand implements ICommand
{
    public function testAction()
    {

    }
}
