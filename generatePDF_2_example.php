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
	* Create a PDF from a URL and open the *Save as* dialog box allow the user to save it on his/her computer
	**/

	require('lib/sprintpdf.php');

	try {
		//Add your Sprint PDF username & pasword below
		$sprintPDFObj = new sprintPDF("[Enter your Sprint PDF username here]", "[Enter your Sprint PDF password here]");

		//Make the call to convert
		$url = "http://www.google.co.jp/";
		$pdf = $sprintPDFObj->convertURI($url);

		// set HTTP response headers
		header("Content-Type: application/pdf");
		header("Cache-Control: no-cache");
		header("Accept-Ranges: none");
		header("Content-Disposition: attachment; filename=\"google_com_produced_by_sprintPDF.pdf\"");

		// send the generated PDF
		echo $pdf;
	} catch(sprintPDFException $e){
		print_r($e);
	}
?>