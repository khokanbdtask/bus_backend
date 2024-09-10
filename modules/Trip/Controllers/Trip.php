<?php

namespace Modules\Trip\Controllers;



use App\Controllers\BaseController;
use Modules\Trip\Models\TripModel;
use Modules\Trip\Models\StuffassignModel;
use Modules\Trip\Models\SubtripModel;
use Modules\Trip\Models\PickdropModel;
use Modules\Trip\Models\FacilityModel;
use Modules\Location\Models\LocationModel;
use Modules\Employee\Models\EmployeeModel;
use Modules\Fleet\Models\FleetModel;
use Modules\Fleet\Models\VehicleModel;
use Modules\Schedule\Models\ScheduleModel;
use Modules\Location\Models\StandModel;
use App\Libraries\Rolepermission;
use Modules\Luggage\Models\LuggagesettingModel;
use Modules\Website\Models\WebsettingModel;

class Trip extends BaseController
{
    protected $Viewpath;
    protected $tripModel;
    protected $subtripModel;
    protected $stuffassignModel;
    protected $locationModel;
    protected $employeeModel;
    protected $fleetTypeModel;
    protected $scheduleeModel;
    protected $vehicleModel;
    protected $standModel;
    protected $picdropModel;
    protected $facilitypModel;
    protected $luggageSettingModel;
    protected $webSettingModel;
    protected $db;

    public function __construct()
    {

        $this->Viewpath = "Modules\Trip\Views";
        $this->tripModel = new TripModel();
        $this->subtripModel = new SubtripModel();
        $this->stuffassignModel = new StuffassignModel();
        $this->locationModel = new LocationModel();
        $this->employeeModel = new EmployeeModel();
        $this->fleetTypeModel = new FleetModel();
        $this->vehicleModel = new VehicleModel();
        $this->scheduleeModel = new ScheduleModel();
        $this->standModel = new StandModel();
        $this->picdropModel = new PickdropModel();
        $this->facilitypModel = new FacilityModel();
        $this->luggageSettingModel = new LuggagesettingModel();
        $this->webSettingModel = new WebsettingModel();
        $this->db      = \Config\Database::connect();
    }
    public function index()
    {
        $data['trip'] = $this->tripModel->select('trips.id as tripid,trips.*,fleets.*,schedules.*,vehicles.*')
            ->join('fleets', 'fleets.id = trips.fleet_id')
            ->join('schedules', 'schedules.id = trips.schedule_id')
            ->join('vehicles', 'vehicles.id = trips.vehicle_id')
            ->orderBy('trips.id', 'DESC')
            ->findAll();

        $data['module'] =    lang("Localize.trip");
        $data['title']  =    lang("Localize.trip_list");

        $data['pageheading'] = lang("Localize.trip_list");

        $rolepermissionLibrary = new Rolepermission();

        $add_data = "add_trip";
        $list_data = "trip_list";

        $data['add_data'] = $rolepermissionLibrary->create($add_data);
        $data['edit_data'] = $rolepermissionLibrary->edit($list_data);
        $data['delete_data'] = $rolepermissionLibrary->delete($list_data);

        echo view($this->Viewpath . '\trip/index', $data);
    }

    public function new()
    {
        $weekday = array(
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thursday',
            '5' => 'Friday',
            '6' => 'Saturday',
            '7' => 'Sunday',
        );
        $data['weekday'] =  $weekday;
        $data['location'] = $this->locationModel->findAll();
        $data['schedule'] = $this->scheduleeModel->findAll();
        $data['fleet_type'] = $this->fleetTypeModel->findAll();
        // $data['driver'] = $this->employeeModel->where('employeetype_id', 1)->findAll();
        $data['assistant'] = $this->employeeModel->where('employeetype_id', 2)->findAll();
        $data['stand'] = $this->standModel->findAll();
        $data['facility'] = $this->facilitypModel->findAll();
        $data['luggagesetting']    = $this->luggageSettingModel->first();
        // dd($data['webseeting']);
        $data['webseeting']    = $this->webSettingModel->first();

        $data['module'] =    lang("Localize.trip");
        $data['title']  =    lang("Localize.add_trip");

        $data['pageheading'] = lang("Localize.add_trip");

        echo view($this->Viewpath . '\trip/new', $data);
    }

