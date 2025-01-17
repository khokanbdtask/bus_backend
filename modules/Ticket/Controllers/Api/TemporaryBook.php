<?php

namespace Modules\Ticket\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Modules\Ticket\Config\TicketValidation;
use Modules\Ticket\Exceptions\TicketExceptions;
use Modules\Ticket\Models\TemporaryBook as TemporaryBookModel;
use Modules\Website\Models\WebsettingModel;
use CodeIgniter\Database\RawSql;
use CodeIgniter\I18n\Time;

class TemporaryBook extends BaseController
{
    use ResponseTrait;

    /**
     * Temporary book model
     *
     * @var TemporaryBookModel
     */
    protected $temporaryBookModel;
    protected $websettingModel;
    public function __construct()
    {
        // Init temporary book validator
        $ticketValidation = new TicketValidation;
        $this->validation = \Config\Services::validation($ticketValidation);
        $this->validation->setRuleGroup('temporarybooks');
        $this->websettingModel = new WebsettingModel();

        // Init temporary book model
        $this->temporaryBookModel = new TemporaryBookModel;
    }

    public function checkSeats()
    {
        // var_dump($this->request->getVar());exit;
        $updateBookId = null;
        $subtrip_id = $this->request->getVar('subtrip_id');
        $client_token = $this->request->getVar('ticket_token');
        $seat_names = $this->request->getVar('seat_names');
        $seat_nameArr = array_map('trim', array_filter(explode(',', $seat_names)));
        $journey_date = $this->request->getVar('journey_date');

        // clean expired rows
        $this->cleanDatabase();

        // check subtrip exists
        // seat search regex string
        $seatSearchRegex = '\\b(' . implode('|', $seat_nameArr) . ')\\b';
        
        $check = $this->temporaryBookModel->query(
            "SELECT * FROM `temporarybooks`
              WHERE subtrip_id = ?
              AND DATE(journey_date) = DATE(?)
              AND seat_names REGEXP ?",
            [$subtrip_id, $journey_date, $seatSearchRegex]
        )->getResult();

        try {
            if (!empty($check) && is_array($check)) {
                // build booked seats 
                $bookByOtherSeats = array();

                foreach ($check as $singleCheck) {
                    // build book info
                    $seatBookId = $singleCheck->id;
                    $seatBookToken = $singleCheck->ticket_token;

                    if ($client_token != $seatBookToken) {
                        // this seats booked by other passanger
                        $bookByOtherSeats = array_merge($bookByOtherSeats, explode(',', $singleCheck->seat_names));
                        continue;
                    }

                    // this seats booked by me
                    $updateBookId = $seatBookId;
                }

                if (count($bookByOtherSeats)) {
                    $seatMatches = array_intersect($bookByOtherSeats, $seat_nameArr);
                    throw new TicketExceptions(sprintf('Seat %s already processed!', implode(', ', $seatMatches)));
                }
            }

            $this->store($updateBookId);
        } catch (TicketExceptions $th) {
            return $this->response->setJSON([
                'status' => 'failed',
                'response' => 204,
                'message' => $th->getErrors()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'response' => 200,
            'message' => true
        ]);
    }

    protected function store(int $seatBookId = null)
    {
        $websetting = $this->websettingModel->first();
        $timeForTimezone = $websetting->timezone;
        $timezone = new \DateTimeZone($timeForTimezone);
        $date = new \DateTime('now', $timezone);
        $created_at = $date->format('Y-m-d H:i:s');

        $e_date = $date->modify('+5 minutes');
        $expires_at = $e_date->format('Y-m-d H:i:s');

        $temporaryBookData = array(
            'subtrip_id' => $this->request->getVar('subtrip_id'),
            'ticket_token' => $this->request->getVar('ticket_token'),
            'seat_names' => $this->request->getVar('seat_names'),
            'journey_date' => $this->request->getVar('journey_date'),
            'created_at' => $created_at,
            'expires_at' => $expires_at
        );

        $seatBookId !== null && $temporaryBookData = array_merge(['id' => $seatBookId], $temporaryBookData);

        if ($this->validation->run($temporaryBookData, 'temporarybooks')) {
            return $this->temporaryBookModel->replace($temporaryBookData);
        }

        throw new TicketExceptions(null, 0, null, array_values($this->validation->getErrors()));
    }

    protected function cleanDatabase()
    {
        $websetting = $this->websettingModel->first();
        $timeForTimezone = $websetting->timezone;
        $timezone = new \DateTimeZone($timeForTimezone);
        $date = new \DateTime('now', $timezone);
        $nowtime = $date->format('Y-m-d H:i:s');

        $this->temporaryBookModel->where("expires_at < '$nowtime'")->delete();
    }


    protected function delete(int $seatBookId)
    {
        return $this->temporaryBookModel->where('id', $seatBookId)->delete();
    }
}
