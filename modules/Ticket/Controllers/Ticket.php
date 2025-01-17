<?php

namespace Modules\Ticket\Controllers;

use Exception;
use App\Libraries\Ticketmail;
use CodeIgniter\Database\RawSql;
use Modules\Tax\Models\TaxModel;
use App\Libraries\Rolepermission;
use CodeIgniter\API\ResponseTrait;
use Modules\Role\Models\RoleModel;
use Modules\Trip\Models\TripModel;
use Modules\User\Models\UserModel;
use App\Controllers\BaseController;
use Modules\Agent\Models\AgentModel;
use Modules\Fleet\Models\FleetModel;
use PhpParser\Node\Expr\Cast\Double;
use Modules\Trip\Models\SubtripModel;
use Modules\Coupon\Models\CouponModel;
use Modules\Fleet\Models\VehicleModel;
use Modules\Ticket\Models\TicketModel;
use Modules\Trip\Models\FacilityModel;
use Modules\Trip\Models\PickdropModel;
use Modules\Website\Models\EmailModel;
use Modules\Location\Models\StandModel;
use Modules\Ticket\Models\MaxtimeModel;
use Modules\User\Models\UserDetailModel;
use Modules\Agent\Models\Agentcommission;
use Modules\Trip\Models\StuffassignModel;
use Modules\Employee\Models\EmployeeModel;
use Modules\Location\Models\LocationModel;
use Modules\Schedule\Models\ScheduleModel;
use Modules\Ticket\Models\JourneylistModel;


use Modules\Ticket\Models\PartialpaidModel;

use Modules\Website\Models\WebsettingModel;
use Modules\Paymethod\Models\PaymethodModel;
use Modules\Coupon\Models\CoupondiscountModel;
use Modules\Trip\Models\RoundtripdiscoundModel;
use Modules\Layout\Models\LayoutModel;
use Modules\Layout\Models\LayoutDetailsModel;
use Modules\Luggage\Models\LuggagesettingModel;
//tag model
use Modules\Ticket\Models\TagModel;

class Ticket extends BaseController
{
    use ResponseTrait;

    protected $Viewpath;
    protected $ticketModel;
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
    protected $taxModel;
    protected $db;
    protected $paymethodModel;
    protected $userModel;
    protected $userDetailModel;
    protected $journeylistModel;
    protected $partialpaidModel;
    protected $maxtimeModel;
    protected $roleModel;
    protected $agentModel;
    protected $agentCommissionModel;

    protected $websettingModel;
    protected $couponModel;
    protected $coupondiscountModel;

    protected $roundtripdiscoundModel;
    private $layoutModel;
    private $layoutDetailsModel;
    private $luggageSettingModel;
    private $tagModel;

    public function __construct()
    {

        $this->Viewpath = "Modules\Ticket\Views";
        $this->ticketModel = new TicketModel();
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
        $this->taxModel = new TaxModel();
        $this->db = \Config\Database::connect();
        $this->paymethodModel = new PaymethodModel();

        $this->userModel = new UserModel();
        $this->userDetailModel = new UserDetailModel();

        $this->journeylistModel = new JourneylistModel();
        $this->partialpaidModel = new PartialpaidModel();

        $this->maxtimeModel = new MaxtimeModel();
        $this->roleModel = new RoleModel();

        $this->agentModel = new AgentModel();
        $this->agentCommissionModel = new Agentcommission();

        $this->websettingModel = new WebsettingModel();

        $this->coupondiscountModel = new CoupondiscountModel();
        $this->couponModel = new CouponModel();

        $this->roundtripdiscoundModel = new RoundtripdiscoundModel();

        $this->layoutModel = new LayoutModel();
        $this->layoutDetailsModel = new LayoutDetailsModel();
        $this->luggageSettingModel = new LuggagesettingModel();
        $this->tagModel = new TagModel();
    }

    function new()
    {

        $this->session->remove('pick_location_id');
        $this->session->remove('drop_location_id');
        $this->session->remove('filterjourneydate');
        $this->session->remove('filterreturndate');
        $this->session->remove('fleet_id');

        $this->session->remove('subtripId');
        $this->session->remove('seatnumbers');
        $this->session->remove('totalseat');
        $this->session->remove('journeydate');
        $this->session->remove('aseat');
        $this->session->remove('spseat');
        $this->session->remove('cseat');
        $this->session->remove('totalprice');
        $this->session->remove('tax');
        $this->session->remove('grandtotal');
        $this->session->remove('pickstand');
        $this->session->remove('dropstand');
        $this->session->remove('isrountrip');
        $this->session->remove('discountPercent');
        $this->session->remove('roundtrip_discount');

        $data['pick_location_id'] = null;
        $data['drop_location_id'] = null;
        $data['filterjourneydate'] = null;
        $data['filterreturndate'] = null;
        $data['fleet_id'] = null;

        $data['filterpath'] = $this->Viewpath;
        $data['location'] = $this->locationModel->findAll();
        $data['fleet_type'] = $this->fleetTypeModel->findAll();

        $data['module'] =    lang("Localize.ticket_booking");
        $data['title']  =    lang("Localize.book_ticket");

        $heading = lang("Localize.search") . ' ' . lang("Localize.ticket");
        $data['pageheading'] = $heading;

        echo view($this->Viewpath . '\ticket/new', $data);
    }

    public function findtrip()
    {
        $t = $this->request->getVar();
        $websetting = $this->websettingModel->first();

        // Get request vars
        $pick_location_id  = $this->request->getVar('pick_location_id') ?: session('pick_location_id');
        $drop_location_id  = $this->request->getVar('drop_location_id') ?: session('drop_location_id');
        $filterjourneydate = $this->request->getVar('filterjourneydate') ?: session('filterjourneydate');
        $filterreturndate  = $this->request->getVar('filterreturndate') ?: session('filterreturndate');
        $fleet_id          = $this->request->getVar('fleet_id') ?: session('fleet_id');

        // 
        $journeyDate = date('Y-m-d', strtotime($filterjourneydate));
        $journeyDayOfWeek = date('N', strtotime($filterjourneydate));

        $this->session->remove('pick_location_id');
        $this->session->remove('drop_location_id');
        $this->session->remove('filterjourneydate');
        $this->session->remove('filterreturndate');
        $this->session->remove('fleet_id');

        $this->session->remove('subtripId');
        $this->session->remove('seatnumbers');
        $this->session->remove('totalseat');
        $this->session->remove('journeydate');
        $this->session->remove('aseat');
        $this->session->remove('spseat');
        $this->session->remove('cseat');
        $this->session->remove('totalprice');
        $this->session->remove('tax');
        $this->session->remove('grandtotal');
        $this->session->remove('pickstand');
        $this->session->remove('dropstand');
        $this->session->remove('isrountrip');
        $this->session->remove('discountPercent');
        $this->session->remove('roundtrip_discount');

        $tripData['pick_location_id'] =  $pick_location_id;
        $tripData['drop_location_id'] = $drop_location_id;
        $tripData['filterjourneydate'] =  $filterjourneydate;
        $tripData['filterreturndate'] = $filterreturndate;
        $tripData['fleet_id'] = $fleet_id;
        $this->session->set($tripData);

        $dayCompareDate = date('Y-m-d', strtotime('+' . $websetting->max_days . 'day'));

        if ($filterjourneydate > $dayCompareDate) {
            return redirect()->route('new-ticket')->with("fail", "No advance booking for this day");
        }

        $data = array('trips' => [], 'in_holiday' => [], 'to_open' => [], 'inactive' => [], 'you_may_like' => []);
        $data['pick_location_id'] = $pick_location_id;
        $data['drop_location_id'] = $drop_location_id;
        $data['filterjourneydate'] = $filterjourneydate;
        $data['filterreturndate'] = $filterreturndate;
        $data['fleet_id'] = $fleet_id;

        $data['filterpath'] = $this->Viewpath;
        $data['location'] = $this->locationModel->findAll();
        $data['fleet_type'] = $this->fleetTypeModel->findAll();

        $timeForTimezone = $websetting->timezone;
        $timezone = new \DateTimeZone($timeForTimezone);
        $date = new \DateTime('now', $timezone);
        $fDate = $date->format('Y-m-d');
        $fTime = $date->format('h:i A');

        $allSubTrips = $this->getAllTrip($pick_location_id, $drop_location_id, $fDate, $fTime, $journeyDate, $fleet_id);

        foreach ($allSubTrips as $subTrip) {
            if ($subTrip->status == 0) {
                // subtrip is inactive
                $data['inactive'][] = $subTrip;
                continue;
            }

            if (strtotime($journeyDate) < strtotime($subTrip->startdate)) {
                // subtrip yet not begin
                $data['to_open'][] = $subTrip;
                continue;
            }

            if (@in_array($journeyDayOfWeek, explode(',', $subTrip->weekend))) {
                // subtrip is on holiday
                $data['in_holiday'][] = $subTrip;
                continue;
            }

            if (($subTrip->pick_location_id != $pick_location_id) || ($subTrip->drop_location_id != $drop_location_id)) {
                // subtrip is related to selected locations
                $data['you_may_like'][] = $subTrip;
                continue;
            }

            $data['trips'][] = $subTrip;
        }


        $data['taxtype'] = $websetting->tax_type;
        $data['module'] = lang("Localize.ticket_booking");
        $data['title'] = lang("Localize.book_ticket");
        $data['pageheading'] = lang("Localize.book") . ' ' . lang("Localize.ticket");
        $data['triptype'] = lang("Localize.single") . ' ' . lang("Localize.trip");

        return view($this->Viewpath . '\ticket\index', $data);
    }

