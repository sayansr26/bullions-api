<?php

require_once("Rest.inc.php");
require_once("db.php");
require_once("functions.php");

class API extends REST
{
    private $functions = NULL;
    private $db = NULL;

    public function __construct()
    {
        $this->db = new DB();
        $this->functions = new functions($this->db);
    }

    public function check_connection()
    {
        $this->functions->checkConnection();
    }

    /*
     * All Api Related android client --------------------------------------------------
     */

    private function check_stat()
    {
        $this->functions->checkStat();
    }

    private function user_register()
    {
        $this->functions->userRegister();
    }

    private function user_login()
    {
        $this->functions->userLogin();
    }

    private function get_user_data()
    {
        $this->functions->getUserData();
    }

    private function get_transections()
    {
        $this->functions->getTransections();
    }

    private function buy_product()
    {
        $this->functions->buyProduct();
    }

    private function sell_product()
    {
        $this->functions->sellProduct();
    }

    private function create_withdraw()
    {
        $this->functions->createWithdrawl();
    }


    private function account_details()
    {
        $this->functions->accountDetails();
    }

    private function create_account()
    {
        $this->functions->createAccount();
    }

    private function get_exchange_rate()
    {
        $this->functions->getExchnageRate();
    }

    private function get_product_rate()
    {
        $this->functions->getProductRate();
    }


    /*
      * End of Api Transactions ---------------------------------------------------------
      */

    public function processApi()
    {
        if (isset($_REQUEST['x']) && $_REQUEST['x'] != "") {
            $func = strtolower(trim(str_replace("/", "", $_REQUEST['x'])));
            if ((int)method_exists($this, $func) > 0) {
                $this->$func();
            } else {
                echo 'processApi - method not exists';
                exit;
            }
        } else {
            echo 'processApi - method not exists';
            exit;
        }
    }
}

// Intiate Library
$api = new API;
$api->processApi();
