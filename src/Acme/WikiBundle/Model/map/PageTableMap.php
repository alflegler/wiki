<?php

namespace Acme\WikiBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'page' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.Acme.WikiBundle.Model.map
 */
class PageTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Acme.WikiBundle.Model.map.PageTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('page');
        $this->setPhpName('Page');
        $this->setClassname('Acme\\WikiBundle\\Model\\Page');
        $this->setPackage('src.Acme.WikiBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('parent_id', 'ParentId', 'INTEGER', 'page', 'id', false, null, null);
        $this->addColumn('parentPath', 'Parentpath', 'VARCHAR', false, 255, null);
        $this->getColumn('parentPath', false)->setPrimaryString(true);
        $this->addColumn('pageName', 'Pagename', 'VARCHAR', false, 255, null);
        $this->getColumn('pageName', false)->setPrimaryString(true);
        $this->addColumn('title', 'Title', 'VARCHAR', false, 255, null);
        $this->addColumn('text', 'Text', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Parent', 'Acme\\WikiBundle\\Model\\Page', RelationMap::MANY_TO_ONE, array('parent_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Children_ref', 'Acme\\WikiBundle\\Model\\Page', RelationMap::ONE_TO_MANY, array('id' => 'parent_id', ), 'CASCADE', null, 'Children_refs');
    } // buildRelations()

} // PageTableMap