    public function getAllTrip($pickLocationId, $dropLocationId, $fDate, $fTime, $journeyDate, $fleetId = null)
    {
        // dd($fTime);
        // bind search query
        $this->subtripModel
            ->select('trips.id as tripid, trips.*, fleets.*, schedules.*, vehicles.*, subtrips.id as subtripId, subtrips.*')

            ->join('trips', 'trips.id = subtrips.trip_id')
            ->join('fleets', 'fleets.id = trips.fleet_id')
            ->join('schedules', 'schedules.id = trips.schedule_id')
            ->join('vehicles', 'vehicles.id = trips.vehicle_id')

            ->groupStart()
            ->groupStart()
            ->where('subtrips.pick_location_id', $pickLocationId)
            ->where('subtrips.drop_location_id', $dropLocationId)
            ->groupEnd()

            ->orGroupStart()
            ->whereIn('subtrips.trip_id', function ($subQuery) use ($pickLocationId, $dropLocationId) {
                $subQuery
                    ->select('sm.trip_id')
                    ->from('subtrips sm')
                    ->where('sm.pick_location_id', $pickLocationId)
                    ->where('sm.drop_location_id', $dropLocationId)
                    ->where('sm.type', 'subtrip');
            })
            ->where('subtrips.type', 'main')
            ->groupEnd()
            ->groupEnd()

            ->where('subtrips.status', 1)
            ->orderBy('subtrips.id', 'DESC');


        ($journeyDate == $fDate) && ($this->subtripModel->where("STR_TO_DATE(schedules.start_time, '%h:%i %p') >= STR_TO_DATE('$fTime', '%h:%i %p')"));
        $fleetId && $this->subtripModel->where('trips.fleet_id', $fleetId);
        // $query = $this->subtripModel->builder()->getCompiledSelect();dd($query);
        return $this->subtripModel->findAll();
    }

    public function getSingleTrip($subTripId, $journeydate)
    {
        $maxtime = $this->maxtimeModel->first();
        $maxtime =  60 * (int)$maxtime->maxtime;
        $subtripInfo = $this->subtripModel->find($subTripId);

        // Release non paid expired seats
        $getData = $this->ticketModel
            ->where('trip_id', $subtripInfo->trip_id)
            ->where('journeydata', $journeydate)
            ->where('payment_status', "unpaid")
            ->where('cancel_status', 0)
            ->where('refund', 0)
            ->findAll();

        foreach ($getData as $key => $delvalue) {
            $cratetime = strtotime($delvalue->created_at);
            $timenow = strtotime("now");

            if (($timenow - $cratetime) > $maxtime) {
                $this->ticketModel->where('id', $delvalue->id)->set(['cancel_status' => 1])->update();
                $bookingId = $this->ticketModel->find($delvalue->id);
                $this->journeylistModel->where('booking_id', $bookingId->booking_id)->delete();
            }
        }

        // Build total booked seats
        $this->ticketModel
            ->where('journeydata', $journeydate)
            ->where('cancel_status', 0)
            ->where('refund', 0);

        if ($subtripInfo->type == 'subtrip') {
            $mainTripId = $subtripInfo->trip_id;
            $subtripStoppagePointsArr = array_filter(explode(',', $subtripInfo->stoppage));
            $mainTripMainSubtripId = $this->subtripModel->where('trip_id', $mainTripId)->where('type', 'main')->first();

            $this->ticketModel
                ->groupStart()
                ->whereIn('subtrip_id', [$subtripInfo->id, $mainTripMainSubtripId->id])

                ->orGroupStart()
                ->where('trip_id', $subtripInfo->trip_id)
                ->whereIn('pick_location_id', array_filter($subtripStoppagePointsArr, fn ($stp_id) => $stp_id != $subtripInfo->drop_location_id))
                ->groupEnd()

                ->orGroupStart()
                ->where('trip_id', $subtripInfo->trip_id)
                ->whereIn('drop_location_id', array_filter($subtripStoppagePointsArr, fn ($stp_id) => $stp_id != $subtripInfo->pick_location_id))
                ->groupEnd()
                ->groupEnd();
        } else {
            $this->ticketModel->where('trip_id', $subtripInfo->trip_id);
        }

        $bookseat = array_column($this->ticketModel->findAll(), 'seatnumber');

        // Build locations and taxes
        $pickdrop = $this->picdropModel->select('pickdrops.id as pickdropid,pickdrops.*,stands.*')
            ->join('stands', 'stands.id = pickdrops.stand_id')
            ->where('trip_id', $subtripInfo->trip_id)
            ->findAll();

        $tax = $this->taxModel->where('status', 1)->findAll();

        // Build subtrip info
        $subtrips = $this->subtripModel->select('trips.id as tripid,trips.*,fleets.*,schedules.*,vehicles.*,subtrips.id as subtripId,subtrips.*')
            ->join('trips', 'trips.id = subtrips.trip_id')
            ->join('fleets', 'fleets.id = trips.fleet_id')
            ->join('schedules', 'schedules.id = trips.schedule_id')
            ->join('vehicles', 'vehicles.id = trips.vehicle_id')
            ->where('subtrips.status', 1)
            ->where('subtrips.id', $subTripId)
            ->findAll();

        $luggagesetting  = $this->luggageSettingModel->first();
        $websetting = $this->websettingModel->first();

        if ($websetting->luggage_service == 1 && empty($luggagesetting)) {
            return $this->response->setJSON([
                'status' => "error",
                'response' => 500,
                'message' => 'Luggage service settings should be initialize.',
            ]);
        }

        return $this->response->setJSON([
            'status' => "success",
            'response' => 200,
            'subtrips' => json_encode($subtrips),
            'pickdrop' => json_encode($pickdrop),
            'tax' => json_encode($tax),
            'bookseat' => json_encode(implode(',', $bookseat)),
            'luggagesetting' => json_encode($luggagesetting),
            'websetting' => json_encode($websetting),


        ]);
    }

