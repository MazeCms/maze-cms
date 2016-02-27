<?php

namespace maze\commet;

interface DriverHttpInterface {
    
    public function add($id, array $message);
    
    public function findByID($id);
    
    public function delete($id);
}
