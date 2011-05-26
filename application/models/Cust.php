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
 * This class has been auto-generated by the Doctrine ORM Framework
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
class Cust extends BaseCust
{

    const TYPE_FULL       = 1;
    const TYPE_ASSOCIATE  = 2;
    const TYPE_INTERNAL   = 3;
    const TYPE_PROBONO    = 4;

    public static $CUST_TYPES_TEXT = array(
	    Cust::TYPE_FULL      => 'Full',
	    Cust::TYPE_ASSOCIATE => 'Associate',
	    Cust::TYPE_INTERNAL  => 'Internal',
	    Cust::TYPE_PROBONO   => 'Pro-bono'
    );


    const STATUS_NORMAL       = 1;
    const STATUS_NOTCONNECTED = 2;
    const STATUS_SUSPENDED    = 3;

    public static $CUST_STATUS_TEXT = array(
	    Cust::STATUS_NORMAL           => 'Normal',
	    Cust::STATUS_NOTCONNECTED     => 'Not Connected',
	    Cust::STATUS_SUSPENDED        => 'Suspended',
    );


    public function setUp()
    {
        $this->hasMany(
            'User',
            array(
                'local' => 'id',
                'foreign' => 'custid'
            )
        );

        $this->hasOne(
            'Irrdbconfig',
            array(
                'local' => 'irrdb',
                'foreign' => 'id'
            )
        );
    }

    /**
     * Turn a type integer into its descriptive string
     *
     * @param $type int If null, then the type of $this object.
     * @return string The descriptive member type
     */
    public function getMemberTypeString( $type = null )
    {
        return Cust::$CUST_TYPES_TEXT[ ( $type === null ? $this['type'] : $type )];
    }

    /**
     * Turn a status integer into its descriptive string
     *
     * @param $type int If null, then the status of $this object.
     * @return string The descriptive member status
     */
    public function getMemberStatusString( $status = null )
    {
        return Cust::$CUST_STATUS_TEXT[ ( $status === null ? $this['status'] : $status )];
    }

    /**
     * Return a Doctrine result of the users IXP connections.
     */
    public function getConnections()
    {
        return Doctrine_Query::create()
            ->from( 'Virtualinterface vi' )
            ->leftJoin( 'vi.Cust c' )
            ->leftJoin( 'vi.Physicalinterface pi' )
            ->leftJoin( 'vi.Vlaninterface vli' )
            ->leftJoin( 'vli.Ipv4address v4' )
            ->leftJoin( 'vli.Ipv6address v6' )
            ->leftJoin( 'vli.Vlan v' )
            ->leftJoin( 'pi.Switchport sp' )
            ->leftJoin( 'sp.SwitchTable s' )
            ->leftJoin( 's.Cabinet cb' )
            ->leftJoin( 'cb.Location l' )
            ->where( 'c.id = ?', $this->id )
            ->orderBy( 'pi.monitorindex' )
            ->execute();
    }

    /**
     * Method to check if the customer is a route server client.
     *
     * @return bool True if the customer is a route server client
     * @throws INEX_Exception
     *
     */
    public function isRouteServerClient( $vlan = null )
    {
        if( $vlan === null )
            throw new INEX_Exception( 'Cust->isRouteServerClient() needs a VLAN tag' );

        $rsClient = false;
        foreach( $this->getConnections() as $connection )
        {
            foreach( $connection->Vlaninterface as $interface )
            {
                if( $interface->Vlan->number == $vlan && $interface->rsclient == 1 )
                {
                    $rsClient = true;
                    break 2;
                }
            }
        }

        return $rsClient;
    }


    /**
     * Method to check is the customer is an AS112 service client.
     *
     * SHORTCOMING: Only looks at the primary peering LAN
     *
     * @return bool True if the customer is an As112 client
     *
     */
    public function isAS112Client()
    {
        $as112Enabled = true;
        foreach( $this->getConnections() as $connection )
            foreach( $connection->Vlaninterface as $interface )
                if( $interface->Vlan->number == 10 && $interface->as112client == 0 )
                {
                    $as112Enabled = false;
                    break 2;
                }

        return $as112Enabled;
    }

    /**
     * Method to see if the customer is an associate member or not
     *
     * @return bool True if the customer is an associate member
     */
    public function isAssociateMember()
    {
        return $this['type'] == self::TYPE_ASSOCIATE;
    }

    /**
     * Method to see if the customer is a full member or not
     *
     * @return bool True if the customer is a full member
     */
    public function isFullMember()
    {
        return $this['type'] == self::TYPE_FULL || $this['type'] == self::TYPE_PROBONO;
    }


}