    public function getSeatLayout()
    {
        $subTripId = $this->request->getVar('id');
        $fleetId = $this->request->getVar('fleet_id');
        $bookedSeats = $this->request->getVar('booked');
        $fleetInfo = $this->fleetTypeModel->find($fleetId);

        // build seat layout vars
        $seatLayout     = $fleetInfo->layout;
        // $seatLayoutArr  = array_filter(explode('-', $seatLayout));
        // $totalSeatInRow = array_sum($seatLayoutArr);
        // $tSeatRowWithB  = $totalSeatInRow + count($seatLayoutArr) - 1;
        $seatNumbers    = array_filter(explode(',', $fleetInfo->seat_number));

        $layout = $this->layoutModel->find($fleetInfo->layout);
        $layout_details = $this->layoutDetailsModel
            ->select('layout_details.*, sd1.element as column1_element, sd2.element as column2_element, sd3.element as column3_element, sd4.element as column4_element, sd5.element as column5_element')
            ->join('seat_elements sd1', 'sd1.id = layout_details.column1', 'left')
            ->join('seat_elements sd2', 'sd2.id = layout_details.column2', 'left')
            ->join('seat_elements sd3', 'sd3.id = layout_details.column3', 'left')
            ->join('seat_elements sd4', 'sd4.id = layout_details.column4', 'left')
            ->join('seat_elements sd5', 'sd5.id = layout_details.column5', 'left')
            ->where('layout_id', $fleetInfo->layout)->findAll();


        // Extracting layout details
        $seatRows = [];
        $seatRows['layout_number'] = $layout->layout_number;
        $seatRows['layout_id'] = $layout->id;
        $seatRows['car_type'] = $layout->car_type;
        $seatRows['total_seat'] = $layout->total_seat;
        $seatRows['total_row'] = $layout->total_row;
        $seatRows['total_column'] = $layout->total_column;

        // Initializing seatRows array
        $seatRows['rowData'] = [];

        // Loop through each row
        foreach ($layout_details as $row) {
            $rowData = [
                'row_no' => $row->row_no,
                'columns' => [],
            ];

            // Loop through each column
            for ($i = 1; $i <= 5; $i++) {
                $columnKey = 'column' . $i;
                $columnElementKey = 'column' . $i . '_element';
                $seatNoKey = 'seat_no' . $i;

                // Concatenate row and column values to get the full seat number

                $columnData = [
                    'column_no' => $i,
                    'column_value' => $row->$columnKey,
                    'column_element' => $row->$columnElementKey,
                    'seat_no' => $row->$seatNoKey,
                    'isBooked' => ($row->$seatNoKey != '') ? in_array($row->$seatNoKey, $bookedSeats) : false,
                ];

                // Adding the column data to the row
                $rowData['columns'][$i] = $columnData;
            }

            // Adding the row data to the result
            $seatRows['rowData'][] = $rowData;
        }
        $filePath = sprintf("%s\\ticket\\seatlayouts\\newlayout", $this->Viewpath);
        return view($filePath, compact('subTripId', 'seatRows'));
    }
    // public function getSeatLayout()
    // {
    //     $subTripId = $this->request->getVar('id');
    //     $fleetId = $this->request->getVar('fleet_id');
    //     $bookedSeats = $this->request->getVar('booked');
    //     $fleetInfo = $this->fleetTypeModel->find($fleetId);

    //     // build seat layout vars
    //     $seatLayout     = $fleetInfo->layout;
    //     $seatLayoutArr  = array_filter(explode('-', $seatLayout));
    //     $totalSeatInRow = array_sum($seatLayoutArr);
    //     $tSeatRowWithB  = $totalSeatInRow + count($seatLayoutArr) - 1;
    //     $seatNumbers    = array_filter(explode(',', $fleetInfo->seat_number));

    //     // remove last seat
    //     // if fleet has last seat
    //     $lastSeat = '';
    //     $fleetInfo->last_seat && $lastSeat = array_pop($seatNumbers);

    //     // build seat group/row
    //     $seatRowGroup = array_chunk($seatNumbers, $totalSeatInRow);
    //     $seatRows = array_map(function ($singleRow) use ($seatLayoutArr, $tSeatRowWithB, $bookedSeats) {
    //         $newSingleRow = array();
    //         $currentTotalIndex = 0;

    //         foreach ($singleRow as $seatRowIndex => $singleSeat) {
    //             $seatRowIndex++;
    //             $newSingleRow[] = array(
    //                 'seatName' => $singleSeat,
    //                 'isBooked' => in_array($singleSeat, $bookedSeats)
    //             );

    //             if (($seatRowIndex != count($singleRow)) && (current($seatLayoutArr) + $currentTotalIndex == $seatRowIndex)) {
    //                 $newSingleRow[] = '';

    //                 $currentTotalIndex += current($seatLayoutArr);
    //                 array_shift($seatLayoutArr);
    //             }
    //         }

    //         return array_pad($newSingleRow, $tSeatRowWithB, '');
    //     }, $seatRowGroup);

    //     if ($lastSeat !== '') {
    //         // last seat exists
    //         // build last seat info
    //         $lastSeatInfo =  array(
    //             'seatName' => $lastSeat,
    //             'isBooked' => in_array($lastSeat, $bookedSeats)
    //         );

    //         if (($lastBlankSpace = array_search('', end($seatRows))) !== false) {
    //             // an blank space exists in last row
    //             // last seat place into the last blank space
    //             $seatRows[count($seatRows) - 1][$lastBlankSpace] = $lastSeatInfo;
    //         } else {
    //             // create new row and last seat place into it
    //             $seatRows[][] = $lastSeatInfo;
    //         }
    //     }

    //     try {
    //         $filePath = sprintf("%s\\ticket\\seatlayouts\\%s", $this->Viewpath, $seatLayout);
    //         return view($filePath, compact('subTripId', 'seatRows', 'totalSeatInRow'));
    //     } catch (\Throwable $e) {
    //         $filePath = sprintf("%s\\ticket\\seatlayouts\\default", $this->Viewpath);
    //         return view($filePath, compact('subTripId', 'seatRows', 'totalSeatInRow'));
    //     }
    // }

    public function booking()
    {
        // dd($this->request->getVar());
        if (empty($this->request->getVar())) {
            $failMsg = session()->getFlashdata('fail') ?: 'Session expired!';
            return redirect()->route('new-ticket')->with("fail", $failMsg);
        }

        $data['returndate'] = $this->request->getVar('returndate');

        $data['seatnumbers'] = $this->request->getVar('seatnumbers');
        $array = explode(",", $data['seatnumbers']);
        $daynamic = count($array);

        if (!empty($data['returndate'])) {
            $grandtotal = $this->request->getVar('grandtotal');
            $discount = $this->roundtripdiscoundModel->where('status', 1)->first();
            $discountMoney = 0;
            $discountPercent = 0;
            if (!empty($discount)) {
                $discountPercent = $discount->discountrate;
                $caltulateDiscont = (float) ($discountPercent / 100) * $grandtotal;
                $discountMoney = (float)  $grandtotal - (float)$caltulateDiscont;
            } else {
                $discountMoney = $grandtotal;
                $discountPercent = 0;
                $caltulateDiscont = 0;
            }

            $singleTripData['subtripId'] =  $this->request->getVar('subtripId');
            $singleTripData['seatnumbers'] = $this->request->getVar('seatnumbers');
            $singleTripData['totalseat'] =  $daynamic;
            $singleTripData['journeydate'] = $this->request->getVar('journeydate');
            $singleTripData['aseat'] = $this->request->getVar('aseat');
            $singleTripData['spseat'] = $this->request->getVar('spseat');
            $singleTripData['cseat'] = $this->request->getVar('cseat');
            $singleTripData['totalprice'] = $this->request->getVar('totalprice');
            $singleTripData['tax'] = $this->request->getVar('tax');
            $singleTripData['grandtotal'] = $discountMoney;
            $singleTripData['pickstand'] = $this->request->getVar('pickstand');
            $singleTripData['dropstand'] = $this->request->getVar('dropstand');
            $singleTripData['discountPercent'] = $discountPercent;
            $singleTripData['roundtrip_discount'] = $caltulateDiscont;
            $singleTripData['free_luggage_kg'] = $this->request->getVar('free_luggage_kg');
            $singleTripData['paid_max_luggage_pcs'] = $this->request->getVar('paid_max_luggage_pcs');
            $singleTripData['price_pcs'] = $this->request->getVar('price_pcs');
            $singleTripData['special_max_luggage_pcs'] = $this->request->getVar('special_max_luggage_pcs');
            $singleTripData['special_price_pcs'] = $this->request->getVar('special_price_pcs');
            $singleTripData['special_luggage'] = $this->request->getVar('special_luggage');



            $this->session->set($singleTripData);

            return redirect()->route('roundfindtrip-ticket');
        } else {
            $data['filterpath'] = $this->Viewpath;
            $builder = $this->db->table('country');
            $query = $builder->get();
            $data['country'] = $query->getResult();

            $data['subtripId'] = $this->request->getVar('subtripId');
            $data['seatnumbers'] = $this->request->getVar('seatnumbers');

            $array = explode(",", $data['seatnumbers']);

            $daynamic = count($array);
            $data['dynamicfield'] = $daynamic - 1;

            $data['totalseat'] = $daynamic;

            $data['journeydate'] = $this->request->getVar('journeydate');


            $data['aseat'] = $this->request->getVar('aseat');
            $data['spseat'] = $this->request->getVar('spseat');
            $data['cseat'] = $this->request->getVar('cseat');

            $data['totalprice'] = $this->request->getVar('totalprice');
            $data['tax'] = $this->request->getVar('tax');
            $data['free_luggage_kg'] = $this->request->getVar('free_luggage_kg');
            $data['paid_max_luggage_pcs'] = $this->request->getVar('paid_max_luggage_pcs');
            $data['price_pcs'] = $this->request->getVar('price_pcs');
            $data['special_max_luggage_pcs'] = $this->request->getVar('special_max_luggage_pcs');
            $data['special_price_pcs'] = $this->request->getVar('special_price_pcs');
            $data['special_luggage'] = $this->request->getVar('special_luggage');




            if ($this->session->has('discountPercent')) {
                $percentDiscount = $this->session->get('discountPercent');
                $getGrandtotal = $this->request->getVar('grandtotal');
                $caltulateDiscont = (float) ($percentDiscount / 100) * $getGrandtotal;
                $discountMoney = (float)  $getGrandtotal - (float)$caltulateDiscont;
                $data['grandtotal'] = $discountMoney;
                $data['roundtrip_discount'] = $caltulateDiscont;
            } else {

                $data['grandtotal'] = $this->request->getVar('grandtotal');
                $data['roundtrip_discount'] = 0;
            }



            $data['pickstand'] = $this->request->getVar('pickstand');
            $data['dropstand'] = $this->request->getVar('dropstand');


            $data['paymethod'] = $this->paymethodModel->where('status', 1)->findAll();

            $data['module'] =    lang("Localize.ticket_booking");
            $data['title']  =    lang("Localize.ticket_list");

            $heading = lang("Localize.book") . ' ' . lang("Localize.ticket");
            $data['pageheading'] = $heading;

            $data['discount'] = $this->session->get('discount');
            $data['rountripStatus'] = 0;
            $data['isrountrip'] = 0;

            echo view($this->Viewpath . '\ticket\booking', $data);
        }
    }

