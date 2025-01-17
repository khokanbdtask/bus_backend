<?php

namespace Modules\Passanger\Controllers\Api;

use App\Controllers\BaseController;
use Modules\User\Models\UserModel;
use Modules\User\Models\UserDetailModel;
use Modules\Role\Models\RoleModel;
use Modules\Ticket\Models\TicketModel;
use Modules\Schedule\Models\ScheduleModel;
use Modules\Trip\Models\TripModel;
use Modules\Rating\Models\RatingModel;
use Modules\Passanger\Models\Socialsignin;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Exception;
use App\Libraries\Tokenjwt;
use Modules\Trip\Models\FacilityModel;
use Modules\Trip\Models\SubtripModel;

class Passanger extends BaseController
{
    use ResponseTrait;
    protected $Viewpath;
    protected $userModel;
    protected $userDetailModel;
    protected $roleModel;
    protected $tokenJwt;
    protected $ticketModel;
    protected $scheduleModel;
    protected $tripModel;
    protected $ratingModel;
    protected $socialsigninModel;
    protected $facilitypModel;
    protected $subtripModel;


    public function __construct()
    {

        $this->Viewpath = "Modules\Passanger\Views";
        $this->userModel = new UserModel();
        $this->userDetailModel = new UserDetailModel();
        $this->roleModel = new RoleModel();
        $this->db = \Config\Database::connect();

        $this->tokenJwt = new Tokenjwt();
        $this->ticketModel = new TicketModel();

        $this->scheduleModel = new ScheduleModel();
        $this->tripModel = new TripModel();

        $this->ratingModel = new RatingModel();

        $this->socialsigninModel = new Socialsignin();
        $this->facilitypModel = new FacilityModel();
        $this->subtripModel = new SubtripModel();
    }
    public function getPassangerdata($segment, $type)