    public function create()
    {
        $picupdata = array();
        $dropdata = array();

        $pickLocation = $this->request->getVar('pick_location_id');
        $dropLocation = $this->request->getVar('drop_location_id');
        $busStandTime = $this->request->getVar('picktime');
        $busStandlocation = $this->request->getVar('picstand');
        $standDetails = $this->request->getVar('detail');
        $busStandtype = $this->request->getVar('type');
        $dropbusStandTime = $this->request->getVar('droptime');
        $dropbusStandlocation = $this->request->getVar('dropstand');
        $dropstandDetails = $this->request->getVar('dropdetail');
        $dropbusStandtype = $this->request->getVar('droptype');
        // $driver = array_filter($this->request->getVar('driver'));
        if (!empty($this->request->getVar('assistant'))) {
            $assistant = array_filter($this->request->getVar('assistant'));
        }

        $weekend = $this->request->getVar('weekend');
        if (!empty($weekend)) {
            $weekend = implode(",", $weekend);
        }

        $stoppage = array_merge([$pickLocation], (array) $this->request->getVar('stoppage'), [$dropLocation]);
        $stoppage = implode(",", array_filter($stoppage));

        $facility = $this->request->getVar('facility');
        if (!empty($facility)) {
            $facility = implode(",", $facility);
        }

        $tripData = array(
            "fleet_id" => $this->request->getVar('fleet_id'),
            "schedule_id" => $this->request->getVar('schedule_id'),
            "pick_location_id" => $this->request->getVar('pick_location_id'),
            "drop_location_id" => $this->request->getVar('drop_location_id'),
            "vehicle_id" => $this->request->getVar('vehicle_id'),
            "distance" => $this->request->getVar('distance'),
            "journey_hour" => $this->request->getVar('journey_hour'),
            "special_seat" => $this->request->getVar('special_seat'),
            "child_seat" => $this->request->getVar('child_seat'),
            "adult_fair" => $this->request->getVar('adult_fair'),
            "child_fair" => $this->request->getVar('child_fair'),
            "special_fair" => $this->request->getVar('special_fair'),
            "weekend" => (string) $weekend,
            "stoppage" => (string) $stoppage,
            "facility" => (string) $facility,
            "company_name" => $this->request->getVar('company_name'),
            "free_luggage_kg" => $this->request->getVar('free_luggage_kg') ?? 0.00,
            "paid_max_luggage_pcs" => $this->request->getVar('paid_max_luggage_pcs'),
            "price_pcs" => $this->request->getVar('price_pcs'),
            "special_max_luggage_pcs" => $this->request->getVar('special_max_luggage_pcs'),
            "special_price_pcs" => $this->request->getVar('special_price_pcs'),
            "max_length" => $this->request->getVar('max_length'),
            "max_weight" => $this->request->getVar('max_weight'),
            "startdate" => $this->request->getVar('startdate'),
            "status" => $this->request->getVar('status'),
        );

        if ($this->validation->run($tripData, 'trip')) {
            // trip data is valid
            $this->db->transStart();
            $maintrip = $this->tripModel->insert($tripData);

            // build data for pickdropstands
            foreach ($busStandTime as $key => $pickupvalue) {
                $picupdata[$key] = array(
                    "stand_id" => $busStandlocation[$key],
                    "trip_id" => $maintrip,
                    "time" => $pickupvalue,
                    "type" => (int) $busStandtype[$key],
                    "detail" => $standDetails[$key],
                );
            }

            foreach ($dropbusStandTime as $dkey => $dropvalue) {
                $dropdata[$dkey] = array(
                    "stand_id" => $dropbusStandlocation[$dkey],
                    "trip_id" => $maintrip,
                    "time" => $dropvalue,
                    "type" => (int) $dropbusStandtype[$dkey],
                    "detail" => $dropstandDetails[$dkey],
                );
            }

            // insert pick stands
            // and insert drop stands
            $this->picdropModel->insertBatch($picupdata);
            $this->picdropModel->insertBatch($dropdata);

            // build subtrip data
            $subtripdata = array(
                "pick_location_id" => $this->request->getVar('pick_location_id'),
                "drop_location_id" => $this->request->getVar('drop_location_id'),
                "stoppage" => (string) $stoppage,
                "trip_id" => $maintrip,
                "adult_fair" => $this->request->getVar('adult_fair'),
                "child_fair" => $this->request->getVar('child_fair'),
                "special_fair" => $this->request->getVar('special_fair'),
                "type" => 'main',
                "status" => $this->request->getVar('status'),
            );

            // insert subtrip
            $this->subtripModel->insert($subtripdata);

            // build driver and assistant data
            // foreach ($driver as $key => $value) {
            //     $driverdata = array(
            //         "trip_id" => $maintrip,
            //         "employee_id" => $value,
            //         "employee_type" => '1',
            //     );

            //     $this->stuffassignModel->insert($driverdata);
            // }
            if (!empty($assistant)) {
                foreach ($assistant as $key => $avalue) {
                    $assistantdata = array(
                        "trip_id" => $maintrip,
                        "employee_id" => $avalue,
                        "employee_type" => '2',
                    );

                    $this->stuffassignModel->insert($assistantdata);
                }
            }

            $this->db->transComplete();
            return redirect()->route('index-trip')->with("success", "Data Save");
        }

        return redirect()->back()->withInput()->with('error', $this->validation->listErrors());
    }

