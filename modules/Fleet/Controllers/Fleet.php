<?php

namespace Modules\Fleet\Controllers;

use App\Controllers\BaseController;
use App\Libraries\DeleteData\SoftwareSettings\Fleet as DeleteFleetData;
use Modules\Fleet\Models\FleetModel;
use App\Libraries\Rolepermission;
use Modules\Coupon\Models\CouponModel;
use Modules\Fitness\Models\FitnessModel;
use Modules\Fleet\Models\VehicleModel;
use Modules\Trip\Models\SubtripModel;
use Modules\Trip\Models\TripModel;
use Modules\Layout\Models\LayoutModel;
use Modules\Layout\Models\LayoutDetailsModel;
class Fleet extends BaseController
{
    private $Viewpath;
    protected $fleetModel;
    protected $vehicleModel;
    protected $fitnessModel;
    protected $tripModel;
    protected $subTripModel;
    protected $couponModel;
    private $layoutModel;
    private $layoutDetailsModel;
    protected $session;

    public function __construct()
    {
        $this->Viewpath = "Modules\Fleet\Views";
        $this->fleetModel = new FleetModel();
        $this->vehicleModel = new VehicleModel();
        $this->fitnessModel = new FitnessModel();
        $this->tripModel = new TripModel();
        $this->subTripModel = new SubtripModel();
        $this->couponModel = new CouponModel();
        $this->layoutModel = new LayoutModel();
        $this->layoutDetailsModel = new LayoutDetailsModel();
        
    }

    public function new()
    {
        $data['module'] =    lang("Localize.fleet");
        $data['title']  =    lang("Localize.add_fleet");
        $data['pageheading'] = lang("Localize.add_fleet");

        $data['layout'] = $this->layoutModel->findAll();

        echo view($this->Viewpath . '\fleet/new', $data);
    }

    public function create()
    {
        $data = array(
            "type" => $this->request->getVar('type'),
            "layout" => $this->request->getVar('layout'),
            "total_seat" => $this->request->getVar('total_seat'),
            "seat_number" => $this->request->getVar('seat_number'),
            "status" => $this->request->getVar('status'),
            
        );

        if ($this->validation->run($data, 'fleet')) {
            $this->fleetModel->insert($data);
            return redirect()->route('index-fleet')->with("success", "Data Save");
        } else {
            $data['validation'] = $this->validation;

            $data['module'] =    lang("Localize.fleet");
            $data['title']  =    lang("Localize.add_fleet");

            $data['pageheading'] = lang("Localize.add_fleet");

            echo view($this->Viewpath . '\fleet/new', $data);
        }
    }

    public function index()
    {
        $data['fleet'] = $this->fleetModel->select('fleets.*,layouts.layout_number as layout_number')->join('layouts', 'layouts.id = fleets.layout')->findAll();

        $data['module'] =    lang("Localize.fleet");
        $data['title']  =    lang("Localize.fleet_list");

        $data['pageheading'] = lang("Localize.fleet_list");

        $rolepermissionLibrary = new Rolepermission();
        $add_data = "add_fleet";
        $list_data = "fleet_list";

        $data['add_data'] = $rolepermissionLibrary->create($add_data);
        $data['edit_data'] = $rolepermissionLibrary->edit($list_data);
        $data['delete_data'] = $rolepermissionLibrary->delete($list_data);

        echo view($this->Viewpath . '\fleet/index', $data);
    }

    public function edit($id)
    {
        $data['fleet'] = $this->fleetModel->find($id);

        $data['module'] =    lang("Localize.fleet");
        $data['title']  =    lang("Localize.fleet_list");
        $data['layout'] = $this->layoutModel->findAll();

        $heading = lang("Localize.edit") . ' ' . lang("Localize.fleet");
        $data['pageheading'] = $heading;

        echo view($this->Viewpath . '\fleet/edit', $data);
    }

    public function update($id)
    {

        $validdata = array(
            "type" => $this->request->getVar('type'),
            "layout" => $this->request->getVar('layout'),
            "total_seat" => $this->request->getVar('total_seat'),
            "seat_number" => $this->request->getVar('seat_number'),
            "status" => $this->request->getVar('status'),
            
        );

        $data = array(
            "id" => $id,
            "type" => $this->request->getVar('type'),
            "layout" => $this->request->getVar('layout'),
            "total_seat" => $this->request->getVar('total_seat'),
            "seat_number" => $this->request->getVar('seat_number'),
            "status" => $this->request->getVar('status'),
            
        );

        if ($this->validation->run($validdata, 'fleet')) {
            $this->fleetModel->save($data);
            return redirect()->route('index-fleet')->with("success", "Data Update");
        } else {
            $data['validation'] = $this->validation;

            $data['module'] =    lang("Localize.fleet");
            $data['title']  =    lang("Localize.fleet_list");

            echo view($this->Viewpath . '\fleet/edit', $data);
        }
    }
}