    {
        $userdata = array();
        if ($type == "email") {
            $userdetail = $this->userModel->join('user_details', 'user_details.user_id = users.id', 'left')->where('role_id', 3)->where('status', 1)->where('login_email', $segment)->findAll();
        }
        if ($type == "mobile") {
            $userdetail = $this->userModel->join('user_details', 'user_details.user_id = users.id', 'left')->where('role_id', 3)->where('status', 1)->where('login_mobile', $segment)->findAll();
        }

        if (empty($userdetail)) {
            $data = [
                'message' => "No Data not found.",
                'status' => "fail",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        } else {
            foreach ($userdetail as $key => $uservalue) {
                $userdata['user_id'] = $uservalue->user_id;
                $userdata['login_email'] = $uservalue->login_email;
                $userdata['login_mobile'] = $uservalue->login_mobile;
                $userdata['slug'] = $uservalue->slug;
                $userdata['status'] = $uservalue->status;
                $userdata['first_name'] = $uservalue->first_name;
                $userdata['last_name'] = $uservalue->last_name;
                $userdata['id_number'] = $uservalue->id_number;
                $userdata['id_type'] = $uservalue->id_type;
                $userdata['address'] = $uservalue->address;
                $userdata['country_id'] = $uservalue->country_id;
                $userdata['city'] = $uservalue->city;
                $userdata['zip_code'] = $uservalue->zip_code;
            }
            $data = [
                'status' => "success",
                'response' => 200,
                'data' => $userdata,
            ];

            return $this->response->setJSON($data);
        }
    }

    public function getPassanger()
    {


        $segment    = $this->request->getVar('userid');
        $password = $this->request->getVar('password');
        $type = $this->request->getVar('type');




        if ($type == "email") {
            $userdetail = $this->userModel->join('user_details', 'user_details.user_id = users.id', 'left')->where('role_id', 3)->where('status', 1)->where('login_email', $segment)->first();
        }
        if ($type == "mobile") {
            $userdetail = $this->userModel->join('user_details', 'user_details.user_id = users.id', 'left')->where('role_id', 3)->where('status', 1)->where('login_mobile', $segment)->first();
        }

        if ($userdetail) {
            $pass = $userdetail->password;
            $verify_pass = password_verify($password, $pass);



            if ($verify_pass) {


                $token = $this->tokenJwt->generateToken($userdetail->slug);



                $data = [
                    'status' => "success",
                    'response' => 200,
                    'data' => $token,
                ];

                return $this->response->setJSON($data);
            } else {
                $data = [
                    'message' => "Password or User Name Not Match",
                    'status' => "fail",
                    'response' => 204,

                ];
                return $this->response->setJSON($data);
            }
        } else {
            $data = [
                'message' => "User Name Not Match",
                'status' => "fail",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        }
    }



    public function getPassangerinfo()

    {
        $key = getenv('TOKEN_SECRET');
        $token = $this->tokenJwt->tokencheck();




        try {
            $decoded = JWT::decode($token, $key, array("HS256"));

            $userdetail = $this->userModel->join('user_details', 'user_details.user_id = users.id', 'left')->where('role_id', 3)->where('status', 1)->where('slug', $decoded->slug)->findAll();

            foreach ($userdetail as $key => $uservalue) {
                $userdata['user_id'] = $uservalue->user_id;
                $userdata['login_email'] = $uservalue->login_email;
                $userdata['login_mobile'] = $uservalue->login_mobile;
                $userdata['slug'] = $uservalue->slug;
                $userdata['status'] = $uservalue->status;
                $userdata['first_name'] = $uservalue->first_name;
                $userdata['last_name'] = $uservalue->last_name;
                $userdata['id_number'] = $uservalue->id_number;
                $userdata['id_type'] = $uservalue->id_type;
                $userdata['address'] = $uservalue->address;
                $userdata['country_id'] = $uservalue->country_id;
                $userdata['city'] = $uservalue->city;
                $userdata['zip_code'] = $uservalue->zip_code;
                if (!empty($uservalue->image)) {
                    $userdata['image'] = base_url() . '/public/' . $uservalue->image;
                } else {
                    $userdata['image'] = null;
                }
            }

            $data = [
                'status' => "success",
                'response' => 200,
                'data' => $userdata,
            ];

            return $this->response->setJSON($data);
        } catch (Exception $ex) {
            $data = [
                'status' => "fail",
                'response' => 201,
                'data' => "token not valid",
            ];
            return $this->response->setJSON($data);
        }
    }


    public function getTickets()
    {
        $key = getenv('TOKEN_SECRET');
        $token = $this->tokenJwt->tokencheck();

        try {
            // check and validate user token
            $decoded = JWT::decode($token, $key, array("HS256"));
            $userdetail = $this->userModel->where('slug', $decoded->slug)->first();

            // get tickets
            $ticketlist = $this->ticketModel
                // select columns
                ->select('tickets.*')
                ->select('l1.name AS pick_location_name, l2.name AS drop_location_name')
                ->select('s1.name AS pick_stand_name, s2.name AS drop_stand_name')
                ->select('pd1.time AS pick_stand_time, pd2.time AS drop_stand_time')

                // join with locations
                ->join('locations l1', 'tickets.pick_location_id = l1.id', 'left')
                ->join('locations l2', 'tickets.drop_location_id = l2.id', 'left')
                ->join('pickdrops pd1', 'tickets.pick_stand_id = pd1.id', 'left')
                ->join('pickdrops pd2', 'tickets.drop_stand_id = pd2.id', 'left')
                ->join('stands s1', 'pd1.stand_id = s1.id', 'left')
                ->join('stands s2', 'pd2.stand_id = s2.id', 'left')

                // select rows
                ->where('passanger_id', $userdetail->id)
                ->orderBy('tickets.id', 'DESC')
                ->findAll();

            if (empty($ticketlist)) {
                // ticket list is empty
                $data = [
                    'status' => "fail",
                    'response' => 201,
                    'data' => "No ticket found",
                ];
                return $this->response->setJSON($data);
            }
            // var_dump($ticketlist);exit;
            foreach ($ticketlist as $key => $ticketvalue) {
                $gettripdata =  $this->tripModel
                ->select('trips.*, l_p.name AS pl_name, l_d.name AS dl_name, sc.start_time, sc.end_time')
                ->join('locations l_p', 'trips.pick_location_id = l_p.id', 'left')
                ->join('locations l_d', 'trips.drop_location_id = l_d.id', 'left')
                ->join('schedules sc', 'trips.schedule_id = sc.id', 'left')
                ->withDeleted()
                ->find($ticketvalue->trip_id);
  
                $travelartripdata = $this->subtripModel
                    ->select('subtrips.*, l_p.name AS pl_name, l_d.name AS dl_name')
                    ->join('locations l_p', 'subtrips.pick_location_id = l_p.id')
                    ->join('locations l_d', 'subtrips.drop_location_id = l_d.id')
                    ->withDeleted()
                    ->find($ticketvalue->subtrip_id);

                $tipdetil = $this->tripModel->where('id', $ticketvalue->trip_id)->first();

                $facility = "no ficility";

                if ($facilities = $tipdetil->facility) {
                    // facility exists
                    // explode comma separated facilities
                    $facilityArr = explode(",", $facilities);
                    $facilityNameArr = [];

                    foreach ($facilityArr as $facility) {
                        $facilityInfo = $this->facilitypModel->select('name')->withDeleted()->find($facility);
                        $facilityNameArr[] = $facilityInfo->name;
                    }

                    $facility = implode(", ", $facilityNameArr);
                }

                $scheduldetail = $this->scheduleModel->where('id', $tipdetil->schedule_id)->first();

                $reviewStatus = 0;
                $rating = $this->ratingModel->where('booking_id', $ticketvalue->booking_id)->first();

                // Journey date
                $journeyDay = date('Y-m-d', strtotime($ticketvalue->journeydata));
                $bookingDate = date('Y-m-d', strtotime($ticketvalue->created_at));

                if (!empty($rating)) {
                    $reviewStatus = 1;
                }

                $ticketdata[$key]['id'] = $ticketvalue->id;
                $ticketdata[$key]['booking_id'] = $ticketvalue->booking_id;
                $ticketdata[$key]['trip_id'] = $ticketvalue->trip_id;
                $ticketdata[$key]['subtrip_id'] = $ticketvalue->subtrip_id;
                $ticketdata[$key]['passanger_id'] = $ticketvalue->passanger_id;
                $ticketdata[$key]['pick_location_id'] = $ticketvalue->pick_location_id;
                $ticketdata[$key]['pick_location_name'] = $ticketvalue->pick_location_name;
                $ticketdata[$key]['drop_location_id'] = $ticketvalue->drop_location_id;
                $ticketdata[$key]['drop_location_name'] = $ticketvalue->drop_location_name;
                $ticketdata[$key]['pick_stand_id'] = $ticketvalue->pick_stand_id;
                $ticketdata[$key]['pick_stand_name'] = $ticketvalue->pick_stand_name;
                $ticketdata[$key]['pick_stand_time'] = $journeyDay . ' ' . $ticketvalue->pick_stand_time;
                $ticketdata[$key]['drop_stand_id'] = $ticketvalue->drop_stand_id;
                $ticketdata[$key]['drop_stand_name'] = $ticketvalue->drop_stand_name;
                $ticketdata[$key]['drop_stand_time'] = $journeyDay . ' ' . $ticketvalue->drop_stand_time;
                $ticketdata[$key]['price'] = $ticketvalue->price;
                $ticketdata[$key]['discount'] = $ticketvalue->discount;
                $ticketdata[$key]['totaltax'] = $ticketvalue->totaltax;
                $ticketdata[$key]['paidamount'] = $ticketvalue->paidamount;
                $ticketdata[$key]['offerer'] = $ticketvalue->offerer;
                $ticketdata[$key]['adult'] = $ticketvalue->adult;
                $ticketdata[$key]['chield'] = $ticketvalue->chield;
                $ticketdata[$key]['special'] = $ticketvalue->special;
                $ticketdata[$key]['seatnumber'] = $ticketvalue->seatnumber;
                $ticketdata[$key]['totalseat'] = $ticketvalue->totalseat;
                $ticketdata[$key]['journeydata'] = $ticketvalue->journeydata;
                $ticketdata[$key]['payment_status'] = $ticketvalue->payment_status;
                $ticketdata[$key]['vehicle_id'] = $ticketvalue->vehicle_id;
                $ticketdata[$key]['payment_detail'] = $ticketvalue->payment_detail;
                $ticketdata[$key]['startime'] = $scheduldetail->start_time;
                $ticketdata[$key]['endtime'] = $scheduldetail->end_time;
                $ticketdata[$key]['refund'] = $ticketvalue->refund;
                $ticketdata[$key]['cancel_status'] = $ticketvalue->cancel_status;
                $ticketdata[$key]['review_status'] = $reviewStatus;
                $ticketdata[$key]['booking_date'] = $bookingDate;
                $ticketdata[$key]['facility'] = $facility;

                
            if ($ticketvalue->paid_max_luggage_pcs == null || $ticketvalue->paid_max_luggage_pcs == '') {
               $ticketdata[$key]['paid_max_luggage_pcs'] = 0;
            }else{
                $ticketdata[$key]['paid_max_luggage_pcs'] = $ticketvalue->paid_max_luggage_pcs;
            }

            if ($ticketvalue->price_pcs == null || $ticketvalue->price_pcs == '') {
               $ticketdata[$key]['price_pcs'] = 0.00;
            }else{
                $ticketdata[$key]['price_pcs'] = $ticketvalue->price_pcs;
            }

            if ($ticketvalue->special_max_luggage_pcs == null || $ticketvalue->special_max_luggage_pcs == '') {
               $ticketdata[$key]['special_max_luggage_pcs'] = 0;
            }else{
                $ticketdata[$key]['special_max_luggage_pcs'] = $ticketvalue->special_max_luggage_pcs;
            }
            if ($ticketvalue->special_price_pcs == null || $ticketvalue->special_price_pcs == '') {
               $ticketdata[$key]['special_price_pcs'] = 0.00;
            }else{
                $ticketdata[$key]['special_price_pcs'] = $ticketvalue->special_price_pcs;
            }
          
           $ticketdata[$key]['from'] = $gettripdata->pl_name;
           $ticketdata[$key]['to'] = $gettripdata->dl_name;
           $ticketdata[$key]['trip_start_time'] = $gettripdata->start_time;
           $ticketdata[$key]['trip_end_time'] = $gettripdata->end_time;
           $ticketdata[$key]['travelerPick'] = $travelartripdata->pl_name;
           $ticketdata[$key]['travelerDrop'] = $travelartripdata->dl_name;

           $ticketdata[$key]['discount'] = (float)$ticketvalue->discount;
           $ticketdata[$key]['totaltax'] = (float)$ticketvalue->totaltax;
           $ticketdata[$key]['paidamount'] = (float)$ticketvalue->paidamount;
           $ticketdata[$key]['roundtrip_discount'] = (float)$ticketvalue->roundtrip_discount;
           $ticketdata[$key]['total_paid_luggage_price'] = round(((int)$ticketvalue->paid_max_luggage_pcs * (float)$ticketvalue->price_pcs), 2);
           $ticketdata[$key]['total_special_luggage_price'] = round(((int)$ticketvalue->special_max_luggage_pcs * (float)$ticketvalue->special_price_pcs), 2);

           $ticketdata[$key]['sub_total'] = round(((float)$ticketvalue->price + (float)$ticketdata[$key]['total_paid_luggage_price'] + (float)$ticketdata[$key]['total_special_luggage_price']), 2);
           $ticketdata[$key]['grand_total'] = round((float)$ticketdata[$key]['sub_total'] + (float)$ticketdata[$key]['totaltax'] - (float)$ticketdata[$key]['discount'], 2);
                
            }

            $data = [
                'status' => "success",
                'response' => 200,
                'data' => $ticketdata,
            ];

            return $this->response->setJSON($data);
        } catch (Exception $ex) {
            $data = [
                'status' => "fail",
                'response' => 201,
                'data' => "token not valid",
                'error' => $ex->getMessage(),
            ];
            return $this->response->setJSON($data);
        }
    }

    public function passangerpicuplod()
    {
        $path = 'image/passenger';
        $image =  $this->request->getFile('image');

        $validation =     $this->validate([
            'image' => 'uploaded[image]|max_size[image,1024]',
        ]);
        if (!$validation) {

            $data = [
                'status' => "fail",
                'response' => 201,
                'data' => $validation,
                'message' => "Max file size 1MB",
            ];

            return $this->response->setJSON($data);
        }


        if ($image->isValid() && !$image->hasMoved()) {
            $profilepic     = $this->imgaeCheck($image, $path);
        }

        $key = getenv('TOKEN_SECRET');
        $token = $this->tokenJwt->tokencheck();





        try {
            $decoded = JWT::decode($token, $key, array("HS256"));


            $userdetailId = $this->usercheck($decoded->slug);

            $picupload = array(
                "id" => $userdetailId->id,
                "image" => $profilepic,

            );

            $success = $this->userDetailModel->save($picupload);

            $data = [
                'status' => "success",
                'response' => 200,
                'data' => $success,
            ];

            return $this->response->setJSON($data);
        } catch (Exception $ex) {
            $data = [
                'status' => "fail",
                'response' => 201,
                'data' => "token not valid",
            ];
            return $this->response->setJSON($data);
        }
    }

    public function changePassengerinfo()
    {

        $key = getenv('TOKEN_SECRET');
        $token = $this->tokenJwt->tokencheck();


        try {
            $decoded = JWT::decode($token, $key, array("HS256"));

            $userdetailId = $this->usercheck($decoded->slug);

            $validdata = array(
                // "id" => $userdetailId->id,
                "first_name" => $this->request->getVar('first_name'),
                "last_name" => $this->request->getVar('last_name'),
                "id_type" => $this->request->getVar('id_type'),
                // "country_id" => $this->request->getVar('country_id'),
            );
            $validationRules = [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'id_type' => 'permit_empty|string',
            ];
            $validation = \Config\Services::validation();

            if ($validation->setRules($validationRules)->run($validdata)) {

                $inputdata = array(
                    "id" => $userdetailId->id,
                    "first_name" => $this->request->getVar('first_name'),
                    "last_name" => $this->request->getVar('last_name'),
                    "id_type" => $this->request->getVar('id_type'),
                    "country_id" => $this->request->getVar('country_id'),
                    "id_number" => $this->request->getVar('id_number'),
                    "address" => $this->request->getVar('address'),
                    "city" => $this->request->getVar('city'),
                    "zip_code" => $this->request->getVar('zip_code'),

                );




                $success = $this->userDetailModel->save($inputdata);

                $data = [
                    'status' => "success",
                    'response' => 200,
                    'data' => $success,
                ];

                return $this->response->setJSON($data);
            } else {
                $data = [
                    'status' => "fail",
                    'response' => 201,
                    'message' => "data validation error",
                    'data' => $this->validation->listErrors(),
                ];
                return $this->response->setJSON($data);
            }
        } catch (Exception $ex) {
            $data = [
                'status' => "fail",
                'response' => 201,
                'data' => "token not valid",
                'error' => $ex,
            ];
            return $this->response->setJSON($data);
        }
    }

    public function changePassword()
    {
        $password = $this->request->getVar('password');
        $repassword = $this->request->getVar('repassword');
        $oldpassword = $this->request->getVar('oldpassword');

        if ($password == $repassword) {

            $key = getenv('TOKEN_SECRET');
            $token = $this->tokenJwt->tokencheck();

            try {
                $decoded = JWT::decode($token, $key, array("HS256"));



                $userdetail = $this->userModel->where('slug', $decoded->slug)->first();

                $pass = $userdetail->password;
                $verify_pass = password_verify($oldpassword, $pass);

                if ($verify_pass) {
                    $newpassword = password_hash($password, PASSWORD_DEFAULT);
                    $passupdate = array(
                        "id" => $userdetail->id,
                        "password" => $newpassword,

                    );

                    $success = $this->userModel->save($passupdate);

                    $data = [
                        'status' => "success",
                        'response' => 200,
                        'data' => $success,
                    ];

                    return $this->response->setJSON($data);
                } else {
                    $data = [
                        'status' => "fail",
                        'response' => 201,
                        'data' => "old-password dosen't match",
                    ];

                    return $this->response->setJSON($data);
                }
            } catch (Exception $ex) {
                $data = [
                    'status' => "fail",
                    'response' => 201,
                    'data' => "token not valid",
                ];
                return $this->response->setJSON($data);
            }
        } else {
            $data = [
                'status' => "fail",
                'response' => 201,
                'data' => "password dosen't match",
            ];
            return $this->response->setJSON($data);
        }
    }


    public function usercheck($slag)
    {

        $userdetail = $this->userModel->where('slug', $slag)->first();
        $userdetailid = $this->userDetailModel->where('user_id', $userdetail->id)->first();
        return $userdetailid;
    }

    public function imgaeCheck($image, $path)
    {
        $newName = $image->getRandomName();
        $path = $path;
        $image->move($path, $newName);
        return $path . '/' . $newName;
    }


    public function regUser()
    {
        $login_email = $this->request->getVar('login_email');
        $login_mobile = $this->request->getVar('login_mobile');

        $inputPass = $this->request->getVar('password');
        $password = password_hash($inputPass, PASSWORD_DEFAULT);

        $bytes = random_bytes(5);
        $slug = bin2hex($bytes);
        $role_id = 3;
        $status = 1;

        $userData = array(
            "login_email" => $login_email,
            "login_mobile" => $login_mobile,
            "password" => $password,
            "slug" => $slug,
            "role_id" => $role_id,
            "status" => $status,
        );

        $validuserData = array(
            "login_email" => $login_email,
            "login_mobile" => $login_mobile,
            "password" => $this->request->getVar('password'),
            "repassword" => $this->request->getVar('repassword'),
            "slug" => $slug,
            "role_id" => $role_id,
            "status" => $status,
        );
        $validdata = array(
            "first_name" => $this->request->getVar('first_name'),
            "last_name" => $this->request->getVar('last_name'),
            "id_type" => $this->request->getVar('id_type') ?: null,
            "id_number" => $this->request->getVar('id_number') ?: null,
            "country_id" => $this->request->getVar('country_id'),
        );

        if ($this->validation->run($validuserData, 'reguser') && $this->validation->run($validdata, 'userDetail')) {

            $this->db->transStart();
            
            // Try to insert the user data
            $userid = $this->userModel->insert($userData);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                $data = [
                    'status' => "fail",
                    'response' => 404,
                    'data' => "User insertion failed",
                ];
                return $this->response->setJSON($data);
            }
            // If user detail validation passes, insert user details
            $data = array(
                "user_id" => $userid,
                "first_name" => $this->request->getVar('first_name'),
                "last_name" => $this->request->getVar('last_name'),
                "id_type" => $this->request->getVar('id_type') ?: null,
                "country_id" => $this->request->getVar('country_id'),
                "id_number" => $this->request->getVar('id_number') ?: null,
                "address" => $this->request->getVar('address'),
                "city" => $this->request->getVar('city'),
                "zip_code" => $this->request->getVar('zip_code'),
            );

            $usrDetails = $this->userDetailModel->insert($data);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                $data = [
                    'status' => "fail",
                    'response' => 404,
                    'data' => "User detail insertion failed",
                ];
                return $this->response->setJSON($data);
            } else {
                // If everything succeeds, commit the transaction
                $this->db->transCommit();
                $data = [
                    'status' => "success",
                    'response' => 200,
                    'data' => "Registration Success",
                ];
                return $this->response->setJSON($data);
            }
        } else {
            $data = [
                'status' => "fail",
                'response' => 404,
                'error' => $this->validation->getErrors(),
                'data' => "User validation failed",
            ];
            return $this->response->setJSON($data);
        }
    }



