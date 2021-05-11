<?php

class Model
{

    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function fetchAllProducts()
    {
        $products = $this->db->select("SELECT * FROM product");
        return $products;
    }

    public function fetchProductById($id)
    {
        $statement = "SELECT * FROM product WHERE id = :id";
        $params = array(":id" => $id);
        $product = $this->db->select($statement, $params);
        // print_r($product);
        return $product[0] ?? false;
    }

    public function fetchCustomerById($id)
    {

        $statement = "SELECT * FROM customers WHERE customer_id=:id";
        $parameters = array(':id' => $id);
        $customer = $this->db->select($statement, $parameters);
        return $customer[0] ?? false;
    }

    public function fetchCustomerByEmail($email)
    {

        $statement = "SELECT * FROM customer WHERE email=:email";
        $parameters = array(':email' => $email);
        $customer = $this->db->select($statement, $parameters);
        return $customer[0] ?? false;
    }



    public function insertOrder($customer_id, $order_id)
    {
        $customer = $this->fetchCustomerById($customer_id);
        if (!$customer) return false;

        $statement = "INSERT INTO order (customer_id, order_id)  
                      VALUES (:customer_id, :order_id)";
        $parameters = array(
            ':customer_id' => $customer_id,
            ':order_id' => $order_id
        );

        // Ordernummer
        $lastInsertId = $this->db->insert($statement, $parameters);

        return array('customer' => $customer, 'lastInsertId' => $lastInsertId);
    }


    public function loginCustomer($email, $password)
    {
        $customer = $this->fetchCustomerByEmail($email);
        if (!$customer) {
            $html = <<< HTML
            <div class="my-2 alert alert-danger">
                Email already taken!
            </div>
            HTML;

            echo $html;
            exit();
        }


        $statement = "SELECT * FROM customer WHERE email=:email";

        $parameters = array(
            ':email' => $email,
        );

        $customer = $this->db->select($statement, $parameters);

        $userId = $customer[0]['id'];
        $email = $customer[0]['email'];
        $dbPassword = $customer[0]['password'];

        if (!password_verify($password, $dbPassword)) {
            $html = <<< HTML
            <div class="my-2 alert alert-danger">
                Wrong username or password!
            </div>
            HTML;

            echo $html;
            exit();
        }

        if (!isset($_SESSION))
            session_start();
        // Store data in session variables
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = $userId;
        $_SESSION["email"] = $email;

        header("location: index.php");
    }




    public function insertCustomer($name, $email, $password)
    {
        $customer = $this->fetchCustomerByEmail($email);
        if ($customer) {
            $html = <<< HTML
            <div class="my-2 alert alert-danger">
                User already exist!
            </div>
            HTML;

            echo $html;
            exit();
        }

        $statement = "INSERT INTO customer (name, email, password)  
                      VALUES (:name, :email, :password)";
        $parameters = array(
            ':name' => $name,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT)
        );
        $this->db->insert($statement, $parameters);
        // Ordernummer
        //        $lastInsertId = $this->db->insert($statement, $parameters);

        //   return array('customer' => $customer, 'lastInsertId' => $lastInsertId);
    }
}
