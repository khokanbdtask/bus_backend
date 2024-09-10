<?php

namespace Modules\Fitness\Models;

use CodeIgniter\Model;

class FitnessModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'fitnesses';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = true;
	protected $protectFields        = true;
	protected $allowedFields 		= 	[
											'id', 'vehicle_id', 'fitness_name', 'start_date', 'end_date', 
											'start_milage', 'end_milage','total_milage', 'tire_condition', 'windshield_washer_condition', 
											'windshield_condition', 'wiper_condition', 'overall_car_condition','driver_id', 'remarks','subtrip_id'
										];
	

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];
}
