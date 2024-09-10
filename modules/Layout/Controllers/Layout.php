<?php

namespace Modules\Layout\Controllers;

use App\Libraries\Rolepermission;
use App\Controllers\BaseController;
use Modules\Layout\Models\LayoutModel;
use Modules\Layout\Models\SeatDetailsModel;
use Modules\Layout\Models\LayoutDetailsModel;


class Layout extends BaseController
{
    private $Viewpath;
    private $layoutModel;
    private $seatDetailsModel;
    private $layoutDetailsModel;


    public function __construct()
    {
        $this->Viewpath = "Modules\Layout\Views";
        $this->layoutModel = new LayoutModel();
        $this->seatDetailsModel = new SeatDetailsModel();
        $this->layoutDetailsModel = new LayoutDetailsModel();
    }

    public function new()
    {
        $data['module'] =    lang("Localize.layout");
        $data['title']  =    lang("Localize.add_layout");
        $data['pageheading'] = lang("Localize.add_layout");

        echo view($this->Viewpath . '\layout/new', $data);
    }

    public function create()
    {
        $data = array(
            "layout_number" => $this->request->getVar('layout_number'),
            "car_type" => $this->request->getVar('car_type'),
            "total_seat" => $this->request->getVar('total_seat'),
            "total_row" => $this->request->getVar('total_row'),
            "total_column" => $this->request->getVar('total_column'),
            "status" => $this->request->getVar('status'),
            "created_by" => session()->get('user_id'),
        );

        if ($this->validation->run($data, 'layout')) {
            // dd($data);
            $this->layoutModel->insert($data);
            return redirect()->route('index-layout')->with("success", "Data Save");
        } else {
            $data['validation'] = $this->validation;
            $data['module'] =    lang("Localize.layout");
            $data['title']  =    lang("Localize.add_layout");
            $data['pageheading'] = lang("Localize.add_layout");

            echo view($this->Viewpath . '\layout/new', $data);
        }
    }

    public function index()
    {
        $data['layout'] = $this->layoutModel->table('layouts')
            ->select('layouts.*, COUNT(layout_details.id) as layout_details_count')
            ->join('layout_details', 'layout_details.layout_id = layouts.id', 'left')
            ->groupBy('layouts.id')
            ->get()
            ->getResult();

        // dd($data);


        $data['module'] =    lang("Localize.layout");
        $data['title']  =    lang("Localize.layout_list");

        $data['pageheading'] = lang("Localize.layout_list");

        $rolepermissionLibrary = new Rolepermission();
        $add_data = "add_layout";
        $list_data = "layout_list";

        $data['add_data'] = $rolepermissionLibrary->create($add_data);
        $data['edit_data'] = $rolepermissionLibrary->edit($list_data);
        $data['delete_data'] = $rolepermissionLibrary->delete($list_data);


        echo view($this->Viewpath . '\layout/index', $data);
    }

    public function edit($id)
    {
        $data['module'] =    lang("Localize.layout");
        $data['title']  =    lang("Localize.edit_layout");
        $data['pageheading'] = lang("Localize.edit_layout");
        $data['layout'] = $this->layoutModel->find($id);

        echo view($this->Viewpath . '\layout/edit', $data);
    }

