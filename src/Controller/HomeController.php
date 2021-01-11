<?php

namespace App\Controller;

use App\Form\LoadType;
use App\Entity\Student;
use App\Form\LoadCsvType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/loadxls", name="home_loadxls")
     */
    public function loadxls(Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(LoadType::class);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            /** on récupère le fichier du formulaire */
            $donnee = $form->getData();
            $fichier = $donnee['Chargement'];
            /** identifie le type de $fichier  **/
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($fichier);
            /**  cré un new Reader du type de fichier identifié  **/
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            /**  charge $fichier dans le Spreadsheet Object (feuille 1) **/
            $spreadsheet = $reader->load($fichier)->getSheet(0);
            /** dernière colonne en hexa */
            $getHighestColumn = $spreadsheet->getHighestColumn();
            /** nombre de lignes */
            $getHighestRow = $spreadsheet->getHighestRow();
            /** init colonne */
            $alpha='A';
            
            for($i=1; $i<=$getHighestRow; $i++) { 
                /** on passe l'entête */
                if($i > 1) {
                    $student = new Student();
                    $student->setname($spreadsheet->getCell($alpha.$i)->getValue());
                    ++$alpha;
                    $student->setNoteMath($spreadsheet->getCell($alpha.$i)->getValue());
                    ++$alpha;
                    $student->setNoteFrancais($spreadsheet->getCell($alpha.$i)->getValue());
                    $manager->persist($student); 
                } 
                if($alpha == $getHighestColumn)
                    $alpha = "A";    
            }                             
            $manager->flush();

            return $this->redirectToRoute('student_index');
        }
        return $this->render('home/loadxls.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/loadcsv", name="home_loadcsv")
     */
    public function loadcsv(Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(LoadCsvType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $donnee = $form->getData();
            $fichier = $donnee['Chargement'];
            $filetype = ucfirst(substr(strrchr($fichier, "."), 1));
            $tab = array();
            if($filetype == "Csv") {                    
                    $row = 1;
                    if (($handle = fopen($fichier, "r")) !== FALSE) {
                        while (($data = fgetcsv($handle, 1000, ";", "'")) !== FALSE) {
                            $num = count($data);
                            $row++;
                            for ($c=0; $c < $num; $c++) {
                                $tab[] = $data[$c];
                            }
                        }
                        fclose($handle);
                         
                        for ($i=0; $i < $num; $i++) { 
                            array_shift($tab);
                        }
                        $row -= 2;
                    }
                    $nbItems = count($tab);
                    $ctr = 0;
                    for ($i=0; $i < $row; $i++) {     
                        if($ctr > $nbItems) 
                            exit;          
                        $student = new Student();
                        $tab[$ctr] = str_replace('"', "", $tab[$ctr]);
                        $student->setName($tab[$ctr]);
                        $ctr++;
                        $student->setNoteMath($tab[$ctr]);          
                        $ctr++;
                        $tab[$ctr] = str_replace('"', "", $tab[$ctr]);
                        $student->setNoteFrancais($tab[$ctr]);                  
                        $ctr++;  
                        $manager->persist($student);        
                    }
                    $manager->flush();   
            }
            return $this->redirectToRoute('student_index');
        }
        return $this->render('home/loadcsv.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