    public function edit($id)
    {
        $data['viewpath'] = $this->Viewpath;
        $data['webseeting']    = $this->webSettingModel->first();
        $trip = $this->tripModel->find($id);
        // $driver = array();
        // foreach ($this->stuffassignModel->where('trip_id ', $id)->where('employee_type', 1)->findAll() as $key => $driverValue) {
        //     array_push($driver, $driverValue->employee_id);
        // }

        $assistant =  array();
        foreach ($this->stuffassignModel->where('trip_id ', $id)->where('employee_type', 2)->findAll() as $key => $assistantValue) {
            array_push($assistant, $assistantValue->employee_id);
        }

        $data['stoppage'] = explode(",", $trip->stoppage);
        $data['weekoff'] = explode(",", $trip->weekend);

        // $data['olddriver'] = $driver;
        $data['olddassistant'] = $assistant;

        $data['facilityold'] = explode(",", $trip->facility);

        $heading = lang("Localize.edit") . ' ' . lang("Localize.trip");
        $data['pageheading'] = $heading;



        $data['trip'] = $trip;
        $weekday = array(
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thursday',
            '5' => 'Friday',
            '6' => 'Saturday',
            '7' => 'Sunday',
        );
        $data['weekday'] =  $weekday;
        $data['location'] = $this->locationModel->findAll();
        $data['schedule'] = $this->scheduleeModel->findAll();
        $data['fleet_type'] = $this->fleetTypeModel->findAll();
        $data['vehicle_id'] = $this->vehicleModel->findAll();
        // $data['driver'] = $this->employeeModel->where('employeetype_id', 1)->findAll();
        $data['assistant'] = $this->employeeModel->where('employeetype_id', 2)->findAll();
        $data['stand'] = $this->standModel->findAll();
        $data['facility'] = $this->facilitypModel->findAll();
        $data['arrival'] = $this->picdropModel->where('trip_id', $id)->where('type', 1)->findAll();
        $data['departure'] = $this->picdropModel->where('trip_id', $id)->where('type', 0)->findAll();

        $data['module'] =    lang("Localize.trip");
        $data['title']  =    lang("Localize.trip_list");

        echo view($this->Viewpath . '\trip/edit', $data);
    }

