<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\DB;

class PdfController extends Controller
{
//     public function index(){
//         $mpdf = new \Mpdf\Mpdf();
//         $mpdf->showImageErrors = true;

//         $message = "No activity this week";
//         $id = auth()->id();
//         $carbon = \Carbon\Carbon::now();  
//         $weekStartDate = $carbon->startOfWeek()->format('Y-m-d H:i');
//         $weekEndDate = $carbon->endOfWeek()->format('Y-m-d H:i');
//         $todos = DB::select("SELECT * FROM todos WHERE user_id = $id AND created_at BETWEEN '$weekStartDate' AND '$weekEndDate' UNION SELECT * FROM todos WHERE id IN(SELECT todo_id FROM transfers WHERE user_id = $id) AND created_at BETWEEN '$weekStartDate' AND '$weekEndDate'");

//         $id = auth()->id();
//         $user = DB::table('users')->where('id',$id)->get('name');
        
//         $user = $user[0]->name;

//         // $mpdf->AddPage('L');
//         // $mpdf->SetFontSize(16);
//         // $mpdf->writeHTML("
//         //     <table autosize='1' style='overflow: visible|hidden|wrap'>
//         //         <tr>
//         //             <td>image</td>
//         //             <td>ICTC Reporting System</td>
//         //             <td>image</td>
//         //         </tr>
//         //         <tr>
//         //             <td>Activity</td>
//         //             <td>Deadline</td>
//         //             <td>Progress</td>
//         //             <td>Status</td>
//         //             <td>Output</td>
//         //         </tr>
                
//         //     </table>
//         // ");
// //one end 

//             $header = '<!--mpdf
//             <htmlpageheader name="letterheader">
//                 <table width="100%" style=" font-family: sans-serif;">
//                     <tr>
//                         <td width="50%" style="color:#0000BB; ">
                            
//                         </td>
//                         <td width="50%" style="text-align: center; font-size: 25pt; vertical-align: center;">
//                             ICTC.<br />
//                             <span style="font-weight: bold; font-size: 20pt;">Weekly Report</span><br />
//                             <span style="font-weight: bold; font-size: 15pt;">Date : {DATE jS F Y}</span>
//                         </td>
//                         <td width="50%" style="text-align: right; vertical-align: top;">
                            
//                         </td>
//                     </tr>
//                 </table>
//             </htmlpageheader>
            
//             <htmlpagefooter name="letterfooter2">
//                 <div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; font-family: sans-serif; ">
//                     Page {PAGENO} of {nbpg}
//                 </div>
//             </htmlpagefooter>
//         mpdf-->
        
//         <style>
//             @page {
//                 margin-top: 2.5cm;
//                 margin-bottom: 2.5cm;
//                 margin-left: 2cm;
//                 margin-right: 2cm;
//                 footer: html_letterfooter2;
//                 background-color: pink;
//             }
        
//             @page :first {
//                 margin-top: 8cm;
//                 margin-bottom: 4cm;
//                 header: html_letterheader;
//                 footer: _blank;
//                 resetpagenum: 1;
//                 background-color: lightblue;
//             }
        
//             @page letterhead {
//                 margin-top: 2.5cm;
//                 margin-bottom: 2.5cm;
//                 margin-left: 2cm;
//                 margin-right: 2cm;
//                 footer: html_letterfooter2;
//                 background-color: pink;
//             }
        
//             @page letterhead :first {
//                 margin-top: 8cm;
//                 margin-bottom: 4cm;
//                 header: html_letterheader;
//                 footer: _blank;
//                 resetpagenum: 1;
//                 background-color: lightblue;
//             }
//         </style>';
        
//         $tabledata = '
//         I will load data table here!!!
//         ';
        
//         $mpdf->WriteHTML($header);
//         $mpdf->WriteHTML($tabledata);

//         // $html = view('html_convert_pdf',[],true);
//         // $mpdf->WriteHTML($html);

//         $mpdf->debug = true; 


//         $mpdf->output();

//     }
    
    protected $fpdf;
 
    public function __construct()
    {
        $this->fpdf = new Fpdf;
    }
    // ->fpdf

    // Page header
    function Header()
    {
        // Logo
        // $this->fpdf->Image('logo.png',10,6,30);
        // Arial bold 15
        $this->fpdf->SetFont('Arial','B',15);
        // Move to the right
        $this->fpdf->Cell(80);
        // Title
        
        // Line break
        $this->fpdf->Ln(20);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->fpdf->SetY(-15);
        // Arial italic 8
        $this->fpdf->SetFont('Arial','I',8);
        // Page number
        $this->fpdf->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

    public function index() 
    {
        $message = "No activity this week";
        $id = auth()->id();
        $carbon = \Carbon\Carbon::now();  
        $weekStartDate = $carbon->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = $carbon->endOfWeek()->format('Y-m-d H:i');
        $todos = DB::select("SELECT * FROM todos WHERE user_id = $id AND created_at BETWEEN '$weekStartDate' AND '$weekEndDate' UNION SELECT * FROM todos WHERE id IN(SELECT todo_id FROM transfers WHERE user_id = $id) AND created_at BETWEEN '$weekStartDate' AND '$weekEndDate'");
        $id = auth()->id();
        $user = DB::table('users')->where('id',$id)->get('name');
        $user = $user[0]->name;
        $this->fpdf->AddPage('l');
        $this->fpdf->SetFont('Times','',12);
        $this->fpdf->Cell(0,0,'ICTC',0,0,'C');
        $this->fpdf->Ln();
        // $this->fpdf->Cell(0,5,'MONTHLY REPORT');
        $this->fpdf->Cell(0,10,'MONTHLY REPORT',0,0,'C');
        $this->fpdf->Ln();
        $this->fpdf->Cell(50,10,"FROM : $user",0,0,'C');
        $this->fpdf->Cell(0,10,'TO :',0,0,'C');
        $this->fpdf->Ln();
        $this->fpdf->Ln();
        $this->fpdf->Cell(20,10,'DATE :',0,0,'C');
        // $this->fpdf->Cell(0,10,'Cc :',0,0,'C');
        $this->fpdf->Ln();

        $this->fpdf->Cell(65,8,'Activity',1);
        $this->fpdf->Cell(50,8,'Deadline',1);
        $this->fpdf->Cell(50,8,'Progress',1);
        $this->fpdf->Cell(20,8,'Status',1);
        $this->fpdf->Cell(100,8,'Output',1);

        $this->fpdf->Ln();

        foreach($todos as $todo){
            if($todo->complited == 1){
                $status = 'Completed';
            }else{
                $status = 'Pending';
            }
            $this->fpdf->Cell(65,8,"$todo->title",1);
            $this->fpdf->Cell(50,8,"$todo->deadline",1);
            $this->fpdf->Cell(50,8,"$todo->progress",1);
            $this->fpdf->Cell(20,8,"$status",1);
            $this->fpdf->Cell(100,8,"$todo->output",1);
            $this->fpdf->Ln();
            // $this->fpdf->MultiCell(100,5,$todo->output, 1,);
        }
        
        
        $this->fpdf->Ln();
        $this->fpdf->Ln();
        $this->fpdf->Cell(20,10,'Signed :',0,0,'C');
        $this->fpdf->Cell(0,10,'Date :',0,0,'C');
        $this->fpdf->Ln();
        $this->fpdf->Output();
        exit;
    }
}
