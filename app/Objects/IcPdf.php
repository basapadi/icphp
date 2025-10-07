<?php
namespace App\Objects;
require_once base_path('libs/tcpdf/tcpdf.php');
class IcPdf extends \TCPDF {

	private object $_company;
	public function setCompany(object $company)
	{
		$this->_company = $company;
	}

	public function build()
	{
		$this->SetFont('helvetica', '', 20);
		$this->SetMargins(15, 15, 15);
        $this->SetAutoPageBreak(TRUE, 15);
        $this->AddPage();

        $this->Ln(16);
	}

	public function Header() {
		// Logo
		$this->Image($this->_company->logo, 15, 3, 15, '', 'PNG', '', 'T', true, 300, '', false, false, 0, false, false, false);
		// Set font
		$this->Ln(5);
		$this->SetFont('helvetica', 'B', 18);
		$this->Cell(0, 15, '           '.trim(@$this->_company->namaToko), 0, false, 'L', 0, '', 0, false, 'M', 'M');
		$this->Ln(5);
		$this->SetFont('helvetica', '', 10);
		$this->Cell(0, 10, '                    '.@$this->_company->alamat, 0, false, 'L', 0, '', 0, false, 'M', 'M');
		$this->Ln(4);
		$this->SetFont('helvetica', '', 8);
		$this->Cell(0, 10, '                         '."telp. {$this->_company->telepon} | email: {$this->_company->email}", 0, false, 'L', 0, '', 0, false, 'M', 'M');
		//$this->SetAuthor(auth()->user()->username);
		// set default monospaced font
		$this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		$this->Ln(3);
		$this->SetLineWidth(0.5); // ketebalan garis
        $this->Line(15, $this->GetY(), 200, $this->GetY());
	}


	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}
}