    public function update($trip_id)
    {
        $data['viewpath'] = $this->Viewpath;
        $picupdata = array();
        $dropdata = array();

        $pickLocation = $this->request->getVar('pick_location_id');
        $dropLocation = $this->request->getVar('drop_location_id');
        $busStandTime = $this->request->getVar('picktime');
        $busStandlocation = $this->request->getVar('picstand');
        $standDetails = $this->request->getVar('detail');
        $busStandtype = $this->request->getVar('type');

        $dropbusStandTime = $this->request->getVar('droptime');
        $dropbusStandlocation = $this->request->getVar('dropstand');
        $dropstandDetails = $this->request->getVar('dropdetail');
        $dropbusStandtype = $this->request->getVar('droptype');

        foreach ($busStandTime as $key => $pickupvalue) {
            $picupdata[$key] = array(
                "stand_id" => $busStandlocation[$key],
                "trip_id" => $trip_id,
                "time" => $busStandTime[$key],
                "type" => (int) $busStandtype[$key],
                "detail" => $standDetails[$key],
            );
        }

        foreach ($dropbusStandTime as $dkey => $dropvalue) {
            $dropdata[$dkey] = array(
                "stand_id" => $dropbusStandlocation[$dkey],
                "trip_id" => $trip_id,
                "time" => $dropbusStandTime[$dkey],
                "type" => (int) $dropbusStandtype[$dkey],
                "detail" => $dropstandDetails[$dkey],
            );
        }

        $driver = $this->request->getVar('driver');
        $assistant = $this->request->getVar('assistant');

        $stoppage = array_merge([$pickLocation], (array) $this->request->getVar('stoppage'), [$dropLocation]);
        $stoppage = implode(",", array_filter($stoppage));

        $weekend = $this->request->getVar('weekend');
        if (!empty($weekend)) {
            $weekend = implode(",", $weekend);
        }

        $facility = $this->request->getVar('facility');
        if (!empty($facility)) {
            $facility = implode(",", $facility);
        }

        $tripData = array(
            "id" => $trip_id,
            "fleet_id" => $this->request->getVar('fleet_id'),
            "schedule_id" => $this->request->getVar('schedule_id'),
            "pick_location_id" => $this->request->getVar('pick_location_id'),
            "drop_location_id" => $this->request->getVar('drop_location_id'),
            "vehicle_id" => $this->request->getVar('vehicle_id'),
            "distance" => $this->request->getVar('distance'),
            "journey_hour" => $this->request->getVar('journey_hour'),
            "special_seat" => $this->request->getVar('special_seat'),
            "child_seat" => $this->request->getVar('child_seat'),
            "adult_fair" => $this->request->getVar('adult_fair'),
            "child_fair" => $this->request->getVar('child_fair'),
            "special_fair" => $this->request->getVar('special_fair'),
            "weekend" => (string) $weekend,
            "stoppage" => (string) $stoppage,
            "facility" => (string) $facility,
            "company_name" => $this->request->getVar('company_name'),
            "free_luggage_kg" => $this->request->getVar('free_luggage_kg') ?? 0.00,
            "paid_max_luggage_pcs" => $this->request->getVar('paid_max_luggage_pcs'),
            "price_pcs" => $this->request->getVar('price_pcs'),
            "special_max_luggage_pcs" => $this->request->getVar('special_max_luggage_pcs'),
            "special_price_pcs" => $this->request->getVar('special_price_pcs'),
            "startdate" => $this->request->getVar('startdate'),
            "status" => $this->request->getVar('status'),
            "max_length" => $this->request->getVar('max_length'),
            "max_weight" => $this->request->getVar('max_weight'),
        );

        if ($this->validation->run($tripData, 'trip')) {
            $this->db->transStart();
            $this->db->query('SET foreign_key_checks = 0');

            $this->tripModel->save($tripData);

            $subtripid = $this->subtripModel->where('trip_id', $trip_id)->where('type', 'main')->find();

            $subtripdata = array(
                "id" => $subtripid[0]->id,
                "pick_location_id" => $this->request->getVar('pick_location_id'),
                "drop_location_id" => $this->request->getVar('drop_location_id'),
                "stoppage" => (string) $stoppage,
                "trip_id" => $trip_id,
                "adult_fair" => $this->request->getVar('adult_fair'),
                "child_fair" => $this->request->getVar('child_fair'),
                "special_fair" => $this->request->getVar('special_fair'),
                "type" => 'main',
                "status" => $this->request->getVar('status'),

            );

            $this->subtripModel->save($subtripdata);

            $this
                ->stuffassignModel
                ->where('trip_id', $trip_id)
                ->where('employee_type', 2)
                ->delete('', true);

            // foreach ($driver as $key => $value) {
            //     $driverdata = array(
            //         "trip_id" => $trip_id,
            //         "employee_id" => $value,
            //         "employee_type" => '1',
            //     );

            //     $this->stuffassignModel->insert($driverdata);
            // }

            if (!empty($assistant)) {
                foreach ($assistant as $key => $avalue) {
                    $assistantdata = array(
                        "trip_id" => $trip_id,
                        "employee_id" => $avalue,
                        "employee_type" => '2',
                    );

                    $this->stuffassignModel->insert($assistantdata);
                }
            }


            if ($this->request->getVar('status') == 0) {

                $this->subtripModel->where('trip_id', $trip_id)
                    ->set(['status' => 0])
                    ->update();
            }
            if ($this->request->getVar('status') == 1) {

                $this->subtripModel->where('trip_id', $trip_id)
                    ->set(['status' => 1])
                    ->update();
            }

            $this->picdropModel->where('trip_id', $trip_id)->delete('', true);

            $this->picdropModel->insertBatch($picupdata);

            $this->picdropModel->insertBatch($dropdata);

            $this->db->query('SET foreign_key_checks = 1');
            $this->db->transComplete();
            return redirect()->route('index-trip')->with("success", "Data Save");
        }

        return redirect()->back()->withInput()->with('fail', $this->validation->listErrors());
    }

