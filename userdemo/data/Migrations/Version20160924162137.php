<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * A migration class. It either upgrades the databases schema (moves it to new state)
 * or downgrades it to the previous state.
 */
class Version20160924162137 extends AbstractMigration
{
    /**
     * Returns the description of this migration.
     */
    public function getDescription()
    {
        $description = 'This is the initial migration which creates the user table.';
        return $description;
    }
    
    /**
     * Upgrades the schema to its newer state.
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // Create 'user' table
        $table = $schema->createTable('user');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);        
        $table->addColumn('email', 'string', ['notnull'=>true, 'length'=>128]);
        $table->addColumn('full_name', 'string', ['notnull'=>true, 'length'=>512]);
        $table->addColumn('password', 'string', ['notnull'=>true, 'length'=>256]);
        $table->addColumn('status', 'integer', ['notnull'=>true]);
        $table->addColumn('date_created', 'datetime', ['notnull'=>true]);
        $table->addColumn('pwd_reset_token', 'string', ['notnull'=>false, 'length'=>32]);
        $table->addColumn('pwd_reset_token_creation_date', 'datetime', ['notnull'=>false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['email'], 'email_idx');
        $table->addOption('engine' , 'InnoDB');
    }

    /**
     * Reverts the schema changes.
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('user');
    }
}
