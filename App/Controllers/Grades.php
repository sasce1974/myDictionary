<?php


namespace App\Controllers;


use Core\Controller;
use App\Models\Grade;

class Grades extends Controller
{
    public function create(){
        $grade = filter_input(INPUT_POST, 'grade',
            FILTER_SANITIZE_NUMBER_INT,
            ['min'=>5, 'max'=>10]);
        $student_id = filter_input(INPUT_POST, 'student_id',
            FILTER_SANITIZE_NUMBER_INT,
            ['min'=>1]);

        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
        if($token !== $_SESSION['token']){
            $_SESSION['error'] = "Token mismatch";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit(403);
            //throw new \Exception('Token Error');
        }
        if(count(Grade::studentGrades($student_id)) > 3){
            $_SESSION['error'] = 'There are already 4 grades for this student';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit(403);
        }

        if(!empty($grade) && !empty($student_id)){

            $r = Grade::create($student_id, $grade);
            if($r){
                $_SESSION['message'] = "Grade Inserted";
            }else{
                $_SESSION['error'] = 'Grade not inserted';
            }

        }else{
            $_SESSION['error'] = 'Please insert Grade';
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    public function destroy(){
        $grade_id = filter_input(INPUT_POST, 'grade_id',
            FILTER_SANITIZE_NUMBER_INT,
            ['min'=>1]);
        $student_id = filter_input(INPUT_POST, 'student_id',
            FILTER_SANITIZE_NUMBER_INT,
            ['min'=>1]);

        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
        if($token !== $_SESSION['token']){
            $_SESSION['error'] = "Token mismatch";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit(403);
        }

        if(!empty($grade_id) && !empty($student_id)){
            $r = Grade::delete($grade_id, $student_id);

            if($r){
                $_SESSION['message'] = "Grade Deleted";
            }else{
                $_SESSION['error'] ='Grade not deleted';
            }
        }else{
            $_SESSION['error'] ='Student ID or Grade ID not provided';
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    public function exportAction(){
        $id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);
        print htmlspecialchars((string)Grade::export($id));//print with htmlspecialchars to get view tags in xml
    }
}