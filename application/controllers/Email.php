<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email extends CI_Controller {

	function __construct() {
        parent::__construct();
    }

	public function index()
	{
            /*$this->pdf->load_view('example_to_pdf');
            $this->pdf->render();
            $this->pdf->stream("name-file.pdf");*/

            $this->pdf->loadHtml($this->load->view("example_to_pdf", array(), true));
            $this->pdf->render();
            // $this->pdf->stream("cahya.pdf");
            $this->pdf->output();
    }

    public function send()
    {
        $html = $this->load->view("example_to_pdf", array(), true);
        $s = zmailer([
            "to"        => "dyazincahya@gmail.com",
            "subject"   => "Test mail report",
            "message"   => $html
        ]);

        var_dump($s);
    }

    public function download_report($start, $end, $report_tipe, $status)
    {
        $star_date = $start;
        $end_date = $end;
        $report_tipe = $report_tipe;
        $status = $status;

        /*$customer_where = "customer_reg_date >= CAST('". $star_date ."' AS DATE) AND customer_reg_date <= CAST('". $end_date ."' AS DATE)";
        $customer = $this->db->query("
            SELECT * FROM customer
            WHERE " . $customer_where)->result_array();

        $package_where = "package_created >= CAST('". $star_date ."' AS DATE) AND package_created <= CAST('". $end_date ."' AS DATE)";
        $package = $this->db->query("
            SELECT * FROM package p
            LEFT JOIN customer c ON c.customer_id=p.package_customer_id
            LEFT JOIN kurir k ON k.kurir_id=p.package_kurir_id
            WHERE " . $package_where)->result_array();

        $customer_non_active = $this->db->get_where("customer", "customer_status='0' AND (". $customer_where . ")")->num_rows();
        $customer_active = $this->db->get_where("customer", "customer_status='1' AND (". $customer_where . ")")->num_rows();
        $customer_pending = $this->db->get_where("customer", "customer_status='2' AND (". $customer_where . ")")->num_rows();

        $package_request = $this->db->get_where("package", "package_status='REQUEST' AND (". $package_where . ")")->num_rows();
        $package_pickup = $this->db->get_where("package", "package_status='PICKUP' AND (". $package_where . ")")->num_rows();
        $package_karantina = $this->db->get_where("package", "package_status='KARANTINA' AND (". $package_where . ")")->num_rows();
        $package_pengiriman = $this->db->get_where("package", "package_status='PENGIRIMAN' AND (". $package_where . ")")->num_rows();
        $package_selesai = $this->db->get_where("package", "package_status='SELESAI' AND (". $package_where . ")")->num_rows();

        $data = [];
        $data['package'] = $package;
        $data['customer'] = $customer;
        $data['summary'] = [
            "cust_nonactive" => $customer_non_active,
            "cust_active" => $customer_active,
            "cust_pending" => $customer_pending,
            "pack_request" => $package_request,
            "pack_pickup" => $package_pickup,
            "pack_karantina" => $package_karantina,
            "pack_pengiriman" => $package_pengiriman,
            "pack_selesai" => $package_selesai
        ];*/

        switch ($report_tipe) {
            case 'CUSTOMER':
                $data = $this->get_data_customer($star_date, $end_date, $status, true);
                $this->pdf->load_view('report_template_customer_pdf', $data);
                $this->pdf->render();
                $this->pdf->stream("REPORT ". $report_tipe . " " . $status . " " . $start . " - " . $end);
                break;

            case 'PACKAGE':
                $data = $this->get_data_package($star_date, $end_date, $status, true);
                $this->pdf->load_view('report_template_package_pdf', $data);
                $this->pdf->render();
                $this->pdf->stream("REPORT ". $report_tipe . " " . $status . " " . $start . " - " . $end);
                break;
            
            default:
                $data = $this->get_all_report($star_date, $end_date, true);
                $this->pdf->load_view('report_template_pdf', $data);
                $this->pdf->render();
                $this->pdf->stream("ALL REPORT " . $start . " - " . $end);
                break;
        }

        
    }

    public function preview_report()
    {
        $star_date = "2020-02-01";
        $end_date = "2020-02-28";
        $report_tipe = "PACKAGE"; //customer //package //all
        $status = "PENGIRIMAN";

        switch ($report_tipe) {
            case 'CUSTOMER':
                $this->get_data_customer($star_date, $end_date, $status);
                break;

            case 'PACKAGE':
                $this->get_data_package($star_date, $end_date, $status);
                break;
            
            default:
                $this->get_all_report($star_date, $end_date);
                break;
        }
    }

    public function send_report()
    {
        $resp = default_response("Email laporan tidak terkirim!");

        $star_date = get_raw_body("star_date");
        $end_date = get_raw_body("end_date");
        $report_tipe = strtoupper(get_raw_body("report_tipe"));
        $status = strtoupper(get_raw_body("status"));

        $to = get_raw_body("to");
        $cc = get_raw_body("cc");
        $bcc = get_raw_body("bcc");


        /*$customer_where = "customer_reg_date >= CAST('". $star_date ."' AS DATE) AND customer_reg_date <= CAST('". $end_date ."' AS DATE)";
        $customer = $this->db->query("
            SELECT * FROM customer
            WHERE " . $customer_where)->result_array();

        $package_where = "package_created >= CAST('". $star_date ."' AS DATE) AND package_created <= CAST('". $end_date ."' AS DATE)";
        $package = $this->db->query("
            SELECT * FROM package p
            LEFT JOIN customer c ON c.customer_id=p.package_customer_id
            LEFT JOIN kurir k ON k.kurir_id=p.package_kurir_id
            WHERE " . $package_where)->result_array();

        $customer_non_active = $this->db->get_where("customer", "customer_status='0' AND (". $customer_where . ")")->num_rows();
        $customer_active = $this->db->get_where("customer", "customer_status='1' AND (". $customer_where . ")")->num_rows();
        $customer_pending = $this->db->get_where("customer", "customer_status='2' AND (". $customer_where . ")")->num_rows();

        $package_request = $this->db->get_where("package", "package_status='REQUEST' AND (". $package_where . ")")->num_rows();
        $package_pickup = $this->db->get_where("package", "package_status='PICKUP' AND (". $package_where . ")")->num_rows();
        $package_karantina = $this->db->get_where("package", "package_status='KARANTINA' AND (". $package_where . ")")->num_rows();
        $package_pengiriman = $this->db->get_where("package", "package_status='PENGIRIMAN' AND (". $package_where . ")")->num_rows();
        $package_selesai = $this->db->get_where("package", "package_status='SELESAI' AND (". $package_where . ")")->num_rows();

        $data = [];
        $data['package'] = $package;
        $data['customer'] = $customer;
        $data['summary'] = [
            "cust_nonactive" => $customer_non_active,
            "cust_active" => $customer_active,
            "cust_pending" => $customer_pending,
            "pack_request" => $package_request,
            "pack_pickup" => $package_pickup,
            "pack_karantina" => $package_karantina,
            "pack_pengiriman" => $package_pengiriman,
            "pack_selesai" => $package_selesai
        ];
        $data['url_download'] = site_url("email/download_report/" . $star_date . "/" . $end_date);
        $message = $this->load->view("report_template", $data, true);*/

        switch ($report_tipe) {
            case 'CUSTOMER':
                $subject = "LAPORAN ". $report_tipe ." ". $status ." KIKAN periode (" . rfdate($star_date, "d/m/Y") . " sampai " . rfdate($end_date, "d/m/Y") . ")";
                $data = $this->get_data_customer($star_date, $end_date, $status, true);
                $message = $this->load->view("report_template_customer", $data, true);
                break;

            case 'PACKAGE':
                $subject = "LAPORAN ". $report_tipe ." ". $status ." KIKAN periode (" . rfdate($star_date, "d/m/Y") . " sampai " . rfdate($end_date, "d/m/Y") . ")";
                $data = $this->get_data_package($star_date, $end_date, $status, true);
                $message = $this->load->view("report_template_package", $data, true);
                break;
            
            default:
                $subject = "SEMUA LAPORAN KIKAN periode (" . rfdate($star_date, "d/m/Y") . " sampai " . rfdate($end_date, "d/m/Y") . ")";
                $data = $this->get_all_report($star_date, $end_date, true);
                $message = $this->load->view("report_template", $data, true);
                break;
        }

        $mail_config = [
            "to"        => str_to_arr($to),
            "cc"        => str_to_arr($cc),
            "bcc"        => str_to_arr($bcc),
            "subject"   => $subject,
            "message"   => $message
        ];
        $mail_send = zmailer($mail_config);
        if($mail_send){
            $resp = [
                "success" => true,
                "message" => "Email laporan sudah terkirim",
                "data" => $mail_config,
                "total" => 1
            ];
        }

        j_encode($resp, "raw");
    }

    private function get_all_report($star_date, $end_date, $return=false){
        $customer_where = "customer_reg_date >= CAST('". $star_date ."' AS DATE) AND customer_reg_date <= CAST('". $end_date ."' AS DATE)";
        $customer = $this->db->query("
            SELECT * FROM customer
            WHERE " . $customer_where)->result_array();

        $package_where = "package_created >= CAST('". $star_date ."' AS DATE) AND package_created <= CAST('". $end_date ."' AS DATE)";
        $package = $this->db->query("
            SELECT * FROM package p
            LEFT JOIN customer c ON c.customer_id=p.package_customer_id
            LEFT JOIN kurir k ON k.kurir_id=p.package_kurir_id
            WHERE " . $package_where)->result_array();

        $customer_non_active = $this->db->get_where("customer", "customer_status='0' AND (". $customer_where . ")")->num_rows();
        $customer_active = $this->db->get_where("customer", "customer_status='1' AND (". $customer_where . ")")->num_rows();
        $customer_pending = $this->db->get_where("customer", "customer_status='2' AND (". $customer_where . ")")->num_rows();

        $package_request = $this->db->get_where("package", "package_status='REQUEST' AND (". $package_where . ")")->num_rows();
        $package_pickup = $this->db->get_where("package", "package_status='PICKUP' AND (". $package_where . ")")->num_rows();
        $package_karantina = $this->db->get_where("package", "package_status='KARANTINA' AND (". $package_where . ")")->num_rows();
        $package_pengiriman = $this->db->get_where("package", "package_status='PENGIRIMAN' AND (". $package_where . ")")->num_rows();
        $package_selesai = $this->db->get_where("package", "package_status='SELESAI' AND (". $package_where . ")")->num_rows();

        $data = [];
        $data['package'] = $package;
        $data['customer'] = $customer;
        $data['summary'] = [
            "cust_nonactive" => $customer_non_active,
            "cust_active" => $customer_active,
            "cust_pending" => $customer_pending,
            "pack_request" => $package_request,
            "pack_pickup" => $package_pickup,
            "pack_karantina" => $package_karantina,
            "pack_pengiriman" => $package_pengiriman,
            "pack_selesai" => $package_selesai
        ];
        $data['url_download'] = site_url("index.php/email/download_report/" . $star_date . "/" . $end_date . "/ALL/ALL");
        $data['period'] = $star_date . " sampai " . $end_date;

        if($return===false){
            $this->load->view("report_template", $data);
        } else {
            return $data;
        }
    }

    private function get_data_customer($star_date, $end_date, $tipe='ALL', $return=false){
        $data=[];
        switch ($tipe) {
            case "ACTIVE":
                $data['title'] = "CUSTOMER ACTIVE";
                $data['period'] = $star_date . " sampai " . $end_date;
                $data['url_download'] = site_url("index.php/email/download_report/" . $star_date . "/" . $end_date . "/CUSTOMER/ACTIVE");
                $customer_where = "(customer_reg_date >= CAST('". $star_date ."' AS DATE) AND customer_reg_date <= CAST('". $end_date ."' AS DATE)) AND customer_status=1";
                $customer = $this->db->query("SELECT * FROM customer WHERE " . $customer_where)->result_array();
                break;

            case "PENDING":
                $data['title'] = "CUSTOMER PENDING";
                $data['period'] = $star_date . " sampai " . $end_date;
                $data['url_download'] = site_url("index.php/email/download_report/" . $star_date . "/" . $end_date . "/CUSTOMER/PENDING");
                $customer_where = "(customer_reg_date >= CAST('". $star_date ."' AS DATE) AND customer_reg_date <= CAST('". $end_date ."' AS DATE)) AND customer_status=2";
                $customer = $this->db->query("SELECT * FROM customer WHERE " . $customer_where)->result_array();
                break;

            case "NON_ACTIVE":
                $data['title'] = "CUSTOMER NON ACTIVE";
                $data['period'] = $star_date . " sampai " . $end_date;
                $data['url_download'] = site_url("index.php/email/download_report/" . $star_date . "/" . $end_date . "/CUSTOMER/NON_ACTIVE");
                $customer_where = "(customer_reg_date >= CAST('". $star_date ."' AS DATE) AND customer_reg_date <= CAST('". $end_date ."' AS DATE)) AND customer_status=0";
                $customer = $this->db->query("SELECT * FROM customer WHERE " . $customer_where)->result_array();
                break;
            
            default: //ALL
                $data['title'] = "ALL CUSTOMER";
                $data['period'] = $star_date . " sampai " . $end_date;
                $data['url_download'] = site_url("index.php/email/download_report/" . $star_date . "/" . $end_date . "/CUSTOMER/ALL");
                $customer_where = "customer_reg_date >= CAST('". $star_date ."' AS DATE) AND customer_reg_date <= CAST('". $end_date ."' AS DATE)";
                $customer = $this->db->query("SELECT * FROM customer WHERE " . $customer_where)->result_array();
                break;
        }
        
        $data['customer'] = $customer;
        if($return===false){
            $this->load->view("report_template_customer", $data);
        } else {
            return $data;
        }
    }

    private function get_data_package($star_date, $end_date, $tipe='ALL', $return=false){
        $data=[];
        switch ($tipe) {
            case "REQUEST":
                $data['title'] = "PACKAGE REQUEST";
                $data['period'] = $star_date . " sampai " . $end_date;
                $data['url_download'] = site_url("index.php/email/download_report/" . $star_date . "/" . $end_date . "/PACKAGE/REQUEST");
                $package_where = "(package_created >= CAST('". $star_date ."' AS DATE) AND package_created <= CAST('". $end_date ."' AS DATE)) AND package_status='REQUEST'";
                $package = $this->db->query("SELECT * FROM package p
                    LEFT JOIN customer c ON c.customer_id=p.package_customer_id
                    LEFT JOIN kurir k ON k.kurir_id=p.package_kurir_id
                    WHERE " . $package_where)->result_array();
                break;

            case "PICKUP":
                $data['title'] = "PACKAGE PICKUP";
                $data['period'] = $star_date . " sampai " . $end_date;
                $data['url_download'] = site_url("index.php/email/download_report/" . $star_date . "/" . $end_date . "/PACKAGE/PICKUP");
                $package_where = "(package_created >= CAST('". $star_date ."' AS DATE) AND package_created <= CAST('". $end_date ."' AS DATE)) AND package_status='PICKUP'";
                $package = $this->db->query("SELECT * FROM package p
                    LEFT JOIN customer c ON c.customer_id=p.package_customer_id
                    LEFT JOIN kurir k ON k.kurir_id=p.package_kurir_id
                    WHERE " . $package_where)->result_array();
                break;

            case "KARANTINA":
                $data['title'] = "PACKAGE KARANTINA";
                $data['period'] = $star_date . " sampai " . $end_date;
                $data['url_download'] = site_url("index.php/email/download_report/" . $star_date . "/" . $end_date . "/PACKAGE/KARANTINA");
                $package_where = "(package_created >= CAST('". $star_date ."' AS DATE) AND package_created <= CAST('". $end_date ."' AS DATE)) AND package_status='KARANTINA'";
                $package = $this->db->query("SELECT * FROM package p
                    LEFT JOIN customer c ON c.customer_id=p.package_customer_id
                    LEFT JOIN kurir k ON k.kurir_id=p.package_kurir_id
                    WHERE " . $package_where)->result_array();
                break;

            case "PENGIRIMAN":
                $data['title'] = "PACKAGE SEDANG DALAM PENGIRIMAN";
                $data['period'] = $star_date . " sampai " . $end_date;
                $data['url_download'] = site_url("index.php/email/download_report/" . $star_date . "/" . $end_date . "/PACKAGE/PENGIRIMAN");
                $package_where = "(package_created >= CAST('". $star_date ."' AS DATE) AND package_created <= CAST('". $end_date ."' AS DATE)) AND package_status='PENGIRIMAN'";
                $package = $this->db->query("SELECT * FROM package p
                    LEFT JOIN customer c ON c.customer_id=p.package_customer_id
                    LEFT JOIN kurir k ON k.kurir_id=p.package_kurir_id
                    WHERE " . $package_where)->result_array();
                break;

            case "SELESAI":
                $data['title'] = "PACKAGE SELESAI";
                $data['period'] = $star_date . " sampai " . $end_date;
                $data['url_download'] = site_url("index.php/email/download_report/" . $star_date . "/" . $end_date . "/PACKAGE/SELESAI");
                $package_where = "(package_created >= CAST('". $star_date ."' AS DATE) AND package_created <= CAST('". $end_date ."' AS DATE)) AND package_status='SELESAI'";
                $package = $this->db->query("SELECT * FROM package p
                    LEFT JOIN customer c ON c.customer_id=p.package_customer_id
                    LEFT JOIN kurir k ON k.kurir_id=p.package_kurir_id
                    WHERE " . $package_where)->result_array();
                break;
            
            default: //ALL
                $data['title'] = "ALL PACKAGE";
                $data['period'] = $star_date . " sampai " . $end_date;
                $data['url_download'] = site_url("index.php/email/download_report/" . $star_date . "/" . $end_date . "/PACKAGE/ALL");
                $package_where = "(package_created >= CAST('". $star_date ."' AS DATE) AND package_created <= CAST('". $end_date ."' AS DATE))";
                $package = $this->db->query("SELECT * FROM package p
                    LEFT JOIN customer c ON c.customer_id=p.package_customer_id
                    LEFT JOIN kurir k ON k.kurir_id=p.package_kurir_id
                    WHERE " . $package_where)->result_array();
                break;
        }
        
        $data['package'] = $package;
        if($return===false){
            $this->load->view("report_template_package", $data);
        } else {
            return $data;
        }
    }
}
