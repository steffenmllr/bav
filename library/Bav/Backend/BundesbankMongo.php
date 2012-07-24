<?php

/**
 * Copyright (C) 2012  Steffen Müller <steffen@digitalhero.me>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *
 * @package Backend
 * @author Steffen Müller <steffen@digitalhero.me>
 * @copyright Copyright (C) 2012 Dennis Lassiter
 */

namespace Bav\Backend;

use Bav\BackendInterface;

class BundesbankMongo implements BackendInterface
{
    

    protected $mongo;
    protected $collection;
    protected $instances = array();
    
    public function __construct($connection, $db = 'kontodaten', $collection = "bav_bank")
    {
        try {
            $this->mongo = new \Mongo($connection.'/'.$db);
            $this->collection = $this->mongo->selectDB($db)->selectCollection($collection);
        } catch (MongoConnectionException $e) {
            throw new BavException\IoException("Unable to connect to MongoDB {$connection} with DB: {$db}");        
        }          

    }
    
    /**
     * Check if Bank exists
     * 
     * @param int $bankID
     * @return boolean 
     */
    public function bankExists($bankID)
    {
        try {
            $this->getBank($bankID);
            return true;
        } catch (Exception\BankNotFoundException $e) {
            return false;
        }
    }
    
    
    public function getAllBanks()
    {
        
    }
    
    public function getBank($bankId)
    {
        if (! isset($this->instances[$bankId])) {
            $this->instances[$bankId] = $this->getNewBank($bankId);

        }
        return $this->instances[$bankId];
    }
    
    
    /**
     * @throws BAV_DataBackendException_IO
     * @throws BAV_DataBackendException_BankNotFound
     * @param String $bankId
     * @see BAV_DataBackend::getNewBank()
     * @return BAV_Bank
     */
    protected function getNewBank($bankId) {        
        try {        
            $result = $this->collection->findOne(array('_id' => (string)$bankId));            

            if ($result === null) {
                throw new Exception\BankNotFoundException("Bank with ID {$bankId} not found");
            }

            // Set Bank
            $bank = new \Bav\Bank($result['_id'], 'De\\System' . $result['validator']);

            // Get Agencies
            foreach($result['banks'] as $id => $agency) {
                $agencies[] = new \Bav\Bank\Agency($id, $agency['name'], $agency['shortTerm'], $agency['city'], $agency['postcode'], $agency['bic'], $agency['pan'], $result['mainAgency']);
            }

            // Set agencys            
            $bank->setAgencies($agencies);

            return $bank;

        } catch (\MongoCursorException $e) {
            throw new \Bav\Exception\IoException();     
        } 
    }

    protected function getAgency($id) {

    }
    



}