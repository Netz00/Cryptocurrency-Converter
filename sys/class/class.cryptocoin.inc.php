<?php

/*!
 * This class handles admin account managing
 */

class cryptocoin extends db_connect
{

    private $requestFrom = 0;
    private $id = 0;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);
    }

    public function setCoinsStatus($cryptocoinID, $cryptocoin_state)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN,
        );

        $stmt = $this->db->prepare("UPDATE cryptocoins SET state = (:state) WHERE id = (:invoiceID)");
        $stmt->bindParam(":state", $cryptocoin_state, PDO::PARAM_INT);
        $stmt->bindParam(":invoiceID", $cryptocoinID, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->execute()) {
            $result['error'] = false;
            $result['error_code'] = ERROR_SUCCESS;
        }

        return $result;
    }

    // get all cryptocoins

    public function get()
    {

        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN,
            "items" => array()
        );

        $stmt = $this->db->prepare(
            "SELECT * FROM cryptocoins"
        );

        if ($stmt->execute()) {

            $result['error'] = false;
            $result['error_code'] = ERROR_SUCCESS;

            while ($row = $stmt->fetch()) {

                $itemInfo = array(
                    "id" => $row['id'],
                    "state" => $row['state'],
                    "name" =>  $row['name'],
                    "symbol" => $row['symbol'],
                    "codename" => $row['codename'],
                );
                array_push($result['items'], $itemInfo);
                unset($itemInfo);
            }
        }

        return $result;
    }






    // get all cryptocoins

    public function getOnlyCodeOfActive()
    {

        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN,
            "items" => array()
        );

        $stmt = $this->db->prepare(
            "SELECT codename FROM cryptocoins WHERE state = 1"
        );

        if ($stmt->execute()) {

            $result['error'] = false;
            $result['error_code'] = ERROR_SUCCESS;

            while ($row = $stmt->fetch()) {

                array_push($result['items'], $row['codename']);
                unset($itemInfo);
            }
        }

        return $result;
    }




    // add new cryptocoin

    public function create($name, $symbol, $codename, $state = 0)
    {
        $result = array(
            'error' => true,
            "error_code" => ERROR_UNKNOWN
        );


        $name = helper::clearText($name);
        $symbol = helper::clearText($symbol);
        $codename = helper::clearText($codename);
        $state = helper::clearText($state);



        $stmt = $this->db->prepare("INSERT INTO cryptocoins (name, symbol, codename, state) value (:name, :symbol, :codename, :state)");
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":symbol", $symbol, PDO::PARAM_STR);
        $stmt->bindParam(":codename", $codename, PDO::PARAM_STR);
        $stmt->bindParam(":state", $state, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array(
                "error" => false,
                'error_description' => 'Cryptocoin added Successfully!'
            );
        }

        return $result;
    }





    // delete cryptocoinS


    public function delete($rowsIDs)
    {
        $result = array(
            'error' => true,
            "error_code" => ERROR_UNKNOWN
        );


        $in  = str_repeat('?,', count($rowsIDs) - 1) . '?';

        // PDO is not good with arrays :(

        $stmt = $this->db->prepare("DELETE FROM cryptocoins WHERE id IN ($in)");

        if ($stmt->execute($rowsIDs)) {

            $result = array(
                "error" => false,
                'error_description' => 'Cryptocoin removed Successfully!'
            );
        }

        return $result;
    }




    // update cryptocoin state


    public function patch($id, $state = 0)
    {
        $result = array(
            'error' => true,
            "error_code" => ERROR_UNKNOWN
        );


        $id = helper::clearText($id);
        $state = helper::clearText($state);


        $stmt = $this->db->prepare("UPDATE cryptocoins SET state = (:state) WHERE ID = (:id)");

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":state", $state, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array(
                "error" => false,
                'error_description' => 'Cryptocoin added Successfully!'
            );
        }

        return $result;
    }
}