    public function create()
    {
        // dd($this->request->getVar());
        $ticketmailLibrary = new Ticketmail();

        $rand = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 8);
        $rand = "TB" . $rand;
        $maitripid = null;
        $subtripid = null;
        $piclocation = null;
        $droplocation = null;
        $vehicles_id = null;
        $backUserId = $this->session->get('user_id');
        $subTripId = $this->request->getVar('subtripId');
        $payment_detail_rocord = $this->request->getVar('paydetail');

        $websetting = $this->websettingModel->first();
        if ($websetting) {
            $timeForTimezone = $websetting->timezone;
            $timezone = new \DateTimeZone($timeForTimezone);
            $date = new \DateTime('now', $timezone);
            $created_at = $date->format('Y-m-d H:i:s');
        }

        $subtrips = $this->subtripModel->select('trips.id as tripid,trips.*,fleets.*,schedules.*,vehicles.*,subtrips.id as subtripId,subtrips.*')
            ->join('trips', 'trips.id = subtrips.trip_id')
            ->join('fleets', 'fleets.id = trips.fleet_id')
            ->join('schedules', 'schedules.id = trips.schedule_id')
            ->join('vehicles', 'vehicles.id = trips.vehicle_id')
            ->where('subtrips.status', 1)
            ->where('subtrips.id', $subTripId)
            ->findAll();

        foreach ($subtrips as $key => $value) {
            $maitripid = $value->tripid;
            $subtripid = $value->id;
            $piclocation = $value->pick_location_id;
            $droplocation = $value->drop_location_id;
            $vehicles_id = $value->vehicle_id;
        }

        // get user info
        $login_email = $this->request->getVar('login_email');
        $login_mobile = $this->request->getVar('login_mobile');
        $userid = $this->userCheck($login_mobile, $login_email ?? null);

        $loginUserId = $this->session->get('user_id');
        $bookUserId = $this->session->get('role_id');

        // Get role info
        $roelinfo = $this->roleModel->find($bookUserId);
        $bookUserType = $roelinfo->name;

        $ticketbooking = array(
            "booking_id" => $rand,
            "trip_id" => $maitripid,
            "subtrip_id" => $subtripid,
            "passanger_id" => $userid,
            "pick_location_id" => $piclocation,
            "drop_location_id" => $droplocation,
            "pick_stand_id" => $this->request->getVar('pickstand'),
            "drop_stand_id" => $this->request->getVar('dropstand'),
            "price" => $this->request->getVar('totalprice'),
            "discount" => $this->request->getVar('discount'),
            "totaltax" => $this->request->getVar('tax'),
            "paidamount" => $this->request->getVar('grandtotal'),
            "adult" => $this->request->getVar('aseat'),
            "chield" => $this->request->getVar('cseat'),
            "special" => $this->request->getVar('spseat'),
            "refund" => 0,
            "bookby_user_id" =>  $loginUserId,
            "bookby_user_type" => $bookUserType,
            "journeydata" => $this->request->getVar('journeydate'),
            "pay_type_id" => $this->request->getVar('pay_method'),
            "payment_status" => $this->request->getVar('payment_status'),
            "payment_detail" => $this->request->getVar('paydetail'),
            "vehicle_id" => $vehicles_id,
            "cancel_status" => 0,
            "offerer" => $this->request->getVar('offerer'),
            "seatnumber" => $this->request->getVar('seatnumbers'),
            "totalseat" => $this->request->getVar('totalseat'),
            "roundtrip_discount" => 0,
            "free_luggage_kg" => $this->request->getVar('free_luggage_kg'),
            "paid_max_luggage_pcs" => $this->request->getVar('paid_max_luggage_pcs'),
            "price_pcs" => $this->request->getVar('price_pcs'),
            "special_max_luggage_pcs" => $this->request->getVar('special_max_luggage_pcs'),
            "special_price_pcs" => $this->request->getVar('special_price_pcs'),
            "special_luggage" => $this->request->getVar('special_luggage'),
            "created_at" => $created_at ?? now(),
        );

        $validTicketbooking = array(
            "booking_id" => $rand,
            "trip_id" => $maitripid,
            "subtrip_id" => $subtripid,
            "passanger_id" => $userid,

            "pick_location_id" => $piclocation,
            "drop_location_id" => $droplocation,
            "pick_stand_id" => $this->request->getVar('pickstand'),
            "drop_stand_id" => $this->request->getVar('dropstand'),

            "price" => $this->request->getVar('totalprice'),
            "paidamount" => $this->request->getVar('grandtotal'),
            "seatnumber" => $this->request->getVar('seatnumbers'),
            "totalseat" => $this->request->getVar('totalseat'),
            "bookby_user_id" => 1,
            "journeydata" => $this->request->getVar('journeydate'),
            "pay_type_id" => $this->request->getVar('pay_method'),
            "payment_status" => $this->request->getVar('payment_status'),
            "vehicle_id" => $vehicles_id,
        );

        if ($this->validation->run($validTicketbooking, 'ticket')) {
            $this->db->transStart();
            $paymentStatus = $this->request->getVar('payment_status');

            if ($paymentStatus == "unpaid") {
                $paidamount = 0;
            }

            if ($paymentStatus == "paid") {
                $paidamount = $this->request->getVar('grandtotal');
            }

            if ($paymentStatus == "partial") {
                $paidamount = $this->request->getVar('partialpay');
            }


            $partialPaid = array(
                "booking_id" => $rand,
                "trip_id" => $maitripid,
                "subtrip_id" => $subtripid,
                "passanger_id" => $userid,
                "paidamount" => $paidamount,
                "pay_type_id" => $this->request->getVar('pay_method'),
            );
            $paidpartial = array(
                "booking_id" => $rand,
                "trip_id" => $maitripid,
                "subtrip_id" => $subtripid,
                "passanger_id" => $userid,
                "paidamount" => $paidamount,
                "pay_type_id" => $this->request->getVar('pay_method'),
                "payment_detail" => $this->request->getVar('paydetail'),
            );

            $this->ticketModel->insert($ticketbooking);


            if ($this->validation->run($partialPaid, 'partialpay')) {

                $this->partialpaidModel->insert($paidpartial);

                $pick_stand_id = $this->request->getVar('pickstand');
                $drop_stand_id = $this->request->getVar('dropstand');

                $journeylist = $this->journeylist($rand, $userid, $maitripid, $subtripid, $piclocation, $droplocation, $pick_stand_id, $drop_stand_id);

                $userRole = $this->session->get('role_id');



                if (($userRole == 2) && ($paymentStatus != "unpaid")) {
                    $totalprice =  $paidamount;
                    $type = "income";
                    $message = "For Ticket Booking";

                    $agentIncome = agentIncomeCommission($backUserId, $totalprice, $rand, $subtripid, $userid, $message);

                    $agentTotalIncome =   agentTotal($backUserId, $totalprice, $rand, $type, $payment_detail_rocord);
                }
            }

            if ($paymentStatus != "unpaid") {
                $paymethod_id = $this->request->getVar('pay_method');
                $payDetail = $this->request->getVar('paydetail');
                $type = "income";
                $detail = "Ticket Booking (" . $rand . ") ";
                accoutTranjection($type, $detail, $paidamount, $backUserId);
                paymethodTeanjection($rand, $paymethod_id, $paidamount, $payDetail, $maitripid, $subtripid, $backUserId);

                $couponcode = $this->request->getVar('offerer');

                if (!empty($couponcode)) {
                    $validDetail = $this->couponModel->where('code', $couponcode)->findAll();


                    $coupondetail = array(
                        "code" => $couponcode,
                        "coupon_id" => $validDetail[0]->id,
                        "booking_id" => $rand,
                        "subtrip_id" => $subtripid,
                        "amount" => $validDetail[0]->discount,

                    );

                    $this->coupondiscountModel->insert($coupondetail);
                }
            }

            $this->db->transComplete();
            $emaildata = $ticketmailLibrary->getticketEmailData($rand);
            // dd($emaildata);
            $status = sendTicket($login_email, $emaildata);

            if ($status == true) {
                return redirect()->route('allbookinglist-ticket')->with("success", "Ticket created successfully");
            }
        }

