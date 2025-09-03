<?php 
// src/Service/ImageUploader.php
namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Question;
use App\Entity\Proposition;
use App\Entity\ExamPaper;

use Symfony\Component\HttpFoundation\File\File;

class PDFImporter
{
    
    public static function extract($fullPath, $io = null) {
        $_questions = array();
        $_status = false;

        // Parse PDF file and build necessary objects.
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($fullPath);

        $images = $pdf->getObjectsByType('XObject');

        // IMAGES IN PDF
        // $io->info(count($images));
        // // $io->info(print_r($pdf->getPages()[0]->getDataTm()));
        // foreach( $images as $image ) {
        //     $io->info(print_r($image->getDetails()));
        // }

        $text = $pdf->getText();
        $text = str_replace('\\r\\n', ' ', $text);
        $text = str_replace('https://www.gratisexam.com/', '', $text);

        $questionArray = preg_split("/QUESTION \d*/", $text, -1);
        array_splice($questionArray, 0, 1);
        
        $remove = false;
        foreach ($questionArray as $questionFull) {
            $question = new Question();
            $question->setTitle("QUESTION");

            $propositions =  preg_split("/\s\w?\./m", $questionFull, -1);
            if (count($propositions) > 1) {
                $question->setTask($propositions[0]);
                array_splice($propositions, 0, 1);  // remove task 

                $last_answer = $propositions[count($propositions)-1];
    
                preg_match("/^.*$/m", $last_answer, $getLast); // get last
                array_splice($propositions, count($propositions)-1, 1, $getLast);  // put last 
                
                preg_match("/Correct Answer:\s*(?P<word>\w+)$/m", $last_answer, $correct_answers_full);
                
                $correct_answers = array();
                if (isset($correct_answers_full["word"])) {
                    $word = $correct_answers_full["word"];
                    
                    $strlen = strlen($word);
                    for($i = 0; $i < $strlen; ++$i) {
                        $correct_answers[self::toNum($word[$i])] = true;
                    }
                }
    
                foreach ($propositions as $kA => $propositionFull) {
                    $propositionFull = substr_replace($propositionFull, "", -1);
    
                    if (strlen($propositionFull) != 0) {
                        $proposition = new Proposition();
                        $proposition->setProposition($propositionFull);
                        $question->addProposition($proposition);
    
                        if (isset($correct_answers[$kA])) {
                            $proposition->setisCorrect(true);
                        }
                    } else if ($kA == 0) {
                        $remove = true;
                    }
                }
            } else {
                $remove = true;
            }
            
            
            if ($remove == false) {
                array_push($_questions, $question);
            } else {
                $remove = false;
            }
        }

        $count = count($_questions);
        if ($count == 0) {
            $_status = "error";
            unlink($fullPath);
        } else {
            $_status = "success";
        }

        return array("questions" => $_questions, "status" => $_status, "count" => $count);
    }

    public static function import($entityManager, ExamPaper $examPaper, string $fullPath, $io = null) {
        $_return = self::extract($fullPath, $io);

        if ($_return["status"] == "success") {
            $_questions = $_return["questions"];
            foreach ($_questions as $question) {
                $question->setExamPaper($examPaper);

                foreach ($question->getPropositions() as $proposition) {
                    $entityManager->persist($proposition);
                }

                $entityManager->persist($question);
            }

            $_message = "Successfully imported file. Found ". $_return["count"]." questions. Please check for inconsistencies.";
        } else {
            $_message = "File importing failed. Found 0 questions.";
        }

        return array($_message, $_return["status"]);
    }

    private static function toNum($data) {
        $alphabet = array( 
            'A', 'B', 'C', 'D', 'E',
            'F', 'G', 'H', 'I', 'J',
            'K', 'L', 'M', 'N', 'O',
            'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y',
            'Z');

        $alpha_flip = array_flip($alphabet);
        $return_value = -1;
        $length = strlen($data);
        for ($i = 0; $i < $length; $i++) {
            $return_value +=
                ($alpha_flip[$data[$i]] + 1) * pow(26, ($length - $i - 1));
        }
        return $return_value;
    }
}