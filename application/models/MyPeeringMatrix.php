<?php

/*
 * Copyright (C) 2009-2011 Internet Neutral Exchange Association Limited.
 * All Rights Reserved.
 * 
 * This file is part of IXP Manager.
 * 
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 * 
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 * 
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 * 
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


/**
 * MyPeeringMatrix
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: MyPeeringMatrix.php 446 2011-05-26 13:40:19Z barryo $
 */
/**
 *
 * Auto-generated Doctrine ORM File
 *
 * @category ORM
 * @package IXP_ORM_Models
 * @copyright Copyright 2008 - 2010 Internet Neutral Exchange Association Limited <info (at) inex.ie>
 * @author Barry O'Donovan <barryo (at) inex.ie>
 */
class MyPeeringMatrix extends BaseMyPeeringMatrix
{

    const PEERED_STATE_NO      = 'NO';
    const PEERED_STATE_YES     = 'YES';
    const PEERED_STATE_WAITING = 'WAITING';
    const PEERED_STATE_NEVER   = 'NEVER';

    public static $PEERED_STATES = array(
        0 => MyPeeringMatrix::PEERED_STATE_NO,
        1 => MyPeeringMatrix::PEERED_STATE_YES,
        2 => MyPeeringMatrix::PEERED_STATE_WAITING,
        3 => MyPeeringMatrix::PEERED_STATE_NEVER
    );

    public function setUp()
    {
        parent::setUp();
        $this->actAs('SoftDelete');

        $this->hasOne( 'MyPeeringMatrixNotes',   array( 'local' => 'notes_id',  'foreign' => 'id' ) );
        // and for convenience:
        $this->hasOne( 'MyPeeringMatrixNotes as Notes',   array( 'local' => 'notes_id',  'foreign' => 'id' ) );
    }


    /**
     * Add or update notes for a peer.
     *
     * Note that each peer only has one notes entry so this function will allow
     * generic access / creation of that notes entry.
     *
     * Note that I call the entry plural 'notes' as it can contain many notes but
     * there is only one entry for every custid => peerid pair.
     *
     * @param $notes string The notes to add / update
     * @param $prepend bool If true, the existing notes are not overwritten but rather added to by prepended $notes
     */
    public function updateNotes( $notes, $prepend = false )
    {
        // does a notes entry exist?
        if( $this['notes_id'] != null )
        {
            if( $prepend )
                $this['Notes']['notes'] = $notes . "\n\n" . $this['Notes']['notes'];
            else
                $this['Notes']['notes'] = $notes;

            $this->Notes->save();
            return true;
        }

        // before creating one, make sure one does not exist for another LAN
        $n = Doctrine_Query::create()
            ->from( 'MyPeeringMatrixNotes n' )
            ->where( 'n.custid = ? and n.peerid = ?' )
            ->fetchOne( array( $this['custid'], $this['peerid'] ), Doctrine_Core::HYDRATE_RECORD );

        if( $n )
        {
            $this['notes_id'] = $n['id'];

            if( $prepend )
                $n['notes'] = $notes . "\n\n" . $n['notes'];
            else
                $n['notes'] = $notes;

            $n->save();
            $this->save();
        }
        else
        {
	        // otherwise create a notes entry
	        $n = new MyPeeringMatrixNotes();
	        $n['custid'] = $this['custid'];
	        $n['peerid'] = $this['peerid'];
	        $n['notes']  = $notes;
	        $n->save();
	        $this['notes_id'] = $n['id'];
	        $this->save();
        }

        // update the notes of any other LAN also
        $records = Doctrine_Query::create()
            ->from( 'MyPeeringMatrix m' )
            ->where( 'm.custid = ? and m.peerid = ?' )
            ->execute( array( $this['custid'], $this['peerid'] ), Doctrine_Core::HYDRATE_RECORD );

        foreach( $records as $r )
        {
            if( $r['notes_id'] != $n['id'] )
            {
                $r['notes_id'] = $n['id'];
                $r->save();
            }
        }

        return true;
    }

    /**
     * Get notes for a peer.
     *
     * Note that each peer only has one notes entry so this function will allow
     * generic access of that notes entry.
     *
     * Note that I call the entry plural 'notes' as it can contain many notes but
     * there is only one entry for every custid => peerid pair.
     *
     * @return string The notes entry or ''
     */
    public function getNotes()
    {
        return $this['notes_id'] != null ? $this['Notes']['notes'] : '';
    }

}