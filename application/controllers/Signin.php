<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signin extends CI_Controller {

	function __construct() {
        parent::__construct();
    }

	public function index()
	{
        $resp = default_response("User access not found!");

        $email = get_raw_body("email");
        $password = md5(get_raw_body("password"));

        $admin_where = "admin_email='" . $email . "' AND admin_password='" . $password . "'";
        $admin = $this->db->get_where("admin", $admin_where);

        if($admin->num_rows() == 0){
            $customer_where = "customer_email='" . $email . "' AND customer_password='" . $password . "' AND customer_status='1'";
            $customer = $this->db->get_where("customer", $customer_where);
            
            if($customer->num_rows() != 0){
                $dataset = $customer->row_array();
                $resp = [
                    "success" => true,
                    "message" => "Request Successfully",
                    "data" => [
                        "user_id" => $dataset['customer_id'],
                        "user_role" => "customer",
                        "user_no_ktp" => $dataset['customer_no_ktp'],
                        "user_nama" => $dataset['customer_nama'],
                        "user_email" => $dataset['customer_email'],
                        "user_password" => $dataset['customer_password'],
                        "user_no_telp" => $dataset['customer_no_telp'],
                        "user_tgl_lahir" => $dataset['customer_tgl_lahir'],
                        "user_kota_tinggal" => $dataset['customer_kota_tinggal'],
                        "user_alamat" => $dataset['customer_alamat']
                    ],
                    "total" => $customer->num_rows()
                ];
            } else {
                $kurir_where = "kurir_email='" . $email . "' AND kurir_password='" . $password . "'";
                $kurir = $this->db->get_where("kurir", $kurir_where);

                if($kurir->num_rows() > 0){
                    $dataset = $kurir->row_array();
                    $resp = [
                        "success" => true,
                        "message" => "Request Successfully",
                        "data" => [
                            "user_id" => $dataset['kurir_id'],
                            "user_role" => "kurir",
                            "user_no_ktp" => $dataset['kurir_no_ktp'],
                            "user_nama" => $dataset['kurir_nama'],
                            "user_email" => $dataset['kurir_email'],
                            "user_password" => $dataset['kurir_password'],
                            "user_no_telp" => $dataset['kurir_no_telp'],
                            "user_tgl_lahir" => null,
                            "user_kota_tinggal" => null,
                            "user_alamat" => null
                        ],
                        "total" => $kurir->num_rows()
                    ];
                }
            }
        } else {
            $dataset = $admin->row_array();
            $resp = [
                "success" => true,
                "message" => "Request Successfully",
                "data" => [
                    "user_id" => $dataset['admin_id'],
                    "user_role" => "admin",
                    "user_no_ktp" => null,
                    "user_nama" => $dataset['admin_nama'],
                    "user_email" => $dataset['admin_email'],
                    "user_password" => $dataset['admin_password'],
                    "user_no_telp" => null,
                    "user_tgl_lahir" => null,
                    "user_kota_tinggal" => null,
                    "user_alamat" => null
                ],
                "total" => $admin->num_rows()
            ];
        }

        j_encode($resp, "raw");
    }

    public function account_update()
    {
        $resp = default_response("Data gagal di perbaharui!");

        $user_id = get_raw_body("user_id");
        $user_role = get_raw_body("user_role");        
        $user_no_ktp = get_raw_body("user_no_ktp");
        $user_nama = get_raw_body("user_nama");
        $user_password = get_raw_body("user_password");
        $user_no_telp = get_raw_body("user_no_telp");
        $user_tgl_lahir = get_raw_body("user_tgl_lahir");
        $user_kota_tinggal = get_raw_body("user_kota_tinggal");
        $user_alamat = get_raw_body("user_alamat");

        if($user_role == "admin")
        {
            $data = [];
            if($user_password == null){
                $data = [
                    "admin_nama" => $user_nama
                ];
            } else {
                $data = [
                    "admin_nama" => $user_nama,
                    "admin_password" => md5($user_password)
                ];
            }

            $this->db->where("admin_id", $user_id);
            $this->db->update("admin", $data);

            $resp = [
                "success" => true,
                "message" => "Data berhasil di perbaharui",
                "data" => $data,
                "total" => 1
            ];
        } elseif ($user_role == "kurir") 
        {
            $data = [];
            if($user_password == null){
                $data = [
                    "kurir_no_ktp" => $user_no_ktp,
                    "kurir_nama" => $user_nama,
                    "kurir_no_telp" => $user_no_telp
                ];
            } else {
                $data = [
                    "kurir_no_ktp" => $user_no_ktp,
                    "kurir_nama" => $user_nama,
                    "kurir_password" => md5($user_password),
                    "kurir_no_telp" => $user_no_telp
                ];
            }

            $this->db->where("kurir_id", $user_id);
            $this->db->update("kurir", $data);

            $resp = [
                "success" => true,
                "message" => "Data berhasil di perbaharui",
                "data" => $data,
                "total" => 1
            ];
        } elseif ($user_role == "customer")
        {
            $data = [];
            if($user_password == null){
                $data = [
                    "customer_no_ktp" => $user_no_ktp,
                    "customer_nama" => $user_nama,
                    "customer_no_telp" => $user_no_telp,
                    "customer_tgl_lahir" => $user_tgl_lahir,
                    "customer_kota_tinggal" => $user_kota_tinggal,
                    "customer_alamat" => $user_alamat
                ];
            } else {
                $data = [
                    "customer_no_ktp" => $user_no_ktp,
                    "customer_nama" => $user_nama,
                    "customer_password" => md5($user_password),
                    "customer_no_telp" => $user_no_telp,
                    "customer_tgl_lahir" => $user_tgl_lahir,
                    "customer_kota_tinggal" => $user_kota_tinggal,
                    "customer_alamat" => $user_alamat
                ];
            }

            $this->db->where("customer_id", $user_id);
            $this->db->update("customer", $data);

            $resp = [
                "success" => true,
                "message" => "Data berhasil di perbaharui",
                "data" => $data,
                "total" => 1
            ];
        }

        j_encode($resp, "raw");
    }

    public function forgot_password()
    {
        $resp = default_response("Email not found!");

        $email = get_raw_body("email");
        $new_password_rand = zrandstr();

        $admin_where = "admin_email='" . $email . "'";
        $admin = $this->db->get_where("admin", $admin_where);

        if($admin->num_rows() == 0){
            $customer_where = "customer_email='" . $email . "'";
            $customer = $this->db->get_where("customer", $customer_where);
            
            if($customer->num_rows() != 0){
                $mail_send = zmailer([
                    "to"        => str_to_arr($email),
                    "subject"   => "Password baru user aplikasi KIKAN",
                    "message"   => "Password barunya adalah <b>". $new_password_rand ."</b>"
                ]);
                if($mail_send){
                    $this->db->where("customer_email", $email);
                    $this->db->update("customer", ["customer_password" => md5($new_password_rand)]);
                    $dataset = $customer->row_array();
                    $resp = [
                        "success" => true,
                        "message" => "Password baru sudah di kirim, cek inbox/folder spam email!",
                        "data" => $email,
                        "total" => $customer->num_rows()
                    ];
                }
            } else {
                $kurir_where = "kurir_email='" . $email . "'";
                $kurir = $this->db->get_where("kurir", $kurir_where);

                if($kurir->num_rows() > 0){
                    $mail_send = zmailer([
                        "to"        => str_to_arr($email),
                        "subject"   => "Password baru user aplikasi KIKAN",
                        "message"   => "Password barunya adalah <b>". $new_password_rand ."</b>"
                    ]);
                    if($mail_send){
                        $this->db->where("kurir_email", $email);
                        $this->db->update("kurir", ["kurir_password" => md5($new_password_rand)]);
                        $dataset = $kurir->row_array();
                        $resp = [
                            "success" => true,
                            "message" => "Password baru sudah di kirim, cek inbox/folder spam email!",
                            "data" => $email,
                            "total" => $customer->num_rows()
                        ];
                    }
                }
            }
        } else {
            $mail_send = zmailer([
                "to"        => str_to_arr($email),
                "subject"   => "Password baru user aplikasi KIKAN",
                "message"   => "Password barunya adalah <b>". $new_password_rand ."</b>"
            ]);
            if($mail_send){
                $this->db->where("admin_email", $email);
                $this->db->update("admin", ["admin_password" => md5($new_password_rand)]);
                $dataset = $admin->row_array();
                $resp = [
                    "success" => true,
                    "message" => "Password baru sudah di kirim, cek inbox/folder spam email!",
                    "data" => $email,
                    "total" => $customer->num_rows()
                ];
            }
        }

        j_encode($resp, "raw");
    }
}
