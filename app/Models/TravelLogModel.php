<?php

namespace App\Models;

use CodeIgniter\Model;

class TravelLogModel extends Model
{
    protected $table            = 'travel_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'travel_id',
        'user_id',
        'action',
        'detail',
    ];

    protected $useTimestamps = false;

    public function log($travelId, $userId, $action, $detail = null)
    {
        return $this->insert([
            'travel_id'  => $travelId,
            'user_id'    => $userId,
            'action'     => $action,
            'detail'     => $detail,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getLogsForTravel($travelId)
    {
        return $this->select('travel_logs.*, users.prefix, users.first_name, users.last_name, positions.name as position_name')
                    ->join('users', 'users.id = travel_logs.user_id', 'left')
                    ->join('positions', 'positions.id = users.position_id', 'left')
                    ->where('travel_logs.travel_id', $travelId)
                    ->orderBy('travel_logs.id', 'ASC')
                    ->findAll();
    }
}
