<?php

namespace Modules\Luggage\Controllers;

use App\Controllers\BaseController;
use Modules\Luggage\Models\LuggagesettingModel;
use Modules\Localize\Models\LocalizeModel;

class Luggagesetting extends BaseController
{
    protected $Viewpath;
    protected $luggageSettingModel;
    protected $localizeModel;
    protected $db;

    public function __construct()
    {

        $this->Viewpath = "Modules\Luggage\Views";
        $this->luggageSettingModel = new LuggagesettingModel();
        $this->localizeModel = new LocalizeModel();
        $this->db = \Config\Database::connect();
    }


    public function new()
    {
        $data['module'] =    lang("Localize.luggage_setting");
        $data['title']  =    lang("Localize.settings");

        $data['pageheading'] = lang("Localize.luggage_setting");
        $data['luggagesetting']    = $this->luggageSettingModel->first();

        if (empty($data['luggagesetting'])) {
            echo view($this->Viewpath . '\luggagesetting/new', $data);
        } else {
            echo view($this->Viewpath . '\luggagesetting/edit', $data);
        }
    }

    public function create()
    {
        $validatedata = array(
            "free_luggage_kg" => $this->request->getVar('free_luggage_kg'),
            "paid_max_luggage_pcs" => $this->request->getVar('paid_max_luggage_pcs'),
            "price_pcs" => $this->request->getVar('price_pcs'),
            "special_max_luggage_pcs" => $this->request->getVar('special_max_luggage_pcs'),
            "special_price_pcs" => $this->request->getVar('special_price_pcs'),
        );
        $validatedata['max_length'] = $this->request->getVar('max_length');
        $validatedata['max_weight'] = $this->request->getVar('max_weight');

        if ($this->validation->run($validatedata, 'luggagesetting')) {

            $this->luggageSettingModel->insert($validatedata);
            return redirect()->route('new-luggagesetting')->with("success", "Data Save");
        } else {
            $data['validation'] = $this->validation;
            $data['module'] =    lang("Localize.luggage_setting");
            $data['title']  =    lang("Localize.settings");
            $data['pageheading'] = lang("Localize.luggage_setting");

            echo view($this->Viewpath . '\luggagesetting/new', $data);
        }
    }

    public function update($id)
    {
        $validatedata = array(
            "id" => $id,
            "free_luggage_kg" => $this->request->getVar('free_luggage_kg'),
            "paid_max_luggage_pcs" => $this->request->getVar('paid_max_luggage_pcs'),
            "price_pcs" => $this->request->getVar('price_pcs'),
            "special_max_luggage_pcs" => $this->request->getVar('special_max_luggage_pcs'),
            "special_price_pcs" => $this->request->getVar('special_price_pcs'),
        );
        $validatedata['max_length'] = $this->request->getVar('max_length');
        $validatedata['max_weight'] = $this->request->getVar('max_weight');
        
        if ($this->validation->run($validatedata, 'luggagesetting')) {
            $this->luggageSettingModel->save($validatedata);
            return redirect()->route('new-luggagesetting')->with("success", "Data Update");
        } else {
            $data['luggagesetting']    = $this->luggageSettingModel->first();

            $data['module'] =    lang("Localize.luggage_setting");
            $data['title']  =    lang("Localize.settings");
            $data['pageheading'] = lang("Localize.luggage_setting");

            echo view($this->Viewpath . '\luggagesetting/edit', $data);
        }
    }

}
