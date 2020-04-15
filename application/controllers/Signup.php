<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {

	function __construct() {
        parent::__construct();
    }

	public function index()
	{
        $resp = default_response("Customer sudah terdaftar!");

        $customer_no_ktp = get_raw_body("no_ktp");
        $customer_nama = get_raw_body("nama");
        $customer_email = get_raw_body("email");
        $customer_password = md5(get_raw_body("password"));
        $customer_no_telp = get_raw_body("no_telp");
        $customer_tgl_lahir = get_raw_body("tgl_lahir");
        $customer_kota_tinggal = get_raw_body("kota_tinggal");
        $customer_alamat = get_raw_body("alamat");

        $check_admin = $this->db->get_where("admin", "admin_email='" . $customer_email . "'")->num_rows();
        $check_kurir = $this->db->get_where("kurir", "kurir_email='" . $customer_email . "'")->num_rows();
        $check_customer = $this->db->get_where("customer", "customer_email='" . $customer_email . "'")->num_rows();


        if($check_admin > 0){
            $resp = default_response("Email ini sudah terdaftar sebagai admin!");
        }

        if($check_kurir > 0){
            $resp = default_response("Email ini sudah terdaftar sebagai kurir!");
        }

        if($check_customer > 0){
            $resp = default_response("Email ini sudah terdaftar sebagai customer!");
        }
        
        if($check_admin === 0 && $check_kurir === 0 && $check_customer === 0){
            if(!empty($customer_no_ktp) && !empty($customer_nama) && !empty($customer_email) && !empty($customer_password) && !empty($customer_no_telp) && !empty($customer_tgl_lahir) && !empty($customer_kota_tinggal) && !empty($customer_alamat)){
                $arr_data = [
                    "customer_no_ktp" => $customer_no_ktp,
                    "customer_nama" => $customer_nama,
                    "customer_email" => $customer_email,
                    "customer_password" => $customer_password,
                    "customer_no_telp" => $customer_no_telp,
                    "customer_tgl_lahir" => $customer_tgl_lahir,
                    "customer_kota_tinggal" => $customer_kota_tinggal,
                    "customer_alamat" => $customer_alamat
                ];

                $this->db->insert("customer", $arr_data);

                $resp = [
                    "success" => true,
                    "message" => "Registrasi Berhasil",
                    "data" => $arr_data,
                    "total" => 1
                ];
            }
        }

        j_encode($resp, "raw");
    }
}
