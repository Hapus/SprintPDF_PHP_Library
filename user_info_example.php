<?php
	/**
	* @file
	* @author  Sprint PDF <http://www.sprintpdf.com>
	* @version 1.0
	*
	* @section LICENSE
	*
	* This program is free software; you can redistribute it and/or
	* modify it under the terms of the GNU General Public License as
	* published by the Free Software Foundation; either version 2 of
	* the License, or (at your option) any later version.
	*
	* This program is distributed in the hope that it will be useful, but
	* WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	* General Public License for more details at
	* http://www.gnu.org/copyleft/gpl.html
	*
	* @section DESCRIPTION
	* Returns the user information for the provided credentials
	**/

	require('lib/sprintpdf.php');

	try {
		//Add your Sprint PDF username & pasword below
		$sprintPDFObj = new sprintPDF("[Enter your Sprint PDF username here]", "[Enter your Sprint PDF password here]");

		$info =  $sprintPDFObj->getInfo();
		print_r($info);
	} catch(sprintPDFException $e){
		print_r($e);
	}
?>