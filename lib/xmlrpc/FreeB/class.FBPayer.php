<?php
	// $Id$
	// $Author$

class FBPayer {

	function Name ( $key ) {
		$i = freemed::get_link_rec($key, 'insco');
		return $i['insconame'];
	} // end method Name

	function NationalPlanID ( $key ) {
		$i = freemed::get_link_rec($key, 'insco');
		return $i['inscoid'];
	} // end method NationalPlanID

	function Attn ( $key ) {
		// TODO: Add this to the database (STUB STUB)
		return '';
	} // end method Attn

	function StreetAddress ( $key ) {
		$i = freemed::get_link_rec($key, 'insco');
		return $i['inscoaddr1'];
	} // end method StreetAddress

	function City ( $key ) {
		$i = freemed::get_link_rec($key, 'insco');
		return $i['inscocity'];
	} // end method City

	function State ( $key ) {
		$i = freemed::get_link_rec($key, 'insco');
		return $i['inscostate'];
	} // end method State

	function Zipcode ( $key ) {
		$i = freemed::get_link_rec($key, 'insco');
		return $i['inscozip'];
	} // end method Zipcode

	function PhoneCountry ( $key ) {
		// TODO: Make PhoneCountry actually work
		return '';
	} // end method PhoneCountry

	function PhoneArea ( $key ) {
		// TODO: Needs i18n
		$i = freemed::get_link_rec($key, 'insco');
		return substr($i['inscophone'], 0, 3);
	} // end method PhoneArea

	function PhoneNumber ( $key ) {
		// TODO: Needs i18n
		$i = freemed::get_link_rec($key, 'insco');
		return substr($i['inscophone'], 3, 7);
	} // end method PhoneNumber

	function isMedicare ( $key ) {
		$i = freemed::get_link_rec($key, 'insco');
		$mods = explode (':', $i['inscomod']);

		// This is *such* a hack... and relies on a value in the db.

		// Do a search for medicare A
		$q = $GLOBALS['sql']->query('SELECT * FROM insmod '.
			'WHERE insmod = \'MA\'');
		$r = $GLOBALS['sql']->fetch_array($i_q);
		foreach ($mods AS $k => $v) {
			if ($v == $r['insmod']) return true;
		}

		// Do a search for medicare B
		$q = $GLOBALS['sql']->query('SELECT * FROM insmod '.
			'WHERE insmod = \'MB\'');
		$r = $GLOBALS['sql']->fetch_array($i_q);
		foreach ($mods AS $k => $v) {
			if ($v == $r['insmod']) return true;
		}

		// If neither one found, return false
		return false;
	} // end method isMedicare

	function isChampus ( $key ) {
		$i = freemed::get_link_rec($key, 'insco');
		$mods = explode (':', $i['inscomod']);

		// Do a search for Champus
		$q = $GLOBALS['sql']->query('SELECT * FROM insmod '.
			'WHERE insmod = \'CH\'');
		$r = $GLOBALS['sql']->fetch_array($i_q);
		foreach ($mods AS $k => $v) {
			if ($v == $r['insmod']) return true;
		}

		// If not found, return false
		return false;
	} // end method isChampus

	function isChampusva ( $key ) {
		return FBPayer::isChampus($key);
	} // end method isChampusva

	function isMedicaid ( $key ) {
		$i = freemed::get_link_rec($key, 'insco');
		$mods = explode (':', $i['inscomod']);

		// Do a search for Champus
		$q = $GLOBALS['sql']->query('SELECT * FROM insmod '.
			'WHERE insmod = \'MC\'');
		$r = $GLOBALS['sql']->fetch_array($i_q);
		foreach ($mods AS $k => $v) {
			if ($v == $r['insmod']) return true;
		}

		// If not found, return false
		return false;
	} // end method isMedicaid

	function isBcbs ( $key ) {
		$i = freemed::get_link_rec($key, 'insco');
		$mods = explode (':', $i['inscomod']);

		// Do a search for Champus
		$q = $GLOBALS['sql']->query('SELECT * FROM insmod '.
			'WHERE insmod = \'BL\'');
		$r = $GLOBALS['sql']->fetch_array($i_q);
		foreach ($mods AS $k => $v) {
			if ($v == $r['insmod']) return true;
		}

		// If not found, return false
		return false;
	} // end method isBcbs

	function isFeca ( $key ) {
		$i = freemed::get_link_rec($key, 'insco');
		$mods = explode (':', $i['inscomod']);

		// Do a search for Champus
		$q = $GLOBALS['sql']->query('SELECT * FROM insmod '.
			'WHERE insmod = \'FI\'');
		$r = $GLOBALS['sql']->fetch_array($i_q);
		foreach ($mods AS $k => $v) {
			if ($v == $r['insmod']) return true;
		}

		// If not found, return false
		return false;
	} // end method isFeca

	function isOtherHCFA ( $key ) {
		return ! ( FBPayer::isMedicare($key) or
			FBPayer::isMedicaid($key) or
			FBPayer::isChampus($key) or
			FBPayer::isBcbs($key) or
			FBPayer::isFeca($key) );
	} // end method isOtherHCFA

	function DiagnosisCodeSet ( ) {
		switch (freemed::config_value('icd')) {
			case '10': 
				return 'ICD10'; break;
			case '9': 
			default:
				return 'ICD9'; break;
		}
	} // end method DiagnosisCodeSet

	function ProcedureCodeSet ( ) {
		// TODO: actually look up CPT-4 or CPT-5
		return 'CPT4';
	} // end method ProcedureCodeSet

	function isHCFACondensed ( ) {
		// TODO: May wish to actually do this from global config
		return true;
	} // end method isHCFACondensed

} // end class FBPayer

?>
