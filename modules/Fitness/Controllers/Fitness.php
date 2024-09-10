<?php

namespace Modules\Fitness\Controllers;

use App\Controllers\BaseController;
use Modules\Fleet\Models\VehicleModel;
use Modules\Fitness\Models\FitnessModel;
use App\Libraries\Rolepermission;
use Modules\Employee\Models\EmployeeModel;
use Modules\Trip\Models\SubtripModel;
use Modules\Location\Models\LocationModel;

class Fitness extends BaseController
{
	protected $Viewpath;

	protected $vehicleModel;
	protected $fitnessModel;
	protected $employeeModel;
	protected $subtripModel;
	protected $locationModel;

	public function __construct()
	{

		$this->Viewpath = "Modules\Fitness\Views";
		$this->vehicleModel = new VehicleModel();
		$this->fitnessModel = new FitnessModel();
		$this->employeeModel = new EmployeeModel();
		$this->subtripModel = new SubtripModel();
		$this->locationModel = new LocationModel();
	}

	public function new()
	{
		$vehicle = $this->vehicleModel->where('status', 1)->findAll();
		$data['vehicle'] = $vehicle;

		$data['tripGroups'] = $this->subtripModel
			->select('subtrips.id')
			->withLocations()
			->active()
			->getGroup();


		$data['module'] =    lang("Localize.fitness");
		$data['title']  =    lang("Localize.add_fitness");
		$data['driver'] = $this->employeeModel->where('employeetype_id', 1)->findAll();
		$data['pageheading'] = lang("Localize.add_fitness");
		if (session()->get('role_id') == 4) {
			$data['driver_id'] = session()->get('employee_id');
		} else {
			$data['driver_id'] = '';
		}

		echo view($this->Viewpath . '\fitness/new', $data);
	}


	public function create()
	{

		$data = array(
			"fitness_name" => $this->request->getVar('fitness_name'),
			"vehicle_id" => $this->request->getVar('vehicle_id'),
			"start_date" => $this->request->getVar('start_date'),
			"end_date" => $this->request->getVar('end_date'),
			"start_milage" => $this->request->getVar('start_milage'),
			"end_milage" => $this->request->getVar('end_milage'),
			"total_milage" => $this->request->getVar('total_milage'),
			"tire_condition" => $this->request->getVar('tire_condition'),
			"windshield_washer_condition" => $this->request->getVar('windshield_washer_condition'),
			"windshield_condition" => $this->request->getVar('windshield_condition'),
			"wiper_condition" => $this->request->getVar('wiper_condition'),
			"overall_car_condition" => $this->request->getVar('overall_car_condition'),
			"driver_id" => $this->request->getVar('driver_id'),
			"subtrip_id" => $this->request->getVar('subtrip_id'),
			"remarks" => $this->request->getVar('remarks')
		);

		if ($this->validation->run($data, 'fitness')) {
			$this->fitnessModel->insert($data);

			return redirect()->route('index-fitness')->with("success", "Data Save");
		} else {
			$vehicle = $this->vehicleModel->where('status', 1)->findAll();
			$data['vehicle'] = $vehicle;
			$data['validation'] = $this->validation;

			$data['module'] =    lang("Localize.fitness");
			$data['title']  =    lang("Localize.fitness_list");

			$data['pageheading'] = lang("Localize.add_fitness");

			echo view($this->Viewpath . '\fitness/new', $data);
		}
	}

	public function index()
	{
		$subtrip = $this->subtripModel->withDeleted()->where('status', 1)->findAll();
		$locationname = $this->locationModel->withDeleted()->findAll();

		foreach ($subtrip as $skey => $subtripvalue) {
			foreach ($locationname as $lkey => $locationvalue) {

				if ($subtripvalue->pick_location_id == $locationvalue->id) {

					$subtrip[$skey]->picklocation = $locationvalue->name;
				}
				if ($subtripvalue->drop_location_id == $locationvalue->id) {
					$subtrip[$skey]->droplocation = $locationvalue->name;
				}
			}
		}
		$fitness = $this->fitnessModel
			->select('fitnesses.*, CONCAT_WS(" ", employees.first_name, employees.last_name) AS driver_name')
			->join('employees', 'fitnesses.driver_id = employees.id')
			->where('fitnesses.deleted_at', null) // Exclude soft-deleted records
			->orderBy('fitnesses.id', 'DESC');
		// ->getResult();


		if (session()->get('role_id') == 4) {
			$fitness = $fitness->where('driver_id', session()->get('employee_id'))->get()->getResult();
		} else {
			$fitness = $fitness->get()->getResult();
		}

		$subtirpName = $subtrip;
		foreach ($fitness as $ckey => $cvalue) {
			
			foreach ($subtirpName as $skey => $svalue) {

				if ($cvalue->subtrip_id == $svalue->id) {
					$fitness[$ckey]->subtrip_name = $svalue->picklocation . '--' . $svalue->droplocation;
				}
			}
		}

		$vehicle = $this->vehicleModel->withDeleted()->where('status', 1)->findAll();

		foreach ($fitness as $fkey => $fvalue) {

			foreach ($vehicle as $vkey => $kvalue) {

				if ($kvalue->id == $fvalue->vehicle_id) {

					$fitness[$fkey]->regno = $kvalue->reg_no;
				}
			}
		}

		$data['fitness'] = $fitness;
		$data['vehicle'] = $vehicle;

		$data['module'] =    lang("Localize.fitness");
		$data['title']  =    lang("Localize.fitness_list");

		$data['pageheading'] = lang("Localize.fitness_list");

		$rolepermissionLibrary = new Rolepermission();
		$add_data = "add_fitness";
		$list_data = "fitness_list";

		$data['add_data'] = $rolepermissionLibrary->create($add_data);
		$data['edit_data'] = $rolepermissionLibrary->edit($list_data);
		$data['delete_data'] = $rolepermissionLibrary->delete($list_data);

		echo view($this->Viewpath . '\fitness/index', $data);
	}


