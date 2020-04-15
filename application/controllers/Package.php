<?php
date_default_timezone_set('Asia/Jakarta');
defined('BASEPATH') OR exit('No direct script access allowed');

class Package extends CI_Controller {

	function __construct() {
        parent::__construct();
    }

	public function index()
	{
        $resp = default_response();

        $keyword = strtoupper(get_raw_body("keyword"));
        $status = strtoupper(get_raw_body("status"));
        $role = strtoupper(get_raw_body("role"));
        $sess_id = strtoupper(get_raw_body("sess_id")); //customer_id or kurir_id

        switch ($role) {
            case 'CUSTOMER':
                if($keyword == null && $status != null){
                    $where = "p.package_customer_id='". $sess_id ."' AND p.package_status='" . $status . "'";
                } else if($status == null && $keyword != null) {
                    $where = "p.package_customer_id='". $sess_id ."' AND UPPER(p.package_keterangan) LIKE '%" . $keyword . "%'";
                } else if($keyword == null && $status == null) {
                    $where = null;
                } else if($keyword != null && $status != null) {
                    $where = "p.package_customer_id='". $sess_id ."' AND (UPPER(p.package_keterangan) LIKE '%" . $keyword . "%' AND p.package_status='" . $status . "')";
                }
                break;
            case 'KURIR':
                if($keyword == null && $status != null){
                    if($status == "REQUEST"){
                        $where = "p.package_status='" . $status . "'";
                    } else {
                        $where = "p.package_kurir_id='". $sess_id ."' AND p.package_status='" . $status . "'";                        
                    }
                } else if($status == null && $keyword != null) {
                    $where = "p.package_kurir_id='". $sess_id ."' AND UPPER(p.package_keterangan) LIKE '%" . $keyword . "%'";
                } else if($keyword == null && $status == null) {
                    $where = null;
                } else if($keyword != null && $status != null) {
                    $where = "p.package_kurir_id='". $sess_id ."' AND (UPPER(p.package_keterangan) LIKE '%" . $keyword . "%' AND p.package_status='" . $status . "')";
                }
                break;
            default:
                if($keyword == null && $status != null){
                    $where = "p.package_status='" . $status . "'";
                } else if($status == null && $keyword != null) {
                    $where = "UPPER(p.package_keterangan) LIKE '%" . $keyword . "%'";
                } else if($keyword == null && $status == null) {
                    $where = null;
                } else if($keyword != null && $status != null) {
                    $where = "UPPER(p.package_keterangan) LIKE '%" . $keyword . "%' AND p.package_status='" . $status . "'";
                }
                break;
        }
        

        if($where == null){
            $package = $this->db->select("*")
                ->from("package p")
                ->join("customer c", "c.customer_id=p.package_customer_id", "LEFT")
                ->join("kurir k", "k.kurir_id=p.package_kurir_id", "LEFT")
                ->order_by("p.package_id", "DESC")->get();
            // $package = $this->db->order_by("package_id", "DESC")->get_where("package");
        } else {
            $package = $this->db->select("*")
                ->from("package p")
                ->join("customer c", "c.customer_id=p.package_customer_id", "LEFT")
                ->join("kurir k", "k.kurir_id=p.package_kurir_id", "LEFT")
                ->where($where)->order_by("p.package_id", "DESC")->get();
            // $package = $this->db->order_by("package_id", "DESC")->get_where("package", $where);
        }

        if($package->num_rows() > 0){
            $package_data = [];
            foreach ($package->result_array() as $key => $val) {
                $package_data[$key]['package_customer_nama'] = $val['customer_nama'];
                $package_data[$key]['package_customer_no_telp'] = $val['customer_no_telp'];
                $package_data[$key]['package_customer_kota_tinggal'] = $val['customer_kota_tinggal'];
                $package_data[$key]['package_customer_alamat'] = $val['customer_alamat'];
                $package_data[$key]['package_kurir_nama'] = $val['kurir_nama'];
                $package_data[$key]['package_id'] = $val['package_id'];
                $package_data[$key]['package_kurir_id'] = $val['package_kurir_id'];
                $package_data[$key]['package_customer_id'] = $val['package_customer_id'];
                $package_data[$key]['package_tujuan'] = $val['package_tujuan'];
                $package_data[$key]['package_alamat'] = $val['package_alamat'];
                $package_data[$key]['package_tgl_request'] = rfdate($val['package_tgl_request']);
                $package_data[$key]['package_tgl_pickup'] = rfdate($val['package_tgl_pickup']);
                $package_data[$key]['package_tgl_karantina'] = rfdate($val['package_tgl_karantina']);
                $package_data[$key]['package_tgl_pengiriman'] = rfdate($val['package_tgl_pengiriman']);
                $package_data[$key]['package_tgl_selesai'] = rfdate($val['package_tgl_selesai']);
                $package_data[$key]['package_nama'] = $val['package_nama'];
                $package_data[$key]['package_keterangan'] = $val['package_keterangan'];
                $package_data[$key]['package_resi'] = $val['package_resi'];
                $package_data[$key]['package_tagihan_karantina'] = "Rp ".number_format((int)$val['package_tagihan_karantina'],0,',','.');
                $package_data[$key]['package_tagihan_pengiriman'] = "Rp ".number_format((int)$val['package_tagihan_pengiriman'],0,',','.');
                if(!empty($val['package_tagihan_karantina']) && !empty($val['package_tagihan_pengiriman'])){
                    $total_x = $val['package_tagihan_karantina']+$val['package_tagihan_pengiriman'];
                    $package_data[$key]['package_tagihan_total'] = "Rp ".number_format((int)$total_x,0,',','.');
                } else {
                    $package_data[$key]['package_tagihan_total'] = "Rp ".number_format(0,0,',','.');
                }
                $package_data[$key]['package_status'] = $val['package_status'];
                $package_data[$key]['package_last_update'] = rfdate($val['package_last_update']);
                $package_data[$key]['package_created'] = rfdate($val['package_created']);
            }

            $resp = [
                "success" => true,
                "message" => "Request Scuccesfully",
                "data" => $package_data,
                "total" => $package->num_rows()
            ];
        }

        j_encode($resp, "raw");
    }

