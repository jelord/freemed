<?php
 // $Id$
 //
 // Authors:
 // 	Jeff Buchbinder <jeff@freemedsoftware.org>
 //	Fred Forester <fforest@netcarrier.com>
 //
 // FreeMED Electronic Medical Record and Practice Management System
 // Copyright (C) 1999-2007 FreeMED Software Foundation
 //
 // This program is free software; you can redistribute it and/or modify
 // it under the terms of the GNU General Public License as published by
 // the Free Software Foundation; either version 2 of the License, or
 // (at your option) any later version.
 //
 // This program is distributed in the hope that it will be useful,
 // but WITHOUT ANY WARRANTY; without even the implied warranty of
 // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 // GNU General Public License for more details.
 //
 // You should have received a copy of the GNU General Public License
 // along with this program; if not, write to the Free Software
 // Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

LoadObjectDependency('org.freemedsoftware.core.EMRModule');

class PatientCoverages extends EMRModule {

	var $MODULE_NAME = "Patient Coverage";
	var $MODULE_VERSION = "0.3.2";
	var $MODULE_FILE = __FILE__;
	var $MODULE_UID = "bfb88968-fd81-41c7-b3ad-93098c3bc6be";

	var $PACKAGE_MINIMUM_VERSION = '0.8.2';

	var $table_name    = "coverage";
	var $record_name   = "Patient Coverage";
	var $patient_field = "covpatient";

	var $variables = array (
		'covdtadd',
		'covdtmod',
		'covpatient',
		'coveffdt',
		'covinsco',
		'covpatinsno',
		'covpatgrpno',
		'covtype',
		'covstatus',
		'covrel',
		'covlname',
		'covfname',
		'covmname',
		'covaddr1',
		'covaddr2',
		'covcity',
		'covstate',
		'covzip',
		'covdob',
		'covsex',
		'covssn',
		'covinstp',
		'covprovasgn',
		'covbenasgn',
		'covrelinfo',
		'covrelinfodt',
		'covplanname',
		'covisassigning',
		'covschool',
		'covemployer',
		'covcopay',
		'covdeduct',
		'user'
	);

	// contructor method
	public function __construct ( ) {
		// __("Patient Coverage")

		$this->summary_vars = array (
			__("Plan") => 'insconame',
			__("Date") => 'coveffdt'
		);
		$this->summary_query = array (
			"IF ( covstatus, 'Deleted', 'Active' ) AS covstat",
			"ELT ( covtype, 'Primary', 'Secondary', 'Tertiary', 'WorkComp' ) AS covtp"
		);
		$this->summary_query_link = array (
			'covinsco' => 'insco'
		);

		$this->_SetAssociation('EmrModule');
		$this->acl = array ( 'bill', 'emr' );

		// Call parent constructor
		parent::__construct ( );
	} // end function PatientCoverages

	protected function add_pre ( &$data ) {
		$s = CreateObject( 'org.freemedsoftware.api.Scheduler' );
		$data['covstatus'] = ACTIVE;
		$data['covdtadd'] = date('Y-m-d');
		$data['covdtmod'] = date('Y-m-d');
		$data['covdteff'] = $s->ImportDate( $data['covdteff'] );
		$data['covrelinfodt'] = $s->ImportDate( $data['covrelinfodt'] );
		$data['covdob'] = $s->ImportDate( $data['covdob'] );
		$data['user'] = freemed::user_cache()->user_number;
	}

	protected function mod_pre ( &$data ) {
		$s = CreateObject( 'org.freemedsoftware.api.Scheduler' );
		$data['covstatus'] = ACTIVE;
		$data['covdtmod'] = date('Y-m-d');
		$data['covdteff'] = $s->ImportDate( $data['covdteff'] );
		$data['covrelinfodt'] = $s->ImportDate( $data['covrelinfodt'] );
		$data['covdob'] = $s->ImportDate( $data['covdob'] );
		$data['user'] = freemed::user_cache()->user_number;
	}

	// Method: GetCoverages
	//
	//	Get list of coverages for a patient.
	//
	// Parameters:
	//
	//	$patient - Patient ID
	//
	//	$asof - (optional) As of a particular date of effectiveness.
	//
	// Returns:
	//
	//	Array of arrays, [ coverage description, id ]
	//
	public function GetCoverages ( $patient, $asof = NULL ) {
		$s = CreateObject( 'org.freemedsoftware.api.Scheduler' );
		$q = "SELECT CONCAT( '[', c.covrel, '] ', i.insconame, ' / ', c.coveffdt ) AS k, c.id AS v FROM coverage c LEFT OUTER JOIN insco i ON c.covinsco = i.id WHERE c.covpatient = ".$GLOBALS['sql']->quote( $patient ). ( $asof != NULL ? " AND c.coveffdt <= ".$GLOBALS['sql']->quote( $s->ImportDate( $asof ) ) : '' )." ORDER BY c.covstatus DESC";
		$r = $GLOBALS['sql']->queryAll( $q );
		foreach ( $r AS $row ) {
			$res[] = array ( $row['k'], $row['v'] );
		}
		return $res;
	} // end method GetCoverages

	// Method: RemoveOldCoverage
	//
	//	Move old coverage to deleted status.
	//
	// Parameters:
	//
	//	$patient - Patient record id
	//
	//	$covtype - Type of coverage to remove.
	//
	// Returns:
	//
	//	Boolean, if successful.
	//
	public function RemoveOldCoverage ( $patient, $covtype ) {
		$query = "UPDATE coverage SET covstatus=".$GLOBALS['sql']->quote( DELETED )." WHERE covtype=".$GLOBALS['sql']->quote( $covtype )." AND covpatient=".$GLOBALS['sql']->quote( $patient );
		$result = $GLOBALS['sql']->query( $query );
		return ( $result ? true : false );
	} // end method RemoveOldCoverage

} // end class PatientCoverages

register_module("PatientCoverages");

?>