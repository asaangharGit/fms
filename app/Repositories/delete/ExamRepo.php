<?php

namespace App\Repositories;

use App\Models\LoanBorrowerDocs;
use App\Models\ExamRecord;
use App\Models\ActivityLogins;
use App\Models\Mark;
use App\Models\Skill;

class ExamRepo
{

    public function all()
    {
        return LoanBorrowerDocs::orderBy('name', 'asc')->orderBy('year', 'desc')->get();
    }

    public function getExam($data)
    {
        return LoanBorrowerDocs::where($data)->get();
    }

    public function find($id)
    {
        return LoanBorrowerDocs::find($id);
    }

    public function create($data)
    {
        return LoanBorrowerDocs::create($data);
    }

    public function createRecord($data)
    {
        return ExamRecord::firstOrCreate($data);
    }

    public function update($id, $data)
    {
        return LoanBorrowerDocs::find($id)->update($data);
    }

    public function updateRecord($where, $data)
    {
        return ExamRecord::where($where)->update($data);
    }

    public function getRecord($data)
    {
        return ExamRecord::where($data)->get();
    }

    public function findRecord($id)
    {
        return ExamRecord::find($id);
    }

    public function delete($id)
    {
        return LoanBorrowerDocs::destroy($id);
    }

    /*********** Grades ***************/

    public function allGrades()
    {
        return ActivityLogins::orderBy('name')->get();
    }

    public function getGrade($data)
    {
        return ActivityLogins::where($data)->get();
    }

    public function findGrade($id)
    {
        return ActivityLogins::find($id);
    }

    public function createGrade($data)
    {
        return ActivityLogins::create($data);
    }

    public function updateGrade($id, $data)
    {
        return ActivityLogins::find($id)->update($data);
    }

    public function deleteGrade($id)
    {
        return ActivityLogins::destroy($id);
    }

    /*********** Marks ***************/

    public function createMark($data)
    {
        return Mark::firstOrCreate($data);
    }

    public function destroyMark($id)
    {
        return Mark::destroy($id);
    }

    public function updateMark($id, $data)
    {
        return Mark::find($id)->update($data);
    }

    public function getExamYears($student_id)
    {
        return Mark::where('student_id', $student_id)->select('year')->distinct()->get();
    }

    public function getMark($data)
    {
        return Mark::where($data)->with('grade')->get();
    }

    /*********** Skills ***************/

    public function getSkill($where)
    {
        return Skill::where($where)->orderBy('name')->get();
    }

    public function getSkillByClassType($class_type = NULL, $skill_type = NULL)
    {
        return ($skill_type)
            ? $this->getSkill(['class_type' => $class_type, 'skill_type' => $skill_type])
            : $this->getSkill(['class_type' => $class_type]);
    }

}