    public function update($id)
    {
        $data = array(
            "layout_number" => $this->request->getVar('layout_number'),
            "car_type" => $this->request->getVar('car_type'),
            "total_seat" => $this->request->getVar('total_seat'),
            "total_row" => $this->request->getVar('total_row'),
            "total_column" => $this->request->getVar('total_column'),
            "status" => $this->request->getVar('status'),
            "updated_by" => session()->get('user_id'),
        );
        $layout = $this->layoutModel->find($id);


        $validationRules = [
            'layout_number'     => [
                'rules' => 'required|' . ($this->request->getPost('layout_number') == $layout->layout_number ? '' : 'is_unique[layouts.layout_number,id,{id}]'),
                'errors' => [
                    'required' => 'Layout number is required',
                    'is_unique' => 'This layout number is already exists'
                ]
            ],
            'car_type'     => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Car type is required'
                ]
            ],
            'total_seat'     => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Total seat is required'
                ]
            ],
            'total_row'     => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Total row is required'
                ]
            ],
            'total_column'     => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Total column is required'
                ]
            ],
            'status'     => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Status is required'
                ]
            ],
        ];

        $validation = \Config\Services::validation();
        if ($validation->setRules($validationRules)->run($data)) {
            // dd($data);
            $this->layoutModel->update($id, $data);
            return redirect()->route('index-layout')->with("success", "Data Save");
        } else {
            $data['validation'] = $this->validation;
            $data['module'] =    lang("Localize.layout");
            $data['title']  =    lang("Localize.edit_layout");
            $data['pageheading'] = lang("Localize.edit_layout");
            $data['layout'] = $this->layoutModel->find($id);
            // dd($data);
            echo view($this->Viewpath . '\layout/edit', $data);
        }
    }

    public function newDetails($id)
    {
        $data['module'] =    lang("Localize.layout");
        $data['title']  =    lang("Localize.add_layout_details");
        $data['pageheading'] = lang("Localize.add_layout_details");
        $data['layout'] = $this->layoutModel->find($id);
        $data['seat_element'] = $this->seatDetailsModel->findAll();

        // dd($data);

        echo view($this->Viewpath . '\layout_details/new', $data);
    }

    public function createDetails()
    {
        // var_dump($this->request->getVar());exit;
        $layoutId = $this->request->getVar('layout_id');
        $columns = $this->request->getVar('columns');
        $seatNos = $this->request->getVar('seat_no');
        $layoutIdF = $this->layoutModel->find($layoutId);
        // var_dump($layoutId->total_seat);

        $totalCount = 0;

        foreach ($seatNos as $subarray) {
            $nonEmptyValues = array_filter($subarray, function ($value) {
                // Exclude empty and driver seats from the count
                return $value !== '';
            });

            $totalCount += count($nonEmptyValues);
        }

        if ($totalCount != (int)$layoutIdF->total_seat) {
            return redirect()->back()->with("fail", "Total Seat and Seat Number Not Match");
        }

        $flatArray = array_reduce($seatNos, 'array_merge', array());

        // Keep track of unique values
        $uniqueValues = array();

        foreach ($flatArray as $value) {
            // Skip empty values
            if ($value !== '') {
                // Check if the value is already encountered
                if (in_array($value, $uniqueValues)) {
                    return redirect()->back()->with("fail", "Duplicate value found - $value");
                    exit;
                } else {
                    // Add the value to the unique values array
                    $uniqueValues[] = $value;
                }
            }
        }

        $flattenedArray = array_merge(...$columns);
        $countOnes = array_count_values($flattenedArray)['1'] ?? 0;
        if ($countOnes > 1) {
            return redirect()->back()->with("fail", "Error: More than one occurrence of driver seat found!");
            exit;
        }


        foreach ($columns as $rowNo => $rowData) {
            $row = [
                'layout_id' => (int)$layoutId,
                'row_no' => $rowNo,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => session()->get('user_id'), // Set the appropriate user ID
            ];

            // Check and include column data if it exists
            if (isset($rowData[1])) $row['column1'] = (int)$rowData[1];
            if (isset($rowData[2])) $row['column2'] = (int)$rowData[2];
            if (isset($rowData[3])) $row['column3'] = (int)$rowData[3];
            if (isset($rowData[4])) $row['column4'] = (int)$rowData[4];
            if (isset($rowData[5])) $row['column5'] = (int)$rowData[5];

            // Include seat_no data if it exists
            if (isset($seatNos[$rowNo][1])) $row['seat_no1'] = $seatNos[$rowNo][1];
            if (isset($seatNos[$rowNo][2])) $row['seat_no2'] = $seatNos[$rowNo][2];
            if (isset($seatNos[$rowNo][3])) $row['seat_no3'] = $seatNos[$rowNo][3];
            if (isset($seatNos[$rowNo][4])) $row['seat_no4'] = $seatNos[$rowNo][4];
            if (isset($seatNos[$rowNo][5])) $row['seat_no5'] = $seatNos[$rowNo][5];

            $batchData[] = $row;
        }
        // dd($batchData);exit;

        if ($this->layoutDetailsModel->insertBatch($batchData)) {
            return redirect()->route('index-layout')->with("success", "Data Save");
        } else {
            return redirect()->route('index-layout')->with("fail", "Data Not Save");
        }
    }

    public function viewDetails($id)
    {
        $data['module'] =    lang("Localize.layout");
        $data['title']  =    lang("Localize.view_layout_details");
        $data['pageheading'] = lang("Localize.view_layout_details");
        $data['layout'] = $this->layoutModel->find($id);
        $data['layout_details'] = $this->layoutDetailsModel
            ->select('layout_details.*, sd1.element as column1_element, sd2.element as column2_element, sd3.element as column3_element, sd4.element as column4_element, sd5.element as column5_element')
            ->join('seat_elements sd1', 'sd1.id = layout_details.column1', 'left')
            ->join('seat_elements sd2', 'sd2.id = layout_details.column2', 'left')
            ->join('seat_elements sd3', 'sd3.id = layout_details.column3', 'left')
            ->join('seat_elements sd4', 'sd4.id = layout_details.column4', 'left')
            ->join('seat_elements sd5', 'sd5.id = layout_details.column5', 'left')
            ->where('layout_id', $id)->findAll();

        // var_dump($data);exit;

        echo view($this->Viewpath . '\layout_details/view', $data);
    }
}
