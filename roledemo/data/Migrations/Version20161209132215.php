<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * A migration class. It either upgrades the databases schema (moves it to new state)
 * or downgrades it to the previous state.
 */
class Version20161209132215 extends AbstractMigration
{
    /**
     * Returns the description of this migration.
     */
    public function getDescription()
    {
        $description = 'A migration which creates the `role` and `permission` tables.';
        return $description;
    }
    
    /**
     * Upgrades the schema to its newer state.
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // Create 'role' table
        $table = $schema->createTable('role');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);        
        $table->addColumn('name', 'string', ['notnull'=>true, 'length'=>128]);
        $table->addColumn('description', 'string', ['notnull'=>true, 'length'=>1024]);
        $table->addColumn('date_created', 'datetime', ['notnull'=>true]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['name'], 'name_idx');
        $table->addOption('engine' , 'InnoDB');
        
        // Create 'role_hierarchy' table (contains parent-child relationships between roles)
        $table = $schema->createTable('role_hierarchy');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);        
        $table->addColumn('parent_role_id', 'integer', ['notnull'=>true]);
        $table->addColumn('child_role_id', 'integer', ['notnull'=>true]);
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('role', ['parent_role_id'], ['id'], 
                ['onDelete'=>'CASCADE', 'onUpdate'=>'CASCADE'], 'role_role_parent_role_id_fk');
        $table->addForeignKeyConstraint('role', ['child_role_id'], ['id'], 
                ['onDelete'=>'CASCADE', 'onUpdate'=>'CASCADE'], 'role_role_child_role_id_fk');
        $table->addOption('engine' , 'InnoDB');
        
        // Create 'permission' table
        $table = $schema->createTable('permission');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);        
        $table->addColumn('name', 'string', ['notnull'=>true, 'length'=>128]);
        $table->addColumn('description', 'string', ['notnull'=>true, 'length'=>1024]);
        $table->addColumn('date_created', 'datetime', ['notnull'=>true]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['name'], 'name_idx');
        $table->addOption('engine' , 'InnoDB');
        
        // Create 'role_permission' table
        $table = $schema->createTable('role_permission');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);        
        $table->addColumn('role_id', 'integer', ['notnull'=>true]);
        $table->addColumn('permission_id', 'integer', ['notnull'=>true]);
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('role', ['role_id'], ['id'], 
                ['onDelete'=>'CASCADE', 'onUpdate'=>'CASCADE'], 'role_permission_role_id_fk');
        $table->addForeignKeyConstraint('permission', ['permission_id'], ['id'], 
                ['onDelete'=>'CASCADE', 'onUpdate'=>'CASCADE'], 'role_permission_permission_id_fk');
        $table->addOption('engine' , 'InnoDB');
        
        // Create 'user_role' table
        $table = $schema->createTable('user_role');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);        
        $table->addColumn('user_id', 'integer', ['notnull'=>true]);
        $table->addColumn('role_id', 'integer', ['notnull'=>true]);
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('user', ['user_id'], ['id'], 
                ['onDelete'=>'CASCADE', 'onUpdate'=>'CASCADE'], 'user_role_user_id_fk');
        $table->addForeignKeyConstraint('role', ['role_id'], ['id'], 
                ['onDelete'=>'CASCADE', 'onUpdate'=>'CASCADE'], 'user_role_role_id_fk');
        $table->addOption('engine' , 'InnoDB');        
    }

    /**
     * Reverts the schema changes.
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('user_role');
        $schema->dropTable('role_permission');
        $schema->dropTable('permission');
        $schema->dropTable('role');
    }
}

