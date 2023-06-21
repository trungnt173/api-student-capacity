<?php

namespace App\Services\Modules\poetry;

use App\Models\poetry as modelPoetry;

class poetry implements MPoetryInterface
{
    public function __construct(
        private modelPoetry $modelPoetry
    )
    {
    }

    public function ListPoetry($id,$idblock){
        try {
            $records = $this->modelPoetry->where('id_semeter',$id)
                ->whereHas('subject',function($q) use ($idblock){
                    $q->where('id_block',$idblock);
                })->paginate(10);
            return $records;
        }catch (\Exception $e) {
            return false;
        }
    }

    public function ListPoetryRespone($idSubject)
    {
        try {
            $records = $this->modelPoetry->where('id_subject', $idSubject)->get();
            $records->load(['classsubject' => function ($q) {
                return $q->select('id', 'name', 'code_class');
            }]);
            return $records;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function ListPoetryDetail($idSemeter,$idBlock,$id_subject,$id_class){
        try {
            $records = $this->modelPoetry->when(!empty($idSemeter), function ($query) use ($idSemeter) {
                $query->where('id_semeter', $idSemeter);
            })
                ->when(!empty($idBlock), function ($query) use ($idBlock) {
                    $query->whereHas('subject', function ($subQuery) use ($idBlock) {
                        $subQuery->where('id_block', $idBlock);
                    });
                })
                ->when(!empty($id_subject) && empty($id_class), function ($query) use ($id_subject) {
                    $query->where('id_subject', $id_subject);
                })
                ->when(!empty($id_subject) && !empty($id_class), function ($query) use ($id_subject, $id_class) {
                    $query->where('id_subject', $id_subject)
                        ->where('id_class', $id_class);
                })
                ->get();
            $records->load(['classsubject'  => function ($q) {
                return $q->select('id','name','code_class');
            }]);
            $records->load(['subject' => function ($q) {
                return $q->select('subject.id','subject.name');
            }]);
            $records->load(['subject.block' => function ($q) {
                return $q->select('id','name');
            }]);
            $records->load(['examination' => function ($q) {
                return $q->select('id', 'name');
            }]);
            $records->load(['semeter' => function ($q) {
                return $q->select('id', 'name');
            }]);
            return $records;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function ListPoetryApi($id, $id_user)
    {
        try {
            $records = $this->modelPoetry
                ->query()
                ->select(
                    'poetry.id',
                    'poetry.id_subject',
                    'poetry.start_time',
                    'poetry.end_time',
                    'poetry.end_time',
                    'semester.name as name_semeter',
                    'subject.name as name_subject',
                    'class.name as name_class',
                    'examination.name as name_examination'
                )
                ->join('semester', 'semester.id', '=', 'poetry.id_semeter')
                ->join('subject', 'subject.id', '=', 'poetry.id_subject')
                ->join('class', 'class.id', '=', 'poetry.id_class')
                ->join('examination', 'examination.id', '=', 'poetry.id_examination')
                ->join('student_poetry', 'student_poetry.id_poetry', '=', 'poetry.id')
                ->join('playtopic', 'playtopic.student_poetry_id', '=', 'student_poetry.id')
                ->where('poetry.id_semeter', $id)
                ->where('student_poetry.id_student', $id_user)
                ->where('playtopic.has_received_exam', 1)
                ->get();
            $data['name_item'] = $records[0]->name_semeter;
            foreach ($records as $value) {
//                if ($value->playtopic === null) {
//                    continue;
//                }

                $data['data'][] = [
                    "id" => $value->id,
                    "id_subject" => $value->id_subject,
                    "name_semeter" => $value->name_semeter,
                    "name_subject" => $value->name_subject,
                    "name_class" => $value->name_class,
                    "name_examtion" => $value->name_examination,
                    "start_time" => $value->start_time,
                    "end_time" => $value->end_time,
                ];
            }
            return $data;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function onePoetryApi($id_poetry)
    {
        try {
            $records = $this->modelPoetry::select('start_time', 'end_time')->find($id_poetry);
            return $records;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getItem($id)
    {
        try {
            $poetry = $this->modelPoetry::find($id);
            $data = ['name_semeter' => $poetry->semeter->name, 'name_subject' => $poetry->subject->name, 'nameClass' => $poetry->classsubject->name, 'nameExamtion' => $poetry->examination->name, 'name_campus' => $poetry->campus->name];
            return $data;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getItempoetry($id)
    {
        {
            try {
                return $this->modelPoetry->find($id);
            } catch (\Exception $e) {
                return false;
            }
        }
    }
}
