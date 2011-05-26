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


/*
 *
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

/**
 *
 * @package INEX_Form
 */
class INEX_Form_VLAN extends INEX_Form
{
    /**
     *
     *
     */
    public function __construct( $options = null, $isEdit = false, $cancelLocation )
    {
        parent::__construct( $options, $isEdit );

        ////////////////////////////////////////////////
        // Create and configure elements
        ////////////////////////////////////////////////

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255 ) )
        ->setRequired( true )
        ->setLabel( 'Name' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $name );



        $number = $this->createElement( 'text', 'number' );
        $number->addValidator( 'int' )
        ->addValidator( 'between', false, array( 1, 4096 ) )
        ->setRequired( true )
        ->setLabel( 'Tag' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $number );

        $rcvrfname = $this->createElement( 'text', 'rcvrfname' );
        $rcvrfname->addValidator( 'stringLength', false, array( 1, 255 ) )
        ->setLabel( 'RC VRF Name' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $rcvrfname );





        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
        ->setRequired( false )
        ->addFilter( new INEX_Filter_StripSlashes() )
        ->setAttrib( 'cols', 60 )
        ->setAttrib( 'rows', 5 );
        $this->addElement( $notes );


        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='"
        . $cancelLocation . "'" ) );
        $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );

    }

}

?>