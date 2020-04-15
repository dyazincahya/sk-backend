<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

	function __construct() {
        parent::__construct();
    }

	function index()
	{
        $resp = default_response("Request is fail");

        $keyword = strtoupper(get_raw_body("keyword"));
        $status = get_raw_body("status"); // 0=non active 1=active 2=pending

        if($keyword == null && $status != null){
            $where = "customer_status='" . $status . "'";
        } else if($status == null && $keyword != null) {
            $where = "UPPER(customer_nama) LIKE '%" . $keyword . "%'";
        } else if($keyword == null && $status == null) {
            $where = null;
        } else if($keyword != null && $status != null) {
            $where = "UPPER(customer_nama) LIKE '%" . $keyword . "%' AND customer_status='" . $status . "'";
        }

        if($where == null){
            $customer = $this->db->order_by("customer_id", "DESC")->get("customer");
        } else {
            $customer = $this->db->order_by("customer_id", "DESC")->get_where("customer", $where);
        }

        if($customer->num_rows() > 0){
            $customer_data = [];
            foreach ($customer->result_array() as $key => $val) {
                $customer_data[$key]["customer_id"] = $val['customer_id'];
                $customer_data[$key]["customer_no_ktp"] = $val['customer_no_ktp'];
                $customer_data[$key]["customer_nama"] = $val['customer_nama'];
                $customer_data[$key]["customer_email"] = $val['customer_email'];
                $customer_data[$key]["customer_password"] = $val['customer_password'];
                $customer_data[$key]["customer_no_telp"] = $val['customer_no_telp'];
                $customer_data[$key]["customer_tgl_lahir"] = $val['customer_tgl_lahir'];
                $customer_data[$key]["customer_kota_tinggal"] = $val['customer_kota_tinggal'];
                $customer_data[$key]["customer_alamat"] = $val['customer_alamat'];            
                $customer_data[$key]["customer_status"] = zstatus($val['customer_status']);            
            }
            $resp = [
                "success" => true,
                "message" => "Request Scuccesfully",
                "data" => $customer_data,
                "total" => $customer->num_rows()
            ];    
        }
        
        j_encode($resp, "raw");
    }

    public function update_status(){
        $resp = default_response("Gagal memperbaharui data!");

        $id = get_raw_body("id");
        $status = get_raw_body("status"); // 0=non active 1=active 2=pending

        if($id != null && $status != null){
            if($status == "OFF"){
                $status = "0";
            }
            $this->db->where("customer_id", $id);
            $this->db->update("customer", ["customer_status" => $status]);

            $message = "";
            if($status ==  1){
                $message = "Akun berhasil di aktifkan";
            } else {
                $message = "Akun berhasil di non aktifkan";
            }

            $resp = [
                "success" => true,
                "message" => $message,
                "data" => [],
                "total" => 1
            ];  
        }

        j_encode($resp, "raw");
    }

    function kurir()
    {
        $resp = default_response("Request is fail");

        $keyword = strtoupper(get_raw_body("keyword"));

        if($keyword == null){
            $kurir = $this->db->order_by("kurir_id", "DESC")->get("kurir");
        } else {
            $where = "UPPER(kurir_nama) LIKE '%" . $keyword . "%'";
            $kurir = $this->db->order_by("kurir_id", "DESC")->get_where("kurir", $where);
        }

        if($kurir->num_rows() > 0){
            $resp = [
                "success" => true,
                "message" => "Request Scuccesfully",
                "data" => $kurir->result_array(),
                "total" => $kurir->num_rows()
            ];    
        }
        
        j_encode($resp, "raw");
    }

    public function kurir_insert(){
        $resp = default_response("Gagal menyimpan data!");

        $no_ktp = get_raw_body("no_ktp");
        $nama = get_raw_body("nama");
        $no_telp = get_raw_body("no_telp");
        $email = get_raw_body("email");
        $password = get_raw_body("password");

        if($no_ktp != null && $nama != null && $no_telp != null && $email != null && $password != null){
            $data = [
                "kurir_no_ktp" => $no_ktp, 
                "kurir_nama" => $nama, 
                "kurir_no_telp" => $no_telp, 
                "kurir_email" => $email, 
                "kurir_password" => md5($password)
            ];
            $this->db->insert("kurir", $data);

            $resp = [
                "success" => true,
                "message" => "Data kurir berhasil disimpan",
                "data" => $data,
                "total" => 1
            ];  
        } else {
            $resp = default_response("Inputan tidak boleh kosong!");
        }

        j_encode($resp, "raw");
    }

    public function kurir_update(){
        $resp = default_response("Gagal menyimpan data!");

        $id = get_raw_body("id");
        $no_ktp = get_raw_body("no_ktp");
        $nama = get_raw_body("nama");
        $no_telp = get_raw_body("no_telp");
        $email = get_raw_body("email");
        $password = get_raw_body("password");

        if($no_ktp != null && $nama != null && $no_telp != null && $email != null){
            if($password != null){
                $data = [
                    "kurir_no_ktp" => $no_ktp, 
                    "kurir_nama" => $nama, 
                    "kurir_no_telp" => $no_telp, 
                    "kurir_email" => $email, 
                    "kurir_password" => md5($password)
                ];
            } else {
                $data = [
                    "kurir_no_ktp" => $no_ktp, 
                    "kurir_nama" => $nama, 
                    "kurir_no_telp" => $no_telp, 
                    "kurir_email" => $email
                ];
            }
            $this->db->where("kurir_id", $id);
            $this->db->update("kurir", $data);

            $resp = [
                "success" => true,
                "message" => "Data kurir berhasil disimpan",
                "data" => $data,
                "total" => 1
            ];  
        } else {
            $resp = default_response("Inputan tidak boleh kosong!");
        }

        j_encode($resp, "raw");
    }

    public function kurir_delete(){
        $resp = default_response("Gagal menghapus data!");

        $id = get_raw_body("id");

        if($id != null){
            $this->db->where("kurir_id", $id);
            $this->db->delete("kurir");

            $resp = [
                "success" => true,
                "message" => "Data kurir berhasil dihapus",
                "data" => [],
                "total" => 1
            ];  
        } else {
            $resp = default_response("Inputan tidak boleh kosong!");
        }

        j_encode($resp, "raw");
    }

    function mail_test(){
        $sender = zmailer([
            "to"        => "dyazincahya@gmail.com",
            "subject"   => "mail test",
            "message"   => "<h1>HALLO BRO</h1>"
        ]);

        if($sender){
            echo "email terkirim";
            exit();
        }
        echo "email tidak terkirim";
    }

}
