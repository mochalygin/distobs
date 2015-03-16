<?php

namespace DistObsNet\Console;

//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Platforms\SqlitePlatform;
//use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Schema\Synchronizer\SingleDatabaseSynchronizer;

class CreateDBCommand extends ContainerAwareCommand
{

    protected $db;

    protected $monolog;

    /**
     * @var \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    protected $platform;

    /**
     * @var \Doctrine\DBAL\Schema\Schema
     */
    protected $schema;

    protected function configure()
    {
        $this
            ->setName('db:install')
            ->setDescription('Installing database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->monolog()->addInfo('Starting creation of the DB...');

        //Генерим sql табличек
        $this->monolog()->addInfo('Generating SQL for tables...');
        try {
            $this->createTableNode();
            $this->createTableData();
            $this->createTableTag();
            $this->createTableDataTag();
            $this->createTableObserver();
            $this->createTablePublisher();
            $this->createTableSettings();

            $this->monolog()->addInfo('Ok');
        } catch(\Exception $e) {
            $this->monolog()->addError('Error (' . $e->getMessage() . ')');
            exit(1);
        }

        //выполняем sql табличек
        $this->monolog()->addInfo('Executing SQL for tables...');
        try {
            $sync = new SingleDatabaseSynchronizer($this->db());
            $sync->updateSchema($this->schema());

            $this->monolog()->addInfo('Ok');
        } catch(\Exception $e) {
            $this->monolog()->addError('Error (' . $e->getMessage() . ')');
            exit(1);
        }

        //записываем флаг готовности БД
        $container = $this->getContainer();
        try {
            $settings = $container['settings']->create();
            $settings->code = 'isDbCreated';
            $settings->value = true;
            $container['settings']->save($settings);

            $this->monolog()->addInfo('Ok');
        } catch (\Exception $e) {
            $this->monolog()->addError('Error (' . $e->getMessage() . ')');
        }

        $this->monolog()->addInfo('DB created succesfully');
    }

    protected function createTableNode()
    {
        $table = $this->createTable('node');

        $table->addColumn('id', 'integer');
        $table->addColumn('public_key', 'string', array('unique' => true, 'length' => 32));
        $table->addColumn('url', 'string', array('length' => 255));
        $table->addColumn('name', 'string', array('length' => 255));
        $table->addColumn('ts', 'datetime');

        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('public_key'));
    }

    protected function createTableData()
    {
        $table = $this->createTable('data');

        $table->addColumn('id', 'integer');
        $table->addColumn('node_id', 'integer');
        $table->addColumn('data', 'text', array('length' => 255));
        $table->addColumn('date', 'integer');

        $table->addColumn('hash', 'string', array('unique' => true, 'length' => 32));

        $table->addColumn('hands', 'smallint');
        $table->addColumn('status', 'boolean');
        $table->addColumn('ts', 'datetime');

        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('hash'));
    }

    protected function createTableSettings()
    {
        $table = $this->createTable('settings');

        $table->addColumn('code', 'string', array('length' => 255));
        $table->addColumn('value', 'string', array('length' => 255));

        $table->setPrimaryKey(array('code'));
    }

    protected function createTableTag()
    {
        $table = $this->createTable('tag');

        $table->addColumn('id', 'integer');
        $table->addColumn('name', 'string', array('length' => 255));

        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('name'));
    }

    protected function createTableDataTag()
    {
        $table = $this->createTable('data_tag');

        $table->addColumn('data_id', 'integer');
        $table->addColumn('tag_id', 'integer');

        $table->setPrimaryKey(array('data_id', 'tag_id'));
    }

    protected function createTableObserver()
    {
        $table = $this->createTable('observer');

        $table->addColumn('node_id', 'integer');
        $table->addColumn('ts', 'integer');

        $table->setPrimaryKey(array('node_id'));
    }

    protected function createTablePublisher()
    {
        $table = $this->createTable('publisher');

        $table->addColumn('node_id', 'integer');
        $table->addColumn('ts', 'integer');

        $table->setPrimaryKey(array('node_id'));
    }

    protected function createTable($tableName)
    {
        $this->monolog()->addInfo('Generating SQL for table ' . $tableName);
        return $this->schema()->createTable($tableName);
    }


    protected function db()
    {
        if (!$this->db) {
            $container = $this->getContainer();
            $this->db = $container['db'];
        }

        return $this->db;
    }

    protected function monolog()
    {
        if (!$this->monolog) {
            $container = $this->getContainer();
            $this->monolog = $container['monolog'];
        }

        return $this->monolog;
    }

    /**
     * @return \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    protected function platform()
    {
        if (!$this->platform) {
            $this->platform = new SqlitePlatform;
        }

        return $this->platform;
    }

    /**
     * @return \Doctrine\DBAL\Schema\Schema
     */
    protected function schema()
    {
        if (!$this->schema) {
            $this->schema = new Schema;
        }

        return $this->schema;
    }

}