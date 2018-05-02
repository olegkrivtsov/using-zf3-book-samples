<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * A migration class. It either upgrades the databases schema (moves it to new state)
 * or downgrades it to the previous state.
 */
class Version20160901114938 extends AbstractMigration
{
    /**
     * Returns the description of this migration.
     */
    public function getDescription()
    {
        $description = 'This migration adds indexes and foreign key constraints.';
        return $description;
    }
    
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // Add index to post table
        $table = $schema->getTable('post');
        $table->addIndex(['date_created'], 'post_date_created_index');
        
        // Add index and foreign key to comment table
        $table = $schema->getTable('comment');
        $table->addIndex(['post_id'], 'comment_post_id_index');
        $table->addForeignKeyConstraint('post', ['post_id'], ['id'], [], 'comment_post_id_fk');
        
        // Add indexes and foreign keys to post_tag table
        $table = $schema->getTable('post_tag');
        $table->addIndex(['post_id'], 'post_tag_post_id_index');
        $table->addIndex(['tag_id'], 'post_tag_id_index');
        $table->addForeignKeyConstraint('post', ['post_id'], ['id'], [], 'post_tag_post_id_fk');
        $table->addForeignKeyConstraint('tag', ['tag_id'], ['id'], [], 'post_tag_tag_id_fk');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $table = $schema->getTable('post_tag');
        $table->removeForeignKey('post_tag_post_id_fk');
        $table->removeForeignKey('post_tag_tag_id_fk');
        $table->dropIndex('post_tag_post_id_index');
        $table->dropIndex('post_tag_tag_id_index'); 
        
        $table = $schema->getTable('comment');
        $table->dropIndex('comment_post_id_index'); 
        $table->removeForeignKey('comment_post_id_fk');
        
        $table = $schema->getTable('post');
        $table->dropIndex('post_date_created_index');       
    }
}