        return redirect()->route('new-ticket')->with("fail", $this->validation->listErrors());
    }

    public function userCheck($login_mobile, $login_email = null)
    {
        $userid = null;
        $evalue = $this->userModel->where('login_email', $login_email)->findAll();
        $mvalue = $this->userModel->where('login_mobile', $login_mobile)->findAll();
        // var_dump($evalue,$mvalue); die();
        if (!empty($evalue) || !empty($mvalue)) {
            if ($evalue) {
                foreach ($evalue as $key => $mobilevalue) {
                    $userid = $mobilevalue->id;
                }
                if ($evalue && $login_mobile) {
                    $this->userModel->where('id', $userid)->set(['login_mobile' => $login_mobile])->update();
                }
                return $userid;
            }
            if ($mvalue) {
                foreach ($mvalue as $key => $emailvalue) {
                    $userid = $emailvalue->id;
                }
                if ($mvalue && $login_email) {
                    $this->userModel->where('id', $userid)->set(['login_email' => $login_email])->update();
                }
                return $userid;
            }
            
        } else {
            $status = 1;
            $role_id = 3;
            $slug = bin2hex(random_bytes(5));
            $password = $confirm = "123456";


            $userData = array(
                "login_email" => $login_email,
                "login_mobile" => $login_mobile,
                "password" => $password,
                "confirm" => $confirm,
                "slug" => $slug,
                "role_id" => $role_id,
                "status" => $status,
            );
            $validdata = array(
                "first_name" => $this->request->getVar('first_name'),
                "id_type" => $this->request->getVar('id_type') ?: null,
                "id_number" => $this->request->getVar('id_number') ?: null,
            );

            if ($this->validation->run($userData, 'user') && $this->validation->run($validdata, 'userDetail')) {
                $this->db->transStart();

                $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
                $userid = $this->userModel->insert($userData);

                $data = array(
                    "user_id" => $userid,
                    "first_name" => $this->request->getVar('first_name'),
                    "last_name" => $this->request->getVar('last_name'),
                    "id_type" => $this->request->getVar('id_type') ?: null,
                    "country_id" => $this->request->getVar('country_id') ?? null,
                    "id_number" => $this->request->getVar('id_number') ?: null,
                    "address" => $this->request->getVar('address') ?: null,
                    "city" => $this->request->getVar('city') ?: null,
                    "zip_code" => $this->request->getVar('zip_code') ?: null,
                );
                // dd($data);
                $this->userDetailModel->insert($data);

                $this->db->transComplete();
                return $userid;
            } else {
                return redirect()->route('new-ticket')->with("fail", $this->validation->listErrors());
            }
        }
    }

    public function journeylist($rand, $userid, $maitripid, $subtripid, $piclocation, $droplocation, $pick_stand_id, $drop_stand_id)
    {
        $journeydate = date("Y-m-d", strtotime($this->request->getVar('journeydate')));
        $joruneylistid = null;

        $mainpassanger = array(
            "booking_id" => $rand,
            "trip_id" => $maitripid,
            "subtrip_id" => $subtripid,
            "pick_location_id" => $piclocation,
            "drop_location_id" => $droplocation,
            "pick_stand_id" => $pick_stand_id,
            "drop_stand_id" => $drop_stand_id,
            "first_name" => $this->request->getVar('first_name'),
            "last_name" => $this->request->getVar('last_name'),
            "phone" => $this->request->getVar('login_mobile'),
            "journeydate" => $journeydate,
            "id_number" => $this->request->getVar('id_number'),
        );

        if ($this->validation->run($mainpassanger, 'journeylist')) {
            $joruneylistid = $this->journeylistModel->insert($mainpassanger);
        }

        $newPassangerFName = $this->request->getVar('first_name_new');
        $newPassangerLName = $this->request->getVar('last_name_new');
        $newPassangerMobile = $this->request->getVar('login_mobile_new');
        $newPassangerNidNumber = $this->request->getVar('id_number_new');

        if (!empty($newPassangerFName)) {
            foreach ($newPassangerFName as $nkey => $newpassanger) {
                $newpassangerlist[$nkey] = array(

                    "booking_id" => $rand,
                    "trip_id" => $maitripid,
                    "subtrip_id" => $subtripid,
                    "pick_location_id" => $piclocation,
                    "drop_location_id" => $droplocation,
                    "pick_stand_id" => $pick_stand_id,
                    "drop_stand_id" => $drop_stand_id,
                    "first_name" => $newPassangerFName[$nkey],
                    "last_name" => $newPassangerLName[$nkey],
                    "phone" => $newPassangerMobile[$nkey],
                    "journeydate" => $journeydate,
                    "id_number" => $newPassangerNidNumber[$nkey],

                );
            }



            $this->journeylistModel->insertBatch($newpassangerlist);
        }


        return   $joruneylistid;
    }

    public function allbookinglist()
    {
        $rolepermissionLibrary = new Rolepermission();
        $refund_action = "refund_list";
        $cancel_action = "cancel_list";

        $data['refund_create'] = $rolepermissionLibrary->create($refund_action);
        $data['cancel_create'] = $rolepermissionLibrary->create($cancel_action);

        $pickdrops =  $this->picdropModel
            ->select('stands.id as standId,stands.*,pickdrops.*')
            ->join('stands', 'stands.id = pickdrops.stand_id')
            ->withDeleted()
            ->findAll();
        // dd($pickdrops);

        $data['ticket'] = $this->filterBooking();
        $data['location'] =  $this->locationModel->withDeleted()->findAll();
        $data['pickdropstand'] =  $pickdrops;
        $data['paymethod'] = $this->paymethodModel->where('status', 1)->findAll();

        $data['module'] =    lang("Localize.ticket_booking");
        $data['title']  =    lang("Localize.ticket_list");
        $data['pageheading'] = lang("Localize.ticket_list");
        $data['websetting'] = $this->websettingModel->first();

        return view($this->Viewpath . '\ticket\bookinglist', $data);
    }

    public function filterBooking()
    {
        $userRole = $this->session->get('role_id');
        $userId = $this->session->get('user_id');

        if ($userRole != 1) {
            // user role is agent or employee
            // list only user booking lists
            $this->ticketModel->where('bookby_user_id', $userId);
        }

        $this->ticketModel->where('cancel_status', 0);
        $this->ticketModel->where('refund', 0);

        $this->ticketModel->select('tickets.*,CONCAT(user_details.first_name," ",user_details.last_name) as passengerName');
        $this->ticketModel->select('(SELECT COUNT(*) FROM ticket_tags WHERE ticket_tags.ticket_id = tickets.id) AS tagCount', false);
        $this->ticketModel->join("user_details", "user_details.user_id = tickets.passanger_id");
        $this->ticketModel->orderBy('id', 'DESC');
        $filterTicket = $this->ticketModel->withDeleted()->findAll();
        // dd($filterTicket);

        return $filterTicket;
    }

    public function agentCommission($totalprice)
    {
        $userId = $this->session->get('user_id');
        $agetnDetail =  $this->agentModel->where('user_id', $userId)->first();
        (float)$commission = (float)(($totalprice) * ($agetnDetail->commission / 100));
        return $commission;
    }


    public function roundfindtrip()
    {
        $websetting = $this->websettingModel->first();

        $pick_location_id = $this->session->get('drop_location_id');
        $drop_location_id = $this->session->get('pick_location_id');
        $filterjourneydate = $this->session->get('filterreturndate');


        // build journey date
        $journeyDate = date('Y-m-d', strtotime($filterjourneydate));
        $journeyDayOfWeek = date('N', strtotime($filterjourneydate));

        $fleet_id = $this->session->get('fleet_id');

        $monthcomparedate =  date('Y-m-d', strtotime('+1 month'));

        // if ($filterjourneydate > $monthcomparedate) {
        //     return redirect()->route('new-ticket')->with("fail", "No advance booking for this day");
        // }

        $data = array('trips' => [], 'in_holiday' => [], 'to_open' => [], 'inactive' => [], 'you_may_like' => []);
        $data['pick_location_id'] = $pick_location_id;
        $data['drop_location_id'] = $drop_location_id;
        $data['filterjourneydate'] = $filterjourneydate;
        $data['filterreturndate'] = "";
        $data['fleet_id'] = $fleet_id;

        $data['filterpath'] = $this->Viewpath;
        $data['location'] = $this->locationModel->findAll();
        $data['fleet_type'] = $this->fleetTypeModel->findAll();

        $timeForTimezone = $websetting->timezone;
        $timezone = new \DateTimeZone($timeForTimezone);
        $date = new \DateTime('now', $timezone);
        $fDate = $date->format('Y-m-d');
        $fTime = $date->format('h:i A');

        $data['free_luggage_kg'] = $this->session->get('free_luggage_kg');
        $data['paid_max_luggage_pcs'] = $this->session->get('paid_max_luggage_pcs');
        $data['price_pcs'] = $this->session->get('price_pcs');
        $data['special_max_luggage_pcs'] = $this->session->get('special_max_luggage_pcs');
        $data['special_price_pcs'] = $this->session->get('special_price_pcs');
        $data['special_luggage'] = $this->session->get('special_luggage');


        $allSubTrips = $this->getAllTrip($pick_location_id, $drop_location_id, $fDate, $fTime, $journeyDate, $fleet_id);

        foreach ($allSubTrips as $subTrip) {
            if ($subTrip->status == 0) {
                // subtrip is inactive
                $data['inactive'][] = $subTrip;
                continue;
            }

            if (strtotime($journeyDate) < strtotime($subTrip->startdate)) {
                // subtrip yet not begin
                $data['to_open'][] = $subTrip;
                continue;
            }

            if (@in_array($journeyDayOfWeek, explode(',', $subTrip->weekend))) {
                // subtrip is on holiday
                $data['in_holiday'][] = $subTrip;
                continue;
            }

            if (($subTrip->pick_location_id != $pick_location_id) || ($subTrip->drop_location_id != $drop_location_id)) {
                // subtrip is related to selected locations
                $data['you_may_like'][] = $subTrip;
                continue;
            }

            $data['trips'][] = $subTrip;
        }

        $data['taxtype'] = $websetting->tax_type;
        $data['rountripStatus'] = 1;

        $data['module'] =    lang("Localize.ticket_booking");
        $data['title']  =    lang("Localize.book_ticket");

        $heading = lang("Localize.book") . ' ' . lang("Localize.ticket");
        $data['pageheading'] = $heading;
        $data['triptype'] = lang("Localize.round") . ' ' . lang("Localize.trip");
        $data['isrountrip'] = 1;
        $data['totalseat'] = $this->session->get('totalseat');

        $isroundtrip['isrountrip'] = 1;

        $this->session->set($isroundtrip);

        return view($this->Viewpath . '\ticket\index', $data);
    }


    public function roundcreate()
    {
        $ticketmailLibrary = new Ticketmail();

        //single Trip Ticket Booking
        $rand = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 8);
        $rand = "TB" . $rand;
        $maitripid = null;
        $subtripid = null;
        $piclocation = null;
        $droplocation = null;
        $vehicles_id = null;
        $backUserId = $this->session->get('user_id');
        $subTripId = $this->session->get('subtripId');
        $payment_detail_rocord = $this->request->getVar('paydetail');

        $discount = $this->request->getVar('discount');

        if ((!empty($discount)) && $discount > 0) {
            $getDiscount = (float) $discount / 2;
        } else {
            $getDiscount = 0;
        }
        $websetting = $this->websettingModel->first();
        if ($websetting) {
            $timeForTimezone = $websetting->timezone;
            $timezone = new \DateTimeZone($timeForTimezone);
            $date = new \DateTime('now', $timezone);
            $created_at = $date->format('Y-m-d H:i:s');
        }


        $subtrips = $this->subtripModel->select('trips.id as tripid,trips.*,fleets.*,schedules.*,vehicles.*,subtrips.id as subtripId,subtrips.*')
            ->join('trips', 'trips.id = subtrips.trip_id')
            ->join('fleets', 'fleets.id = trips.fleet_id')
            ->join('schedules', 'schedules.id = trips.schedule_id')
            ->join('vehicles', 'vehicles.id = trips.vehicle_id')
            ->where('subtrips.status', 1)
            ->where('subtrips.id', $subTripId)
            ->findAll();


        foreach ($subtrips as $key => $value) {
            $maitripid = $value->tripid;
            $subtripid = $value->id;
            $piclocation = $value->pick_location_id;
            $droplocation = $value->drop_location_id;
            $vehicles_id = $value->vehicle_id;
        }


        $login_email = $this->request->getVar('login_email');
        $login_mobile = $this->request->getVar('login_mobile');

        $userid = $this->userCheck($login_mobile, $login_email ?? null);

        $loginUserId = $this->session->get('user_id');

        $bookUserId = $this->session->get('role_id');

        $roelinfo = $this->roleModel->find($bookUserId);

        $bookUserType = $roelinfo->name;

        $pay_method_id = $this->request->getVar('pay_method');

        $payment_status = $this->request->getVar('payment_status');
        $payment_detail = $this->request->getVar('paydetail');

        $ticketbooking = array(
            "booking_id" => $rand,
            "trip_id" => $maitripid,
            "subtrip_id" => $subtripid,
            "passanger_id" => $userid,
            "pick_location_id" => $piclocation,
            "drop_location_id" => $droplocation,
            "pick_stand_id" => $this->session->get('pickstand'),
            "drop_stand_id" => $this->session->get('dropstand'),
            "price" => $this->session->get('totalprice'),
            "discount" =>  $getDiscount,
            "roundtrip_discount" =>  $this->session->get('roundtrip_discount'),
            "totaltax" => $this->session->get('tax'),
            "paidamount" => $this->session->get('grandtotal'),
            "adult" => $this->session->get('aseat'),
            "chield" => $this->session->get('cseat'),
            "special" => $this->session->get('spseat'),
            "refund" => 0,
            "bookby_user_id" =>  $loginUserId,
            "bookby_user_type" => $bookUserType,
            "journeydata" => $this->session->get('journeydate'),
            "pay_type_id" =>  $pay_method_id,
            // "payment_status" => $payment_status,
            "payment_detail" => $payment_detail,
            "vehicle_id" => $vehicles_id,
            "cancel_status" => 0,

            "offerer" => $this->session->get('offerer'),
            // "offerer" => 0,
            "seatnumber" => $this->session->get('seatnumbers'),
            "totalseat" => $this->session->get('totalseat'),
            "free_luggage_kg" => $this->session->get('free_luggage_kg') ?? 0.00,
            "paid_max_luggage_pcs" => $this->session->get('paid_max_luggage_pcs'),
            "price_pcs" => $this->session->get('price_pcs'),
            "special_max_luggage_pcs" => $this->session->get('special_max_luggage_pcs'),
            "special_price_pcs" => $this->session->get('special_price_pcs'),
            "special_luggage" => $this->session->get('special_luggage'),
            "created_at" => $created_at ?? now(),
        );

        $validTicketbooking = array(

            "booking_id" => $rand,
            "trip_id" => $maitripid,
            "subtrip_id" => $subtripid,
            "passanger_id" => $userid,

            "pick_location_id" => $piclocation,
            "drop_location_id" => $droplocation,
            "pick_stand_id" => $this->session->get('pickstand'),
            "drop_stand_id" => $this->session->get('dropstand'),

            "price" => $this->request->getVar('totalprice'),
            "paidamount" => $this->request->getVar('grandtotal'),
            "seatnumber" => $this->request->getVar('seatnumbers'),
            "totalseat" => $this->session->get('totalprice'),
            "bookby_user_id" => 1,
            "journeydata" => $this->session->get('journeydate'),
            "pay_type_id" => $pay_method_id,
            "payment_status" => $payment_status,
            "vehicle_id" => $vehicles_id,

        );


        if ($this->validation->run($validTicketbooking, 'ticket')) {
            $this->db->transStart();

            $paymentStatus = $this->request->getVar('payment_status');

            if ($paymentStatus == "unpaid") {
                $singleTripPaymentStatus = "unpaid";
                $roundTripPaymentStatus = "unpaid";
                $paidamount = $this->session->get('grandtotal');
                $roundtripPaid = $this->request->getVar('grandtotal');
                $roundPaidamount = (float) $roundtripPaid - (float)$paidamount;
                $transectionSingeltrip = 0;
                $transectionDoubletrip = 0;
            }
            if ($paymentStatus == "paid") {
                $paidamount = $this->session->get('grandtotal');
                $roundtripPaid = $this->request->getVar('grandtotal');
                $roundPaidamount = (float) $roundtripPaid - (float)$paidamount;
                $singleTripPaymentStatus = "paid";
                $roundTripPaymentStatus = "paid";
                $transectionSingeltrip = $paidamount;
                $transectionDoubletrip =   $roundPaidamount;
            }
            if ($paymentStatus == "partial") {
                $partialamount = $this->request->getVar('partialpay');
                $singelTripamount = $this->session->get('grandtotal');
                if ($partialamount < $singelTripamount) {
                    $paidamount = $this->session->get('grandtotal');
                    $roundtripPaid = $this->request->getVar('grandtotal');
                    $roundPaidamount = (float) $roundtripPaid - (float)$paidamount;
                    $singleTripPaymentStatus = "partial";
                    $roundTripPaymentStatus = "unpaid";
                    $transectionSingeltrip = $partialamount;
                    $transectionDoubletrip = 0;
                }

                if ($partialamount == $singelTripamount) {

                    $paidamount = $this->session->get('grandtotal');
                    $roundtripPaid = $this->request->getVar('grandtotal');
                    $roundPaidamount = (float) $roundtripPaid - (float)$paidamount;
                    $singleTripPaymentStatus = "paid";
                    $roundTripPaymentStatus = "unpaid";
                    $transectionSingeltrip = $partialamount;
                    $transectionDoubletrip = 0;
                }

                if ($partialamount > $singelTripamount) {

                    $paidamount = $this->session->get('grandtotal');
                    $roundtripPaid = $this->request->getVar('grandtotal');
                    $roundPaidamount = (float) $roundtripPaid - (float)$paidamount;

                    $extraMoney = (float) $partialamount - (float) $paidamount;
                    $singleTripPaymentStatus = "paid";
                    $roundTripPaymentStatus = "partial";

                    $transectionSingeltrip = $paidamount;
                    $transectionDoubletrip = $extraMoney;
                }
            }



            $ticketbooking['payment_status'] = $singleTripPaymentStatus;
            $this->ticketModel->insert($ticketbooking);

            $partialPaid = array(
                "booking_id" => $rand,
                "trip_id" => $maitripid,
                "subtrip_id" => $subtripid,
                "passanger_id" => $userid,
                "paidamount" => $transectionSingeltrip,
                "pay_type_id" => $this->request->getVar('pay_method'),
            );
            $paidpartial = array(
                "booking_id" => $rand,
                "trip_id" => $maitripid,
                "subtrip_id" => $subtripid,
                "passanger_id" => $userid,
                "paidamount" => $transectionSingeltrip,
                "pay_type_id" => $this->request->getVar('pay_method'),
                "payment_detail" => $payment_detail,
            );

            if ($this->validation->run($partialPaid, 'partialpay')) {
                $this->partialpaidModel->insert($paidpartial);
                $pick_stand_id = $this->session->get('pickstand');
                $drop_stand_id = $this->session->get('dropstand');

                $journeylist = $this->journeylist($rand, $userid, $maitripid, $subtripid, $piclocation, $droplocation, $pick_stand_id, $drop_stand_id);
                $userRole = $this->session->get('role_id');

                if (($userRole == 2) && ($singleTripPaymentStatus != "unpaid")) {
                    $totalprice =  $transectionSingeltrip;
                    $type = "income";
                    $message = "For Ticket Booking";
                    $agentIncome = agentIncomeCommission($backUserId, $totalprice, $rand, $subtripid, $userid, $message);
                    $agentTotalIncome = agentTotal($backUserId, $totalprice, $rand, $type, $payment_detail_rocord);
                }
            }


            if ($singleTripPaymentStatus != "unpaid") {
                $paymethod_id = $this->request->getVar('pay_method');
                $payDetail = $this->request->getVar('paydetail');
                $type = "income";
                $detail = "Ticket Booking (" . $rand . ") ";
                accoutTranjection($type, $detail, $transectionSingeltrip, $backUserId);
                paymethodTeanjection($rand, $paymethod_id, $transectionSingeltrip, $payDetail, $maitripid, $subtripid, $backUserId);
            }


            $roundrand = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 8);
            $roundrand = "TB" . $roundrand;
            $maitripid = null;
            $subtripid = null;
            $piclocation = null;
            $droplocation = null;
            $vehicles_id = null;
            $backUserId = $this->session->get('user_id');
            $subTripId = $this->request->getVar('subtripId');
            $payment_detail_rocord = $this->request->getVar('paydetail');

            $subtrips = $this->subtripModel->select('trips.id as tripid,trips.*,fleets.*,schedules.*,vehicles.*,subtrips.id as subtripId,subtrips.*')
                ->join('trips', 'trips.id = subtrips.trip_id')
                ->join('fleets', 'fleets.id = trips.fleet_id')
                ->join('schedules', 'schedules.id = trips.schedule_id')
                ->join('vehicles', 'vehicles.id = trips.vehicle_id')
                ->where('subtrips.status', 1)
                ->where('subtrips.id', $subTripId)
                ->findAll();

            foreach ($subtrips as $key => $value) {
                $maitripid = $value->tripid;
                $subtripid = $value->id;
                $piclocation = $value->pick_location_id;
                $droplocation = $value->drop_location_id;
                $vehicles_id = $value->vehicle_id;
            }


            $roundticketbooking = array(
                "booking_id" => $roundrand,
                "trip_id" => $maitripid,
                "subtrip_id" => $subtripid,
                "passanger_id" => $userid,
                "pick_location_id" => $piclocation,
                "drop_location_id" => $droplocation,
                "pick_stand_id" => $this->request->getVar('pickstand'),
                "drop_stand_id" => $this->request->getVar('dropstand'),
                "price" => $this->request->getVar('totalprice'),
                "discount" =>  $getDiscount,
                "roundtrip_discount" =>  $this->request->getVar('roundtrip_discount'),
                "totaltax" => $this->request->getVar('tax'),
                "paidamount" =>  $roundPaidamount,
                "adult" => $this->request->getVar('aseat'),
                "chield" => $this->request->getVar('cseat'),
                "special" => $this->request->getVar('spseat'),
                "refund" => 0,
                "bookby_user_id" =>  $loginUserId,
                "bookby_user_type" => $bookUserType,
                "journeydata" => $this->request->getVar('journeydate'),
                "pay_type_id" => $this->request->getVar('pay_method'),
                // "payment_status" => $this->request->getVar('payment_status'),
                "payment_detail" => $this->request->getVar('paydetail'),
                "vehicle_id" => $vehicles_id,
                "cancel_status" => 0,

                "offerer" => $this->request->getVar('offerer'),
                // "offerer" => 0,
                "seatnumber" => $this->request->getVar('seatnumbers'),
                "totalseat" => $this->request->getVar('totalseat'),

            );

            $roundvalidTicketbooking = array(
                "booking_id" => $roundrand,
                "trip_id" => $maitripid,
                "subtrip_id" => $subtripid,
                "passanger_id" => $userid,

                "pick_location_id" => $piclocation,
                "drop_location_id" => $droplocation,
                "pick_stand_id" => $this->request->getVar('pickstand'),
                "drop_stand_id" => $this->request->getVar('dropstand'),

                "price" => $this->request->getVar('totalprice'),
                "paidamount" =>  $roundPaidamount,
                "seatnumber" => $this->request->getVar('seatnumbers'),
                "totalseat" => $this->request->getVar('totalseat'),
                "bookby_user_id" => 1,
                "journeydata" => $this->request->getVar('journeydate'),
                "pay_type_id" => $this->request->getVar('pay_method'),
                "payment_status" => $this->request->getVar('payment_status'),
                "vehicle_id" => $vehicles_id,

            );

            if ($this->validation->run($roundvalidTicketbooking, 'ticket')) {
                $partialPaid = array(
                    "booking_id" => $roundrand,
                    "trip_id" => $maitripid,
                    "subtrip_id" => $subtripid,
                    "passanger_id" => $userid,
                    "paidamount" => $transectionDoubletrip,
                    "pay_type_id" => $this->request->getVar('pay_method'),
                );

                $paidpartial = array(
                    "booking_id" => $roundrand,
                    "trip_id" => $maitripid,
                    "subtrip_id" => $subtripid,
                    "passanger_id" => $userid,
                    "paidamount" => $transectionDoubletrip,
                    "pay_type_id" => $this->request->getVar('pay_method'),
                    "payment_detail" => $this->request->getVar('paydetail'),
                );


                $roundticketbooking['payment_status'] = $roundTripPaymentStatus;
                $this->ticketModel->insert($roundticketbooking);


                if ($this->validation->run($partialPaid, 'partialpay')) {

                    $this->partialpaidModel->insert($paidpartial);

                    $pick_stand_id = $this->request->getVar('pickstand');
                    $drop_stand_id = $this->request->getVar('dropstand');

                    $journeylist = $this->journeylist($roundrand, $userid, $maitripid, $subtripid, $piclocation, $droplocation, $pick_stand_id, $drop_stand_id);

                    $userRole = $this->session->get('role_id');



                    if (($userRole == 2) && ($roundTripPaymentStatus != "unpaid")) {
                        $totalprice =  $transectionDoubletrip;
                        $type = "income";
                        $message = "For Ticket Booking";

                        $agentIncome = agentIncomeCommission($backUserId, $totalprice, $roundrand, $subtripid, $userid, $message);

                        $agentTotalIncome =   agentTotal($backUserId, $totalprice, $roundrand, $type, $payment_detail_rocord);
                    }
                }
            }

            if ($roundTripPaymentStatus != "unpaid") {
                $paymethod_id = $this->request->getVar('pay_method');
                $payDetail = $this->request->getVar('paydetail');
                $type = "income";
                $detail = "Ticket Booking (" . $roundrand . ") ";
                accoutTranjection($type, $detail, $transectionDoubletrip, $backUserId);
                paymethodTeanjection($rand, $paymethod_id, $transectionDoubletrip, $payDetail, $maitripid, $subtripid, $backUserId);
            }

            $couponcode = $this->request->getVar('offerer');
            if (!empty($couponcode)) {
                $validDetail = $this->couponModel->where('code', $couponcode)->findAll();


                $coupondetail = array(
                    "code" => $couponcode,
                    "coupon_id" => $validDetail[0]->id,
                    "booking_id" => $rand,
                    "subtrip_id" => $subtripid,
                    "amount" => $validDetail[0]->discount,

                );

                $coupondetailround = array(
                    "code" => $couponcode,
                    "coupon_id" => $validDetail[0]->id,
                    "booking_id" => $roundPaidamount,
                    "subtrip_id" => $subtripid,
                    "amount" => $validDetail[0]->discount,

                );

                $this->coupondiscountModel->insert($coupondetail);
                $this->coupondiscountModel->insert($coupondetailround);
            }


            $emaildata = $ticketmailLibrary->getticketEmailData($rand);
            $roundemaildata = $ticketmailLibrary->getticketEmailData($roundrand);
            $this->db->transComplete();
            $status = sendTicket($login_email, $emaildata);
            $rouondstatus = sendTicket($login_email, $roundemaildata);
            $status = true;
            $rouondstatus = true;

            if (($status == true) && ($rouondstatus == true)) {
                $this->session->remove('subtripId');
                $this->session->remove('seatnumbers');
                $this->session->remove('totalseat');
                $this->session->remove('journeydate');
                $this->session->remove('aseat');
                $this->session->remove('spseat');
                $this->session->remove('cseat');
                $this->session->remove('totalprice');
                $this->session->remove('tax');
                $this->session->remove('grandtotal');
                $this->session->remove('pickstand');
                $this->session->remove('dropstand');
                $this->session->remove('isrountrip');
                $this->session->remove('discountPercent');
                $this->session->remove('roundtrip_discount');
                return redirect()->route('allbookinglist-ticket')->with("success", "Ticket Booking");
            }
        }

        return redirect()->route('new-ticket')->with("fail", $this->validation->getError());
    }

    public function generateTag()
    {
        // var_dump($this->request->getVar('ticket_id'));

        $ticketInfo = $this->ticketModel->find($this->request->getVar('ticket_id'));
        $rand = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 8);
        $ticket_tag = $ticketInfo->booking_id . "-" . $rand;

        $paid_max_luggage_pcs = $ticketInfo->paid_max_luggage_pcs ?  (int)$ticketInfo->paid_max_luggage_pcs : 0;
        $special_max_luggage_pcs = $ticketInfo->special_max_luggage_pcs ?  (int)$ticketInfo->special_max_luggage_pcs : 0;
        $totalTagCount = $paid_max_luggage_pcs + $special_max_luggage_pcs;

        // Insert tags into the tagModel
        for ($i = 1; $i <= $totalTagCount; $i++) {
            $tag = $ticket_tag . "_" . $i;

            // Assuming you have a method in your tagModel to insert tags
            $ticketTag = $this->tagModel->insert([
                'tag' => $tag,
                'ticket_id' => $this->request->getVar('ticket_id'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        if (isset($ticketTag)) {
            $data = [
                'status' => "success",
                'response' => 200,
                'message' => 'Ticket Tag Generated',
            ];
        } else {
            $data = [
                'status' => "error",
                'response' => 500,
                'message' => 'Ticket Tag Not Generated',
            ];
        }

        return $this->response->setJSON($data);
    }

    public function showTag($ticket_id)
    {
        $this->ticketModel->select('tickets.*,user_details.*,users.*,CONCAT(user_details.first_name," ",user_details.last_name) as passengerName');
        $this->ticketModel->join("user_details", "user_details.user_id = tickets.passanger_id");
        $this->ticketModel->join("users", "users.id = user_details.user_id");
        $this->ticketModel->orderBy('tickets.id', 'DESC');
        $data['ticket'] = $this->ticketModel->find($ticket_id);

        $data['tags'] = $this->tagModel->where('ticket_id', $ticket_id)->findAll();
        $data['module'] =    lang("Localize.ticket_booking");
        $data['title']  =    lang("Localize.ticket_tag_list");
        $data['pageheading'] = lang("Localize.ticket_tag_list");

        // dd($data['ticket']);
        // dd($data['tags']);
        return view($this->Viewpath . '\ticket\showtag', $data);
    }
    // public function sendMail(){
    //     $email = \Config\Services::email();

    //     $config['protocol'] = 'mail';
    //     $config['mailType'] = 'html';
    //     $config['charset'] = 'UTF-8';
    //     $config['validate'] = false;

    //     $email->initialize($config);
    //     // dd($email->initialize($config));

    //     $to = "hoquea2022@gmail.com";
    //     $subject = "Ticket Booking Details";
    //     $message = "Hello, this is the email content."; // Replace with your actual content

    //     $email->setTo($to);
    //     $email->setFrom('hoquea57@gmail.com', 'Ticket Booking');
    //     $email->setSubject($subject);
    //     $email->setMessage($message);


    //     if ($email->send()) {
    //         return "Email sent successfully";
    //     } else {
    //         echo "<pre>";
    //         print_r($email->printDebugger(['headers'])); 
    //         log_message('error', $email->printDebugger(['headers']));
    //         return "Failed to send email. Check logs for more details.";
    //         // return false;
    //     }
    // }

    public function sendMail()
    {
        $email = \Config\Services::email();

        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'sandbox.smtp.mailtrap.io',
            'smtp_port' => 2525,
            'smtp_user' => 'e99662bca3997c',
            'smtp_pass' => 'b3d14564c43550',
            'crlf' => "\r\n",
            'newline' => "\r\n"
        );

        $email->initialize($config);

        $to = "hoquea57@gmail.com";
        $subject = "Test Email from CodeIgniter on WAMP";
        $message = "This is a test email from CodeIgniter running on WAMP server.";

        $email->setTo($to);
        $email->setFrom('hoquea2022@gmail.com', 'Your Name');
        $email->setSubject($subject);
        $email->setMessage($message);

        if ($email->send()) {
            return "Email sent successfully";
        } else {
            echo "<pre>";
            print_r($email->printDebugger(['headers']));
            log_message('error', $email->printDebugger(['headers']));
            return "Failed to send email. Check logs for more details.";
        }
    }
}
