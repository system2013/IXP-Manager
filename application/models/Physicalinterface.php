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
class Physicalinterface extends BasePhysicalinterface
{

    const STATUS_CONNECTED       = 1;
    const STATUS_DISABLED        = 2;
    const STATUS_NOTCONNECTED    = 3;
    const STATUS_XCONNECT        = 4;
    const STATUS_QUARANTINE      = 5;

    public static $STATES = array(
    1 => 'STATUS_CONNECTED',
    2 => 'STATUS_DISABLED',
    3 => 'STATUS_NOTCONNECTED',
    4 => 'STATUS_XCONNECT',
    5 => 'STATUS_QUARANTINE'
    );

    public static $STATES_TEXT = array(
    1 => 'Connected',
    2 => 'Disabled',
    3 => 'Not Connected',
    4 => 'Awaiting X-Connect',
    5 => 'Quarantine'
    );

    public static $SPEED = array(
    10    => '10 Mbps',
    100   => '100 Mbps',
    1000  => '1000 Mbps',
    10000 => '10000 Mbps'
    );

    public static $DUPLEX = array(
        'full'   => 'full',
        'half'   => 'half'
        );


        public function setUp()
        {
            parent::setUp();

            $this->hasOne( 'Switchport',        array( 'local' => 'switchportid',        'foreign' => 'id') );
            $this->hasOne( 'Virtualinterface',  array( 'local' => 'virtualinterfaceid',  'foreign' => 'id') );
        }

}