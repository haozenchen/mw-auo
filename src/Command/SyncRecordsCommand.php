<?php
// src/Command/SyncRecordsCommand.php
namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\CommandInterface;
use App\Service\SyncService;
use App\Service\DeleteLog;

class SyncRecordsCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $io->out('刪除過期log中...');
        $deleteLog = new DeleteLog();
        $deleteLog->do_del();
        $io->out('刪除完成。');

        // $io->out('同步資料中...');
        // $syncService = new SyncService();
        // $data = [
        //     'user'=>true,
        //     'user2'=>true,
        //     'edu'=>true,
        //     'exp'=>true,
        //     'dep'=>true,
        //     'appover'=>true,
        // ];
        // $syncService->doSync($data, false);
        // $io->out('同步完成。');
        return CommandInterface::CODE_SUCCESS;
    }
}
?>