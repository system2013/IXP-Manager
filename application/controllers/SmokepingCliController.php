<?php

/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * Controller: Smokeping CLI Actions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SmokepingCliController extends IXP_Controller_CliAction
{
    public function genConfAction()
    {
        // what IXP are we running on here?
        if( $this->multiIXP() )
        {
            $ixpid = $this->getParam( 'ixp', false );
            
            if( !$ixpid || !( $ixp = $this->getD2R( '\\Entities\\IXP' )->find( $ixpid ) ) )
                die( "ERROR: Invalid or no IXP specified.\n" );
        }
        else
            $ixp = $this->getD2R( '\\Entities\\IXP' )->getDefault();
        
        $this->view->ixp                   = $ixp;
        
        $this->view->targets = $this->genConf_getTargets( $ixp );
        
        if( isset( $this->_options['smokeping']['conf']['dstfile'] ) )
        {
            if( !$this->writeConfig( $this->_options['smokeping']['conf']['dstfile'], $this->view->render( 'smokeping-cli/conf/index.cfg' ) ) )
                fwrite( STDERR, "Error: could not save configuration data\n" );
        }
        else
            echo $this->view->render( 'smokeping-cli/conf/index.cfg' );
    }
    
    /**
     * Utility function to slurp all VLAN interfaces ports from the database and arrange them in
     * arrays by infrastructure.
     *
     * @param \Entities\IXP $ixp
     */
    public static function genConf_getTargets( $ixp )
    {
        $data = [];
    
        foreach( $ixp->getInfrastructures() as $infra )
        {
            $data[ $infra->getId() ]['name']      = preg_replace( '/#/', '', $infra->getName() );
            $data[ $infra->getId() ]['shortname'] = preg_replace( '/#/', '', $infra->getShortname() );
            $data[ $infra->getId() ]['vlans']     = [];
            
            foreach( $infra->getVLANs() as $v )
            {
                if( $v->getPrivate() )
                    continue;

                $vlan = [];
                $vlan[ 'name' ]   = preg_replace( '/#/', '', $v->getName() );
                $vlan[ 'number' ] = preg_replace( '/#/', '', $v->getNumber() );
                $vlan[ 'ints' ]   = [];
                
                foreach( $v->getVlanInterfaces() as $vli )
                {
                    if( $vli->getVirtualInterface()->getCustomer()->getStatus() != \Entities\Customer::STATUS_NORMAL )
                        continue;
                    
                    $havePhysInt = false;
                    foreach( $vli->getVirtualInterface()->getPhysicalInterfaces() as $pi )
                    {
                        if( $pi->getStatus() == \Entities\PhysicalInterface::STATUS_CONNECTED )
                        {
                            $havePhysInt = true;
                            break;
                        }
                    }
                    
                    if( !$havePhysInt )
                        continue;
                    
                    $vlan[ 'ints' ][ $vli->getId() ]['name']            = preg_replace( '/#/', '', $vli->getVirtualInterface()->getCustomer()->getName() );
                    $vlan[ 'ints' ][ $vli->getId() ]['shortname']       = preg_replace( '/#/', '', $vli->getVirtualInterface()->getCustomer()->getShortname() );
                    $vlan[ 'ints' ][ $vli->getId() ]['abbreviatedname'] = preg_replace( '/#/', '', $vli->getVirtualInterface()->getCustomer()->getAbbreviatedName() );

                    if( $vli->getIpv4enabled() && $vli->getIpv4canping() )
                        $vlan[ 'ints' ][ $vli->getId() ]['ipv4'] =  $vli->getIpv4Address()->getAddress();
                    else
                        $vlan[ 'ints' ][ $vli->getId() ]['ipv4'] =  false;
                    
                    if( $vli->getIpv6enabled() && $vli->getIpv6canping() )
                        $vlan[ 'ints' ][ $vli->getId() ]['ipv6'] = $vli->getIpv6Address()->getAddress();
                    else
                        $vlan[ 'ints' ][ $vli->getId() ]['ipv6'] = false;
                }
                
                $data[ $infra->getId() ]['vlans'][ $v->getId() ] = $vlan;
            }
        }

        return $data;
    }
    
}