    public function save()
    {
        $resp = default_response("Gagal menyimpan data!");

        $package_nama = strtoupper(get_raw_body("nama"));
        $package_keterangan = strtoupper(get_raw_body("keterangan"));
        $package_tujuan = strtoupper(get_raw_body("tujuan"));
        $package_alamat = strtoupper(get_raw_body("alamat"));
        $package_customer_id = strtoupper(get_raw_body("customer_id"));

        if($package_nama != null && $package_keterangan != null && $package_tujuan != null && $package_alamat != null && $package_customer_id != null){
            $data = [
                "package_nama" => $package_nama,
                "package_keterangan" => $package_keterangan,
                "package_tujuan" => $package_tujuan,
                "package_alamat" => $package_alamat,
                "package_customer_id" => $package_customer_id,
                "package_tgl_request" => date("Y-m-d H:i:s"),
                "package_last_update" => date("Y-m-d H:i:s")
            ];
            $this->db->insert("package", $data);

            $resp = [
                "success" => true,
                "message" => "Data berhasil di simpan",
                "data" => $data,
                "total" => 1
            ];
        }

        j_encode($resp, "raw");
    }

    public function update()
    {
        $resp = default_response("Gagal memperbaharui data!");

        $package_id = strtoupper(get_raw_body("id"));
        $package_nama = strtoupper(get_raw_body("nama"));
        $package_keterangan = strtoupper(get_raw_body("keterangan"));
        $package_tujuan = strtoupper(get_raw_body("tujuan"));

        if($package_nama != null && $package_keterangan != null && $package_tujuan != null && $package_customer_id != null){
            $data = [
                "package_nama" => $package_nama,
                "package_keterangan" => $package_keterangan,
                "package_tujuan" => $package_tujuan,
                "package_customer_id" => $package_customer_id,
                "package_tgl_request" => date("Y-m-d H:i:s"),
                "package_last_update" => date("Y-m-d H:i:s")
            ];
            $this->db->insert("package", $data);

            $resp = [
                "success" => true,
                "message" => "Data berhasil di simpan",
                "data" => $data,
                "total" => 1
            ];
        }

        j_encode($resp, "raw");
    }

