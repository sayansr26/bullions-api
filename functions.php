<?php

require_once("Rest.inc.php");
require_once("db.php");

class functions extends REST
{
    private $mysqli = NULL;
    private $db = NULL;

    public function __construct($db)
    {
        parent::__construct();
        $this->db = $db;
        $this->mysqli = $db->mysqli;
    }

    public function checkConnection()
    {
        if (mysqli_ping($this->mysqli)) {
            $respon = array(
                'status' => 'ok', 'databse' => 'connected'
            );
            $this->response($this->json($respon), 200);
        } else {
            $respon = array(
                'status' => 'failed', 'database' => 'not connected'
            );
            $this->response($this->json($respon), 404);
        }
    }

    public function checkStat()
    {
        include('include/config.php');

        if (isset($_GET['phone'])) {

            $phone = $_GET['phone'];

            $query = "SELECT * FROM user_data WHERE phone = '$phone'";
            $sql = mysqli_query($connect, $query);

            if (mysqli_num_rows($sql) > 0) {
                $set['success'] = '1';
                $set['result'] = array('msg' => 'number exist');
                echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
            } else {
                $set['success'] = '0';
                $set['result'] = array('msg' => 'number not exist');
                echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function userRegister()
    {
        include('include/config.php');
        include('include/datapilot.php');

        if (isset($_GET['name'])) {
            $set['success'] = '1';
            $data = array(
                'name' => $_GET['name'],
                'email' => $_GET['email'],
                'phone' => $_GET['phone'],
                'wallet_balance' => '0',
                'withdrawl_balance' => '0',
                'withdrawl_balance' => '0',
                'gold_balance' => '0',
                'silver_balance' => '0',
                'password' => md5('123456'),
                'date' => date('Y-m-d')
            );
            $qry = Insert('user_data', $data);
            $set['result'] = array('msg' => 'Register successfully !');
            echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
            die();
        } else {
            header('Content-Type: application/json; charset=utf-8');
            $json = json_encode($set);

            echo $json;
            exit;
        }
    }

    public function userLogin()
    {
        include('include/config.php');


        if (isset($_GET['phone'])) {
            $set['success'] = '1';
            $set['result'] = array('msg' => 'login success !');
            echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
            die();
        } else {
            header('Content-Type: application/json; charset=utf-8');
            $json = json_encode($set);

            echo $json;
            exit;
        }
    }

    public function getUserData()
    {
        include('include/config.php');

        if (isset($_GET['phone'])) {
            $query = "SELECT * FROM user_data WHERE phone = '" . $_GET['phone'] . "'";
            $sql = mysqli_query($connect, $query);
            $row = mysqli_fetch_assoc($sql);

            if (mysqli_num_rows($sql) > 0) {
                $set['success'] = '1';
                $set['result'] = array(
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'wallet_balance' => $row['wallet_balance'],
                    'withdrawl_balance' => $row['withdrawl_balance'],
                    'gold_balance' => $row['gold_balance'],
                    'silver_balance' => $row['silver_balance'],
                    'profile_status' => $row['profile_status'],
                );
                echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
                die();
            } else {
                $set['success'] = '0';
                $set['result'] = array(
                    'msg' => 'User not found',
                );
                echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
                die();
            }
        } else {
            header('Content-Type: application/json; charset=utf-8');
            $json = json_encode($set);

            echo $json;
            exit;
        }
    }

    public function getTransections()
    {
        include('include/config.php');

        $query = "SELECT * FROM transections WHERE user = '" . $_GET['phone'] . "' ORDER BY id DESC";
        $sel = mysqli_query($connect, $query);

        if (mysqli_num_rows($sel) > 0) {
            $set['success'] = '1';
            while ($row = mysqli_fetch_array($sel)) {
                $set['result'][] = array(
                    'id' => $row['id'],
                    'user' => $row['user'],
                    'asset' => $row['asset'],
                    'amount' => $row['amount'],
                    'total' => $row['total'],
                    'pay_id' => $row['pay_id'],
                    'order_id' => $row['order_id'],
                    'type' => $row['type'],
                    'date' => $row['date'],
                );
            }
            header('Content-Type: application/json; charset=utf-8');
            $json = json_encode($set);

            echo $json;

            exit;
        } else {
            $set['success'] = '0';
            $set['result'] = array(
                'msg' => 'No transection details found',
            );
            $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
            die();
        }

        header('Content-Type: application/json; charset=utf-8');
        $json = json_encode($set);

        echo $json;

        exit;
    }

    public function buyProduct()
    {
        include('include/config.php');
        include('include/datapilot.php');

        if (isset($_GET['phone'])) {
            $set['success'] = '1';
            $goldbalance = $_GET['gold_balance'];
            $silverbalance = $_GET['silver_balance'];
            $quantity = $_GET['quantity'];
            $newgold  =  $goldbalance + $quantity;
            $newsilver = $silverbalance + $quantity;
            $phone = $_GET['phone'];
            $data = array(
                'user' => $_GET['phone'],
                'asset' => $_GET['asset'],
                'amount' => $_GET['amount'],
                'total' => $_GET['total'],
                'pay_id' => $_GET['pay_id'],
                'order_id' => $_GET['order_id'],
                'type' => 'buy',
                'date' => date('Y-m-d')
            );
            $udata = array(
                'gold_balance' => $newgold,
                'silver_balance' => $newsilver,
                'wallet_balance' => '0'
            );
            $insert = Insert('transections', $data);
            $upgrade = Update('user_data', $udata, "WHERE phone = '$phone'");
            $set['result'] = array('msg' => 'Product Buy Successfuly !');
            echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
            die();
        } else {
            header('Content-Type: application/json; charset=utf-8');
            $json = json_encode($set);

            echo $json;

            exit;
        }
    }

    public function sellProduct()
    {
        include('include/config.php');
        include('include/datapilot.php');

        if (isset($_GET['phone'])) {
            $set['success'] = '1';
            $goldbalance = $_GET['gold_balance'];
            $silverbalance = $_GET['silver_balance'];
            $balance = $_GET['withdrawl_balance'];
            $quantity = $_GET['quantity'];
            $total = $_GET['total'];
            $phone = $_GET['phone'];
            $newWithdrawl = $balance + $total;
            $newgold  = $goldbalance - $quantity;
            $newsilver = $silverbalance - $quantity;
            $data = array(
                'user' => $_GET['phone'],
                'asset' => $_GET['asset'],
                'amount' => $_GET['amount'],
                'total' => $_GET['total'],
                'pay_id' => 'none',
                'order_id' => 'none',
                'type' => 'sell',
                'date' => date('Y-m-d')
            );
            $udata = array(
                'gold_balance' => $newgold,
                'silver_balance' => $newsilver,
                'wallet_balance' => '0',
                'withdrawl_balance' => $newWithdrawl,
            );
            $insert = Insert('transections', $data);
            $update = Update('user_data', $udata, "WHERE phone = '$phone'");
            $set['result'] = array('msg' => 'Product Sell Successfuly !');
            echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
            die();
        } else {
            header('Content-Type: application/json; charset=utf-8');
            $json = json_encode($set);

            echo $json;

            exit;
        }
    }

    public function createWithdrawl()
    {
        include('include/config.php');
        include('include/datapilot.php');

        if (isset($_GET['phone'])) {
            $phone = $_GET['phone'];
            $balance = $_GET['balance'];
            $amount = $_GET['amount'];
            $newAmout = $balance - $amount;
            $data = array(
                'user' => $_GET['phone'],
                'account' => $_GET['account'],
                'ifsc' => $_GET['ifsc'],
                'banificary' => $_GET['banificary'],
                'amount' => $_GET['amount'],
                'status' => 'pending',
                'date' => date('Y-m-d')
            );
            $udata = array(
                'withdrawl_balance' => $newAmout
            );
            $insert = Insert('withdrawls', $data);
            $update = Update('user_data', $udata, "WHERE phone = '$phone'");
            $set['success'] = '1';
            $set['result'] = array('msg' => 'Withdraw request recived !');
            echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
            die();
        } else {
            header('Content-Type: application/json; charset=utf-8');
            $json = json_encode($set);

            echo $json;

            exit;
        }
    }

    public function accountDetails()
    {
        include('include/config.php');

        if (isset($_GET['phone'])) {
            $query = "SELECT * FROM accounts WHERE phone = '" . $_GET['phone'] . "'";
            $sel = mysqli_query($connect, $query);
            $row = mysqli_fetch_assoc($sel);

            if (mysqli_num_rows($sel) > 0) {
                $set['success'] = '1';
                $set['result'] = array(
                    'banificary' => $row['banificary'],
                    'account' => $row['account'],
                    'ifsc' => $row['ifsc'],
                    'phone' => $row['phone'],
                    'date' => $row['date'],
                );
                echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
                die();
            } else {
                $set['success'] = '0';
                $set['result'] = array('msg' => 'need to create account');
                echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
                die();
            }
        } else {
            header('Content-Type: application/json; charset=utf-8');
            $json = json_encode($set);
            echo $json;

            exit;
        }
    }

    public function createAccount()
    {
        include('include/config.php');
        include('include/datapilot.php');

        if (isset($_GET['phone'])) {
            $set['success'] = '1';
            $data = array(
                'banificary' => $_GET['banificary'],
                'account' => $_GET['account'],
                'ifsc' => $_GET['ifsc'],
                'phone' => $_GET['phone'],
                'date' => date("Y-m-d")
            );
            $query = Insert('accounts', $data);
            $set['result'] = array('msg' => 'account created successfully !');
            echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
            die();
        } else {
            header('Content-Type: application/json; charset=utf-8');
            $json = json_encode($set);

            echo $json;

            exit;
        }
    }

    public function getExchnageRate()
    {
        include('include/config.php');

        $query = "SELECT * FROM charges";
        $sel = mysqli_query($connect, $query);
        $row = mysqli_fetch_assoc($sel);

        if (mysqli_num_rows($sel) > 0) {
            $set['success'] = '1';
            $set['result'] = array(
                'currency' => $row['currency'],
                'rate' => $row['rate'],
            );
            echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
            die();
        } else {
            $set['success'] = '0';
            $set['result'] = array('msg' => 'exchnage rate not found');
            echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
            die();
        }

        header('Content-Type: application/json; charset=utf-8');
        $json = json_encode($set);

        echo $json;

        exit;
    }

    public function getProductRate()
    {
        include('include/config.php');

        $query = "SELECT * FROM product_rate";
        $sel = mysqli_query($connect, $query);
        $row = mysqli_fetch_assoc($sel);

        if (mysqli_num_rows($sel) > 0) {
            $set['success'] = '1';
            $set['result'] = array(
                'product' => $row['product'],
                'price' => $row['price'],
            );
            echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
            die();
        } else {
            $set['success'] = '0';
            $set['result'] = array('msg' => 'exchnage rate not found');
            echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
            die();
        }

        header('Content-Type: application/json; charset=utf-8');
        $json = json_encode($set);

        echo $json;

        exit;
    }
}
