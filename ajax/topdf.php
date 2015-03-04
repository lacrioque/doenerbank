<?php
session_start();
if(!isset($_SESSION['user_id']) && $_SESSION['user']['admin'] === 1){
die('{"success": "false", "message": "Nicht eigeloggt, bitte einloggen!"}');
}
ini_set('display_errors', '1');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '123455678');
require_once("../inc/defines.php");
include("../inc/database.php");
include("../inc/einzelbestellung.php");
include("../inc/user.php");
include("../inc/artikel.php");
include("../inc/bestellung.php");
$values = $_GET;
$out = "";
$DB = new DB();
$Objbestellung = new bestellung();

$alleBestellungen = $Objbestellung->showTagesbestellung();
$body = "<body>";
$body .= "<table border=1>"
        . "<tr>"
        . "<td>Nutzer</td><td>Artikelname</td><td>Artikelpreis</td><td>Artikelbemerkung</td>"
        . "</tr>";

foreach($alleBestellungen as $I=>$bestellung){
    $body.="<tr>"
            . "<td>".$bestellung['Nutzer']."</td><td>".$bestellung['Artikelname']."</td><td>".$bestellung['Artikelpreis']."</td><td>".$bestellung['Artikelbemerkung']."</td>"
            . "</tr>";
}

$body .= "</table>";

$head = "<!DOCTYPE HTML>"
        . "<html>"
        . "<head>"
        . "<meta charset=\"utf-8\">"
        . "</head>";
$footer = "<br><br><br>"
        . "<div>Copyright DoenerbankGroup - Steffen Pfeil, Lennart Potthoff, Markus Flür</div>"
        . "</body>"
        . "</html>";

$html = $head.$body.$footer;
//echo $html;
$pdf_name = 'einkaufsliste_'.$Objbestellung->getDatumFileString().".pdf";
$pdf_pfad = '/var/www/doenerbank/pdf/'.$pdf_name;
require('../inc/tcpdf/tcpdf.php');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator("Die Doenerbank");
$pdf->SetAuthor('Doenerbank Group');
$pdf->SetTitle($pdf_name);
$pdf->SetKeywords('Doener, Bestellung, lecker');
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->lastPage();
$pdf->Output($pdf_pfad, 'F');
$pdf->Output($pdf_name, 'I');