<?php
namespace Record\Command;

use Core\Command\BaseCommand;
use Shirou\Interfaces\ICommand;
use Shirou\Console\ConsoleUtil;

/**
 * @CommandName(['export'])
 * @CommandDescription('Record export command.')
 */
class Export extends BaseCommand implements ICommand
{
    public function wakewordAction($name = '')
    {
      $count = 0;
      $config = $this->getDI()->get('config');

      $sql = "SELECT v.vs_id, vs.vs_id, v.v_file_path, vs.vs_text FROM fly_voice v INNER JOIN fly_voice_script vs on v.vs_id = vs.vs_id WHERE vs.vs_text LIKE '%$name%'";
      $rows = $this->getDI()->get('db')->fetchAll($sql);

      $zipFilePath = ROOT_PATH . '/app/storage/files/' . str_replace(' ', '', $name) . '.zip';
      $zip = new \ZipArchive();
      $zip->open($zipFilePath, \ZipArchive::CREATE);

      foreach ($rows as $row) {
        $filePath = ROOT_PATH . $config->default->voices->directory . $row['v_file_path'];

        preg_match('/[a-zA-Z]+\/[0-9]{1,2}\/(.*)/', $row['v_file_path'], $matches);
        $zip->addFile($filePath, $matches[1]);
        $count++;
      }

      $zip->close();
      echo 'Done ZIP '. $count .' files';
    }
}
