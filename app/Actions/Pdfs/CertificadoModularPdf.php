<?php

namespace App\Actions\Pdfs;

use App\Models\Certificado;
use App\Models\Instituto;
use App\Models\ModuloCompetencia;
use Carbon\Carbon;
use P3dr0\Fpdf\Pdf;

class CertificadoModularPdf
{
    protected $pdf;
    public function print(Certificado  $certificado,$instituto)
    {
        ob_get_clean();
        $fileName='certificado_modular_'.$certificado->estudiante->numero_documento;
        $this->pdf=new Pdf();
        $this->pdf->AddPage('L','A4');
        $this->pdf->SetTitle($fileName);
        $this->pdf->SetAutoPageBreak(false, 10);
        $this->pdf->SetMargins(20,15,20);
        $this->pdf->setFont('Arial', '', '8');
        $this->pdf->SetFillColor(255, 255, 255);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetY(30);
        $this->pdf->Image(public_path().'/images/logo-minedu.png',123,14,48);
        $this->pdf->setFont('Arial', 'B', '11');//--268//$this->pdf->getWidthPage()-30
        $this->pdf->MultiCell(0,5.5,utf8_to_decode('INSTITUTO DE EDUCACIÓN SUPERIOR '.$instituto->TipoGestionEducativa->descripcion),0,'C');
        $this->pdf->MultiCell(0,5.5,$instituto->denominacion,0,'C');
        $this->pdf->Ln(8);
        $this->pdf->setFont('Arial', 'B', '15');
        $this->pdf->Cell(0,5,'CERTIFICADO MODULAR',0,1,'C');
        $this->pdf->Ln(5);
        $this->pdf->setFont('Arial', '', '9');
        $this->pdf->Cell(20,5,'Otorgado a:',0,0,'L');
        $this->pdf->setFont('Arial', 'B', '9.8');
        $this->pdf->Cell($this->pdf->getWidthPage()-80,5,utf8_to_decode($certificado->estudiante->apellidos_completos),0,1,'C');
        $this->pdf->Ln(5);
        $this->pdf->setFont('Arial', '', '9');
        $this->pdf->Cell(86,5,utf8_to_decode('Por haber aprobado satisfactoriamente el módulo formativo:'),0,1,'L');
        $this->pdf->Ln(3);
        $this->pdf->setFont('Arial', 'B', '9.5');
        $this->pdf->MultiCell(0,5,utf8_to_decode($certificado->modulo->descripcion),0,'C',0);
        $this->pdf->Ln(5);
        $this->pdf->setFont('Arial', '', '9');
        $this->pdf->Cell(60,5,utf8_to_decode('Correspondiente al programa de estudio:'),0,1,'L');
        $this->pdf->Ln(4);
        $this->pdf->setFont('Arial', 'B', '9.5');
        $this->pdf->MultiCell(0,5,utf8_to_decode($certificado->planEstudio->programaEstudio->descripcion),0,'C',0);
        $this->pdf->Ln(5);
        $this->pdf->setFont('Arial', '', '9');
        $html='<p>Desarrollado del <b>'.Carbon::parse($certificado->fecha_inicio)->format('d/m/Y').'</b>';
        $html.=' al <b>'.Carbon::parse($certificado->fecha_fin)->format('d/m/Y').'</b>,';
        $html.=' con un total de <b>'.$certificado->modulo->total_creditos.'</b> '.utf8_to_decode('créditos').', equivalente a ';
        $html.='<b>'.$certificado->modulo->total_horas.'</b> horas.';
        $html.='</p>';
        $this->pdf->WriteHTML($html);
        $this->pdf->Ln(10);
        $this->pdf->Cell(0,5,$instituto->distrito.','.fecha_largo($certificado->fecha),0,1,'R');
        $this->pdf->SetY($this->pdf->GetPageHeight()-35);
        $this->pdf->setFont('Arial', '', '9');
        $this->pdf->Cell(0,5,utf8_to_decode($certificado->director_general),0,1,'C');
        $this->pdf->Cell(0,5,'Director General',0,1,'C');
        $this->competetionPage($certificado);

        return $this->pdf->Output('', 'certificado_modular_'.$fileName.'.pdf');
    }
    public function competetionPage($certificado)
    {
        $competencias=(new ModuloCompetencia())->getCompetenciasIndicadores($certificado->id_referencia);
        $this->pdf->AddPage('L','A4');
        $this->pdf->SetMargins(20,15,20);
        $this->pdf->setFont('Arial', 'B', '9');
        $this->pdf->SetFillColor(215, 216, 217);
        $this->pdf->Cell(90,6,'UNIDAD DE COMPETENCIA',1,0,'C',true);
        $this->pdf->Cell(170,6,'INDICADORES DE LOGRO',1,0,'C',true);
        $this->pdf->SetFillColor(255, 255, 255);
        $this->pdf->setFont('Arial', '', '6');
        $this->pdf->Ln();
        foreach($competencias as $competencia){
            $heigth=5;
            $indicadores=json_decode($competencia->indicadores,true);
            if($competencia->cantidad_indicadores<>0){
                $heigth=$competencia->cantidad_indicadores*$heigth;
            }
            $y=$this->pdf->GetY();
            $this->pdf->MultiCell(90,3.4,utf8_to_decode($competencia->codigo.' '.$competencia->descripcion),0,'L',0);
            $this->pdf->Ln();
            $yI=$this->pdf->GetY();

            if($competencia->cantidad_indicadores>0){
                $this->pdf->SetY($y);
                foreach($indicadores as $key=>$indicador){
                    $this->pdf->SetX(110);
                    $border='R';
                    if(($key+1)==$competencia->cantidad_indicadores){
                        $border='BR';
                    }

                    $this->pdf->MultiCell(170,3.4,utf8_to_decode($indicador['numero'].'.- '.$indicador['descripcion']),$border,'L',0);
                    $yI=$this->pdf->GetY();
                }
                $this->pdf->Rect(20,$y,90,$yI-$y);
            }else{
                $this->pdf->Rect(20,$y,90,$yI-$y);
                $this->pdf->Rect(110,$y,170,$yI-$y);
            }
        }
        $this->pdf->SetY($this->pdf->GetPageHeight()-35);
        $this->pdf->setFont('Arial', 'B', '9');
        $this->pdf->Rect(120,$this->pdf->GetY()-1,58,12);
        $this->pdf->Cell(0,5,utf8_to_decode('Código de Registro Institucional'),0,1,'C');
        $this->pdf->Cell(0,5,$certificado->numero_registro_institucional,0,1,'C');

    }

}
