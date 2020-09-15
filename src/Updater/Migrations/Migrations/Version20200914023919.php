<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

final class Version20200914023919 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->createSinglePage('/dashboard/system/automation', 'Automation');
        $this->createSinglePage('/dashboard/system/automation/commands', 'Commands', ['meta_keywords' => 'automated jobs, commands, console, cli']);
        $this->createSinglePage('/dashboard/system/automation/queues', 'Queues');
        $this->createSinglePage('/dashboard/system/automation/settings', 'Automation Settings');
    }
}
