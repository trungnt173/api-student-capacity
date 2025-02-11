<?php

namespace App\Services\Modules\MSemeter;

use App\Models\poetry as modelPoetry;
use App\Models\semeter as SemeterModel;
use App\Models\studentPoetry;

class Semeter implements MSemeterInterface
{

    public function __construct(private SemeterModel $modelSemeter, private modelPoetry $modelPoetry)
    {

    }

    public function ListSemeter()
    {
        return $this->modelSemeter::all();
    }

    public function GetSemeter()
    {
        return $this->getList()->paginate(5);
    }

    public function getList()
    {
        $dataQuery = $this->modelSemeter::query();

        if (!(auth()->user()->hasRole('super admin'))) {
            $dataQuery->where('id_campus', auth()->user()->campus_id);
        }

        if (auth()->user()->hasRole('teacher')) {
            $dataQuery->with('blocks');
        }

        return $dataQuery->orderBy('id', 'desc');
    }

    public function getAllSemeter()
    {
        $dataQuery = $this->modelSemeter::query();
        if (!(auth()->user()->hasRole('super admin'))) {
            $dataQuery->where('id_campus', auth()->user()->campus_id);
        }
        $data = $dataQuery->get();
        return $data;
    }

    public function GetSemeterAPI($codeCampus)
    {
        $semesterAndCount = studentPoetry::query()
            ->selectRaw('poetry.id_semeter, count(poetry.id_semeter) as total_poetry')
            ->join('poetry', 'poetry.id', '=', 'student_poetry.id_poetry')
            ->where('poetry.exam_date', date('Y-m-d'))
            ->groupBy(['poetry.id_semeter', 'student_poetry.id_student'])
            ->pluck('total_poetry', 'id_semeter');

        $data = $this->modelSemeter
//            ->where('id_campus', $codeCampus)
            ->whereIn('id', $semesterAndCount->keys()->toArray())
            ->get();
        foreach ($data as $value) {
            $value['total_poetry'] = $semesterAndCount[$value->id] ?? 0;
        }

        return $data;
    }

    public function getItemSemeter($id)
    {
        try {
            return $this->modelSemeter->find($id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getName($id)
    {
        try {
            return $this->modelSemeter->find($id)->name;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getListByCampus($id_campus)
    {
        try {
            return $this->modelSemeter->where('id_campus', $id_campus)->get();
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function find($id)
    {
        try {
            return $this->modelSemeter->find($id);
        } catch (\Exception $e) {
            return false;
        }
    }

}