    public function findtrip()
    {
        $data['location'] = $this->locationModel->findAll();
        $data['pageheading'] = lang("Localize.trip");
        echo view($this->Viewpath . '\trip/findtrip', $data);
    }

    public function getAllTrip()

    {
        $day = $this->request->getVar('journeydate');
        $day = date('Y-m-d', strtotime($day));



        $dayofweek = date('N', strtotime($this->request->getVar('journeydate')));
        $picklocation = $this->request->getVar('pick_location_id');
        $droplocation = $this->request->getVar('drop_location_id');
        $maintripId = array();


        $getdata =  $this->tripModel->select('trips.id')->Where('startdate >', $day)->where('status', 1)->orwhere("find_in_set($dayofweek, weekend)")->findAll();

        foreach ($getdata as $key => $value) {
            array_push($maintripId, (int)$value->id);
        }

        if ($getdata) {
            $getMainTripid = array();
            $subtrips =  $this->subtripModel->select('trip_id')->where('pick_location_id', $picklocation)->where('drop_location_id', $droplocation)->whereNotIn('trip_id', $maintripId)->findAll();



            foreach ($subtrips as $key => $svalue) {
                array_push($getMainTripid, (int)$svalue->trip_id);
            }


            if ($subtrips) {
                $allTripList =  $this->subtripModel->select('trips.id as tripid,trips.*,fleets.*,schedules.*,vehicles.*,subtrips.id as subtripId,subtrips.*')
                    ->join('trips', 'trips.id = subtrips.trip_id')
                    ->join('fleets', 'fleets.id = trips.fleet_id')
                    ->join('schedules', 'schedules.id = trips.schedule_id')
                    ->join('vehicles', 'vehicles.id = trips.vehicle_id')
                    ->whereIn('trip_id', $getMainTripid)
                    ->where('subtrips.status', 1)
                    ->findAll();
                echo json_encode($allTripList);
            } else {
                dd("Holiday for all trip No trip found");
            }
        } else {
            $getMainTripid = array();
            $subtrips =  $this->subtripModel->select('trip_id')->where('pick_location_id', $picklocation)->where('drop_location_id', $droplocation)->findAll();

            foreach ($subtrips as $key => $svalue) {
                array_push($getMainTripid, (int)$svalue->trip_id);
            }
            if ($subtrips) {

                $subtrips =  $this->subtripModel->select('trips.id as tripid,trips.*,fleets.*,schedules.*,vehicles.*,subtrips.id as subtripId,subtrips.*')
                    ->join('trips', 'trips.id = subtrips.trip_id')
                    ->join('fleets', 'fleets.id = trips.fleet_id')
                    ->join('schedules', 'schedules.id = trips.schedule_id')
                    ->join('vehicles', 'vehicles.id = trips.vehicle_id')
                    ->whereIn('trip_id', $getMainTripid)
                    ->where('subtrips.status', 1)
                    ->findAll();
                echo json_encode($subtrips);
            } else {
                dd("NO trip found for this destination");
            }
        }
    }



