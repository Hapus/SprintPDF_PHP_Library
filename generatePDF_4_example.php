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
	* Create a PDF from raw HTML; also, set some page parameters for PDF generation
	**/

	require('lib/sprintpdf.php');

	try {
		//Add your Sprint PDF username & pasword below
		$sprintPDFObj = new sprintPDF("[Enter your Sprint PDF username here]", "[Enter your Sprint PDF password here]");

		//Create the HTML
		$HTML = "<html><body>Hello world!</body></html>";

		//Set some options; a full list of options can be found at http://www.sprintpdf.com/Documentation/options
		$options[] = '--orientation "landscape"';
		$options[] = '--header-center "Generated by SprintPDF"';
		$options[] = '--header-right "Page [page] of [topage]"';

		//Make the call to convert
		$pdf = $sprintPDFObj->convertHTML($HTML, null , implode(' ', $options));

		// set HTTP response headers
		header("Content-Type: application/pdf");
		header("Cache-Control: no-cache");
		header("Accept-Ranges: none");

		// send the generated PDF
		echo $pdf;
	} catch(sprintPDFException $e){
		print_r($e);
	}
?>