    public function loginsocial()
    {


        $appid = $this->request->getVar('appid');
        $first_name = $this->request->getVar('first_name');
        $last_name = $this->request->getVar('last_name');
        $email = $this->request->getVar('email');

        $getAppid = $this->socialsigninModel->where('appid', $appid)->where('email', $email)->first();

        if (!empty($getAppid)) {



            $userdetail = $this->userModel->join('user_details', 'user_details.user_id = users.id', 'left')->where('role_id', 3)->where('status', 1)
                ->where('login_email', $email)
                ->where('login_mobile', $appid)
                ->first();


            if ($userdetail) {

                $token = $this->tokenJwt->generateToken($userdetail->slug);

                $data = [
                    'status' => "success",
                    'response' => 200,
                    'data' => $token,
                ];

                return $this->response->setJSON($data);
            } else {
                $data = [
                    'message' => "User  Not Found",
                    'status' => "fail",
                    'response' => 204,

                ];
                return $this->response->setJSON($data);
            }
        } else {



            $socialdatavalidation = [
                'appid' => $appid,
                'email' => $email,
            ];

            $socialdata = [
                'appid' => $appid,
                'email' => $email,
                'other' => $this->request->getVar('other'),
            ];

            if ($this->validation->run($socialdatavalidation, 'socialsingup')) {



                $this->socialsigninModel->insert($socialdata);


                $inputPass = $appid;
                $password = password_hash($inputPass, PASSWORD_DEFAULT);

                $bytes = random_bytes(5);
                $slug = bin2hex($bytes);
                $role_id = 3;
                $status = 1;

                $userData = array(
                    "login_email" => $email,
                    "login_mobile" => $appid,
                    "password" => $password,
                    "slug" => $slug,
                    "role_id" => $role_id,
                    "status" => $status,
                );

                if ($this->validation->run($userData, 'user')) {

                    $userid = $this->userModel->insert($userData);

                    $validdata = array(
                        "user_id" => $userid,
                        "first_name" => $first_name,
                        "last_name" => $last_name,
                        "id_type" => "passport",
                        "id_number" => $appid ?: null,
                        "country_id" => 14,
                    );

                    if ($this->validation->run($validdata, 'userDetail')) {
                        $data = array(
                            "user_id" => $userid,
                            "first_name" => $first_name,
                            "last_name" => $last_name,
                            "id_type" => "passport",
                            "id_number" => $appid ?: null,
                            "country_id" => 14,

                        );

                        $this->userDetailModel->insert($data);


                        $userdetail = $this->userModel->join('user_details', 'user_details.user_id = users.id', 'left')->where('role_id', 3)->where('status', 1)
                            ->where('login_email', $email)
                            ->where('login_mobile', $appid)
                            ->first();


                        if ($userdetail) {

                            $token = $this->tokenJwt->generateToken($userdetail->slug);

                            $data = [
                                'status' => "success",
                                'response' => 200,
                                'data' => $token,
                            ];

                            return $this->response->setJSON($data);
                        } else {
                            $data = [
                                'message' => "User  Not Found",
                                'status' => "fail",
                                'response' => 204,

                            ];
                            return $this->response->setJSON($data);
                        }
                    }
                } else {
                    $data = [
                        'status' => "fail",
                        'response' => 404,
                        'error' => $this->validation->getErrors(),   //$validation->listErrors()
                        'data' => "Registration fail",
                    ];
                    return $this->response->setJSON($data);
                }
            } else {

                $data = [
                    'status' => "fail",
                    'response' => 404,
                    'error' => $this->validation->getErrors(),   //$validation->listErrors()
                    'data' => "Registration fail",
                ];
                return $this->response->setJSON($data);
            }
        }
    }


    public function checkEmail()
    {
        $email = $this->request->getVar('login_email');

        $emailDetail = $this->userModel->where('login_email', $email)->first();

        if (empty($emailDetail)) {
            $data = [
                'message' => "No Email address found",
                'status' => "fail",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        } else {
            $data = [
                'message' => "Email address found",
                'status' => "success",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        }
    }



    public function checkMobile()
    {
        $mobile = $this->request->getVar('login_mobile');

        $emailDetail = $this->userModel->where('login_mobile', $mobile)->first();

        if (empty($emailDetail)) {
            $data = [
                'message' => "No Mobile Number found",
                'status' => "fail",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        } else {
            $data = [
                'message' => "Mobile Number found",
                'status' => "success",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        }
    }


    public function checkIdNumber()
    {
        $idnumber = $this->request->getVar('id_number');

        $idDetail = $this->userDetailModel->where('id_number', $idnumber)->first();

        if (empty($idDetail)) {
            $data = [
                'message' => "No Id Number found",
                'status' => "fail",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        } else {
            $data = [
                'message' => "ID Number found",
                'status' => "success",
                'response' => 204,

            ];
            return $this->response->setJSON($data);
        }
    }
}