    public function pickup()
    {
        $resp = default_response("Pickup data gagal!");

        $package_id = strtoupper(get_raw_body("id"));
        $package_kurir_id = strtoupper(get_raw_body("kurir_id"));

        if($package_id != null){
            $data = [
                "package_kurir_id" => $package_kurir_id,
                "package_status" => "PICKUP",
                "package_tgl_pickup" => date("Y-m-d H:i:s"),
                "package_last_update" => date("Y-m-d H:i:s")
            ];
            $this->db->where("package_id", $package_id);
            $this->db->update("package", $data);

            $resp = [
                "success" => true,
                "message" => "Pickup data berhasil",
                "data" => $data,
                "total" => 1
            ];
        }

        j_encode($resp, "raw");
    }

    public function karantina()
    {
        $resp = default_response("Karantina gagal!");

        $package_id = strtoupper(get_raw_body("id"));
        $package_tagihan_karantina = strtoupper(get_raw_body("tagihan"));

        if($package_id != null){
            $data = [
                "package_tagihan_karantina" => $package_tagihan_karantina,
                "package_status" => "KARANTINA",
                "package_tgl_karantina" => date("Y-m-d H:i:s"),
                "package_last_update" => date("Y-m-d H:i:s")
            ];
            $this->db->where("package_id", $package_id);
            $this->db->update("package", $data);

            $resp = [
                "success" => true,
                "message" => "Karantina berhasil",
                "data" => $data,
                "total" => 1
            ];
        }

        j_encode($resp, "raw");
    }

    public function pengiriman()
    {
        $resp = default_response("Data pengiriman gagal disimpan!");

        $package_id = strtoupper(get_raw_body("id"));
        $package_tagihan_pengiriman = strtoupper(get_raw_body("tagihan"));
        $package_resi = strtoupper(get_raw_body("no_resi"));

        if($package_id != null){
            $data = [
                "package_tagihan_pengiriman" => $package_tagihan_pengiriman,
                "package_resi" => $package_resi,
                "package_status" => "PENGIRIMAN",
                "package_tgl_pengiriman" => date("Y-m-d H:i:s"),
                "package_last_update" => date("Y-m-d H:i:s")
            ];
            $this->db->where("package_id", $package_id);
            $this->db->update("package", $data);

            $resp = [
                "success" => true,
                "message" => "Data pengiriman berhasil disimpan",
                "data" => $data,
                "total" => 1
            ];
        }

        j_encode($resp, "raw");
    }

    public function selesai()
    {
        $resp = default_response("Konfirmasi penerimaan gagal!");

        $package_id = strtoupper(get_raw_body("id"));
        $package_tagihan_pengiriman = strtoupper(get_raw_body("tagihan"));
        $package_resi = strtoupper(get_raw_body("no_resi"));

        if($package_id != null){
            $data = [
                "package_status" => "SELESAI",
                "package_tgl_selesai" => date("Y-m-d H:i:s"),
                "package_last_update" => date("Y-m-d H:i:s")
            ];
            $this->db->where("package_id", $package_id);
            $this->db->update("package", $data);

            $resp = [
                "success" => true,
                "message" => "Konfirmasi penerimaan berhasil. Silahkan menyelesaikan pembayaran sebelum melakukan pemesanan selanjutnya, terimakasih.",
                "data" => $data,
                "total" => 1
            ];
        }

        j_encode($resp, "raw");
    }
}