    public function delete($id)
    {
        $subtripid = array();
        $subtrip = $this->subtripModel->where('trip_id', $id)->findAll();


        foreach ($subtrip as $key => $subvalue) {
            $subid = $subvalue->id;
            array_push($subtripid, $subid);
        }

        if ($subtrip) {
            $this->subtripModel->delete($subtripid);
        }

        $this->tripModel->delete($id);
        return redirect()->route('index-trip')->with("fail", "Data Deleted");
    }

    public function tripDriverList($tripId)
    {
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

        $data['driverList'] = $this->stuffassignModel->select('stuffassigns.*,employees.*,stuffassigns.id as s_id,employees.first_name as emp_first_name,employees.last_name as emp_last_name,user_details.first_name as approve_by_firstname,user_details.last_name as approve_by_lastname')
            ->join('employees', 'stuffassigns.employee_id = employees.id ', 'left')
            ->join('user_details', 'stuffassigns.approved_by = user_details.user_id', 'left')
            ->where('stuffassigns.trip_id', $tripId)
            ->where('stuffassigns.employee_type', 1)
            ->findAll();



        echo view($this->Viewpath . '\trip/driverlist', $data);
    }

    public function assignDriver($tripId)
    {
        $data['tripId'] = $tripId;

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

        if ($data['employee_type'] == 1) {
            $data['driver'] = $this->employeeModel->where('employeetype_id', 1)->where('id', $this->session->get('employee_id'))->findAll();
        } else {
            $data['driver'] = $this->employeeModel->where('employeetype_id', 1)->findAll();
        }
        echo view($this->Viewpath . '\trip/assigndriver', $data);
    }

    public function addDriverTrip()
    {
        $tripId = $this->request->getVar('tripId');
        $driver = $this->request->getVar('driver');
        $start_date = $this->request->getVar('start_date');
        $end_date = $this->request->getVar('end_date');

        //check the employee type of the logged in user
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


        $checkDriverForTrip = $this->stuffassignModel
            ->where('trip_id', $tripId)
            ->where("DATE(start_date) <= '$end_date'")
            ->where("DATE(end_date) >= '$start_date'")
            ->where('is_approved', 1)
            ->findAll();

        $checkDriver = $this->stuffassignModel
            ->where('employee_id', $driver)
            ->where("DATE(start_date) <= '$end_date'")
            ->where("DATE(end_date) >= '$start_date'")
            ->where('is_approved', 1)
            ->findAll();

        // var_dump($checkDriverForTrip);exit
        if ($checkDriverForTrip) {
            $data = [
                'message' => "Driver Already Assigned to this trip for this date range",
                'status' => "fail",
                'response' => 400,
            ];
            return $this->response->setJSON($data);
        }
        if ($checkDriver) {
            $data = [
                'message' => "Driver Already Assigned to another trip for this date range",
                'status' => "fail",
                'response' => 400,
            ];
            return $this->response->setJSON($data);
        }

        //check the employee type of the logged in user
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

        if ($data['employee_type'] == 1) {
            $driverdata = array(
                "trip_id" => $tripId,
                "employee_id" => $this->session->get('employee_id'),
                "employee_type" => '1',
                "start_date" => $start_date,
                "end_date" => $end_date,
                "is_approved" => 0,
                "approved_by" => null,
            );
        } else {
            $driverdata = array(
                "trip_id" => $tripId,
                "employee_id" => $driver,
                "employee_type" => '1',
                "start_date" => $start_date,
                "end_date" => $end_date,
                "is_approved" => 1,
                "approved_by" => session()->get('user_id'),
            );
        }

        $stuffAssign =  $this->stuffassignModel->insert($driverdata);
        if ($stuffAssign) {
            $data = [
                'message' => "Data Save",
                'status' => "success",
                'response' => 200,
            ];
        } else {
            $data = [
                'message' => "Data Not Save",
                'status' => "fail",
                'response' => 400,
            ];
        }
        return $this->response->setJSON($data);
    }