	public function edit($id)
	{
		$data['vehicle'] = $this->vehicleModel->where('status', 1)->findAll();
		$data['fitness'] = $this->fitnessModel->find($id);
		$data['driver'] = $this->employeeModel->where('employeetype_id', 1)->findAll();
		$data['module'] =    lang("Localize.fitness");
		$data['title']  =    lang("Localize.fitness_list");
		$subtrip = $this->subtripModel
			->select('subtrips.id, l1.name AS picklocation, l2.name AS droplocation')
			->join('locations l1', 'subtrips.pick_location_id = l1.id')
			->join('locations l2', 'subtrips.drop_location_id = l2.id')
			->where('l1.deleted_at IS NULL')
			->where('l2.deleted_at IS NULL')
			->where('status', 1)
			->findAll();

		$data['subtrip'] = $subtrip;

		if (session()->get('role_id') == 4) {
			$data['driver_id'] = session()->get('employee_id');
		} else {
			$data['driver_id'] = '';
		}

		$heading = lang("Localize.edit") . ' ' . lang("Localize.fitness");
		$data['pageheading'] = $heading;

		echo view($this->Viewpath . '\fitness/edit', $data);
	}



	public function update($id)
	{

		$validdata = array(
			"fitness_name" => $this->request->getVar('fitness_name'),
			"vehicle_id" => $this->request->getVar('vehicle_id'),
			"start_date" => $this->request->getVar('start_date'),
			"end_date" => $this->request->getVar('end_date'),
			"start_milage" => $this->request->getVar('start_milage'),
			"end_milage" => $this->request->getVar('end_milage'),
			"total_milage" => $this->request->getVar('total_milage'),
			"subtrip_id" => $this->request->getVar('subtrip_id'),
		);
		$data = array(
			"id" => $id,
			"fitness_name" => $this->request->getVar('fitness_name'),
			"vehicle_id" => $this->request->getVar('vehicle_id'),
			"start_date" => $this->request->getVar('start_date'),
			"end_date" => $this->request->getVar('end_date'),
			"start_milage" => $this->request->getVar('start_milage'),
			"end_milage" => $this->request->getVar('end_milage'),
			"total_milage" => $this->request->getVar('total_milage'),
			"tire_condition" => $this->request->getVar('tire_condition'),
			"windshield_washer_condition" => $this->request->getVar('windshield_washer_condition'),
			"windshield_condition" => $this->request->getVar('windshield_condition'),
			"wiper_condition" => $this->request->getVar('wiper_condition'),
			"overall_car_condition" => $this->request->getVar('overall_car_condition'),
			"driver_id" => $this->request->getVar('driver_id'),
			"subtrip_id" => $this->request->getVar('subtrip_id'),
			"remarks" => $this->request->getVar('remarks')
		);

		if ($this->validation->run($validdata, 'fitness')) {
			$this->fitnessModel->save($data);
			return redirect()->route('index-fitness')->with("success", "Data Update");
		} else {

			$data['validation'] = $this->validation;
			$data['vehicle'] = $this->vehicleModel->where('status', 1)->findAll();
			$data['fitness'] = $this->fitnessModel->find($id);
			$data['driver'] = $this->employeeModel->where('employeetype_id', 1)->findAll();
			$subtrip = $this->subtripModel
				->select('subtrips.id, l1.name AS picklocation, l2.name AS droplocation')
				->join('locations l1', 'subtrips.pick_location_id = l1.id')
				->join('locations l2', 'subtrips.drop_location_id = l2.id')
				->where('l1.deleted_at IS NULL')
				->where('l2.deleted_at IS NULL')
				->where('status', 1)
				->findAll();

			$data['subtrip'] = $subtrip;

			$data['module'] =    lang("Localize.fitness");
			$data['title']  =    lang("Localize.fitness_list");

			echo view($this->Viewpath . '\fitness/edit', $data);
		}
	}


	public function delete($id)
	{
		$this->fitnessModel->delete($id);
		return redirect()->route('index-fitness')->with("fail", "Data Deleted");
	}
}
