<?php

/**
 * BaseVirtualinterfaceSeq
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: BaseVirtualinterfaceSeq.php 114 2010-03-15 12:49:13Z barryo $
 */
abstract class BaseVirtualinterfaceSeq extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('virtualinterface_seq');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}