    public function approveDriver($id)
    {
        $driverToApprove = $this->stuffassignModel
            ->where('id', $id)
            ->first();
            
        if ($driverToApprove != null) {
            $tripId = $driverToApprove->trip_id;
            $driver = $driverToApprove->employee_id;
            $start_date = $driverToApprove->start_date;
            $end_date = $driverToApprove->end_date;

            $checkDriverForTrip = $this->stuffassignModel
                ->where('trip_id', $tripId)
                ->where("DATE(start_date) <= '$end_date'")
                ->where("DATE(end_date) >= '$start_date'")
                ->where('is_approved', 1)
                ->findAll();

            $checkDriver = $this->stuffassignModel
                ->where('employee_id', $driver)
                ->where("DATE(start_date) <= '$end_date'")
                ->where("DATE(end_date) >= '$start_date'")
                ->where('is_approved', 1)
                ->findAll();

            // var_dump($checkDriverForTrip);exit
            if ($checkDriverForTrip) {
                $data = [
                    'message' => "Driver Already Assigned to this trip for this date range",
                    'status' => "fail",
                    'response' => 400,
                ];
                return $this->response->setJSON($data);
            }
            if ($checkDriver) {
                $data = [
                    'message' => "Driver Already Assigned to another trip for this date range",
                    'status' => "fail",
                    'response' => 400,
                ];
                return $this->response->setJSON($data);
            }

            $approveDriver = $this->stuffassignModel
                ->where('id', $id)
                ->set(['is_approved' => 1, 'approved_by' => session()->get('user_id')])
                ->update();
            // dd($approveDriver);

            $approveDriver = $this->stuffassignModel
                ->where('id', $id)
                ->findAll();

            if ($approveDriver) {
                $data = [
                    'message' => "Data Save",
                    'status' => true,
                    'response' => 200,
                ];
            } else {
                $data = [
                    'message' => "Data Not Save",
                    'status' => false,
                    'response' => 400,
                ];
            }
            return $this->response->setJSON($data);
        } else {
            $data = [
                'message' => "Not Found",
                'status' => "fail",
                'response' => 400,
            ];
            return $this->response->setJSON($data);
        }
    }
    public function showTripDriverList($tripId)
    {
        $data['module'] =    lang("Localize.trip");
        $data['title']  =    lang("Localize.driver") . ' ' . lang("Localize.trip_list");
        $data['pageheading'] = lang("Localize.driver") . ' ' . lang("Localize.trip_list");

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

        $data['driverList'] = $this->stuffassignModel->select('stuffassigns.*,employees.*,stuffassigns.id as s_id,employees.first_name as emp_first_name,employees.last_name as emp_last_name,user_details.first_name as approve_by_firstname,user_details.last_name as approve_by_lastname')
            ->join('employees', 'stuffassigns.employee_id = employees.id ', 'left')
            ->join('user_details', 'stuffassigns.approved_by = user_details.user_id', 'left')
            ->where('stuffassigns.trip_id', $tripId)
            ->where('stuffassigns.employee_type', 1)
            ->findAll();
        echo view($this->Viewpath . '\trip/showDriverlist', $data);
    }

    public function deleteDriver($id)
    {
        $approveDriver = $this->stuffassignModel
            ->where('id', $id)
            ->delete();
        // dd($approveDriver);
        if ($approveDriver) {
            $data = [
                'message' => "Data Deleted",
                'status' => true,
                'response' => 200,
            ];
        } else {
            $data = [
                'message' => "Operation Failed",
                'status' => false,
                'response' => 400,
            ];
        }
        return $this->response->setJSON($data);
    }
}
