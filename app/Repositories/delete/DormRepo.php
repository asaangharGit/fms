<?php

namespace App\Repositories;

use App\Models\ActivityLogs;

class DormRepo
{

    public function create($data)
    {
        return ActivityLogs::create($data);
    }

    public function getAll($order = 'name')
    {
        return ActivityLogs::orderBy($order)->get();
    }

    public function getDorm($data)
    {
        return ActivityLogs::where($data)->get();
    }

    public function update($id, $data)
    {
        return ActivityLogs::find($id)->update($data);
    }

    public function find($id)
    {
        return ActivityLogs::find($id);
    }


}
