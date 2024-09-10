<?php

namespace App\Controllers;

use App\Libraries\Chart;
use Modules\Trip\Models\TripModel;
use Modules\Employee\Models\EmployeeModel;
use Modules\Trip\Models\StuffassignModel;



class DriverHome extends BaseController
{
    protected $tripModel;
    protected $employeeModel;
    protected $stuffassignModel;


    public function __construct()
    {
        $this->tripModel = new TripModel();
        $this->employeeModel = new EmployeeModel();
        $this->stuffassignModel = new StuffassignModel();  
    }

    public function index()
    {
        return redirect()->route('driver-home');
    }

    public function driver()
    {
        $charrLibrary = new Chart();
        $todaytrip = $charrLibrary->totalTripToday();
        $totalBooking = $charrLibrary->totalBooking();
        $totalMoney = $charrLibrary->totalMoney();
        $totalPassanger = $charrLibrary->totalPassanger();

        $data['trip'] = $this->tripModel->select('trips.id as tripid,trips.*,fleets.*,schedules.*,vehicles.*')
            ->join('fleets', 'fleets.id = trips.fleet_id')
            ->join('schedules', 'schedules.id = trips.schedule_id')
            ->join('vehicles', 'vehicles.id = trips.vehicle_id')
            ->orderBy('trips.id', 'desc')
            ->findAll();
        // dd($this->session->get('employee_id'));
        //employee id
        if ($this->session->get('employee_id') != null) {
            $data['get_employee_type'] = $this->employeeModel->where('id', $this->session->get('employee_id'))->first();
        } else {
            $data['get_employee_type'] = null;
        }
        if ($data['get_employee_type'] != null) {
            $data['employee_type'] = $data['get_employee_type']->employeetype_id;
        } else {
            $data['employee_type'] = null;
        }
        $current_date = date('Y-m-d');
        if ($data['employee_type'] == 1) {
            $data['driverAssignedListToday'] = $this->stuffassignModel
            ->select('stuffassigns.*,trips.*,l1.name as pickup_location_name,l2.name as drop_location_name')
            ->join('trips', 'trips.id = stuffassigns.trip_id')
            ->join('locations l1', 'trips.pick_location_id = l1.id', 'left')
            ->join('locations l2', 'trips.drop_location_id = l2.id', 'left')
            ->where('employee_type', 1)
            ->where('employee_id', $this->session->get('employee_id'))
            ->where("DATE(start_date) <= '$current_date'")
            ->where("DATE(end_date) >= '$current_date'")
            ->where('is_approved', 1)
            ->findAll();
        }else{
            $data['driverAssignedListToday'] = [];
        }
        // dd($data['driverAssignedListToday']);
        $data['todaytrip'] = $todaytrip;
        $data['todaybooking'] = $totalBooking;
        $data['totalmoney'] = $totalMoney;
        $data['totalpassanger'] = $totalPassanger;

        $data['module'] =    lang("Localize.dashboard");
        $data['title']  =    lang("Localize.dashboard");

        return view('template/admin/driver_welcome', $data);
    }
}
