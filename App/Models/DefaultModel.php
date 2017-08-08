<?php

namespace App\Models;
use Joomla\Input\Input;
use Joomla\Model\AbstractDatabaseModel;
use Joomla\Database\DatabaseDriver;
use Joomla\Application\Joomla\Application;
use Joomla\Session\Session;
use App\Models\ProductsModel;


class DefaultModel extends AbstractDatabaseModel
{
	protected $input;
	protected $app;
	protected $session;
	protected $user;
	protected $limitstart   = 0;
	protected $limit        = 100;
	public function __construct(Input $input, DatabaseDriver $db)
	{
		parent::__construct($db);
		$this->session = new Session();
		$this->input = $input;
	}

	
	public function insert($table, $columns, $data)
	{
		//if($this->input->getMethod()=='POST'){
			$query = $this->db->getQuery(true)
				->insert($this->db->qn($table))
				->columns($this->db->qn($columns))
				->values(implode(",",$this->db->q($data)));
			$this->db->setQuery($query);
			$result = $this->db->execute();
			if($result){
				$query = $this->db->getQuery(true)
					->select('LAST_INSERT_ID() as id')
					->from($table);
				$this->db->setQuery($query);
				$result = $this->db->loadObject();
			}
			$result->success = true;
			return $result;
		//}else{
			$result = new \stdClass();
			$result->success = false;
			return false;
		//}
			
	}
	
	public function update($table, $fields, $conditions)
	{
		if($this->input->getMethod()=='POST'){
			$query = $this->db->getQuery(true)
			->update($this->db->qn($table))
			->set($fields)
			->where($conditions);
			$this->db->setQuery($query);
			$result = $this->db->execute();
			return $result;
		}else{
			$result = new \stdClass();
			$result->success = false;
			return false;
		}
	}
	
	public function validate_user_email($user_email)
	{
		$query = $this->db->getQuery(true)
			->select('u.id, u.username, u.name, u.email')
			->from('#__users as u')
			->where('(u.email=' . $this->db->quote($user_email). ') And (activation = 0) And (block = 0)');
		$this->db->setQuery($query);
		$result = $this->db->loadObject();
		
		return $result;
		 
	}
	
	public function getItemById($id1 = null, $id2 = null, $id3 = null)
	{
		//if($this->input->getMethod()=='GET')
		//{
			$query = $this->db->getQuery(true);
			
			$query = $this->_buildQuery();
			$this->_buildWhere($query, $id1, $id2, $id3);
			$this->db->setQuery($query);
			return $this->db->setQuery($query)->loadObject();
		//}
		
	}
	
	public function getItemByAlias($alias)
	{
		if($this->input->getMethod()=='GET')
		{
			$query = $this->db->getQuery(true);
			
			$query = $this->_buildQuery();
			$this->_buildWhere($query, $alias);
			$this->db->setQuery($query);
		}
		return $this->db->setQuery($query)->loadObject();
	}
	
	public function listItems($pc = null, $id = null)
  	{
  		$query = $this->db->getQuery(true);
  		$query = $this->_buildQuery();
  		$this->_buildWhere($query, $pc, $id);
  
  		return $this->db->setQuery($query, $this->limitstart, $this->limit)->loadObjectList();
 	}
  
  	protected function _getList($query, $limitstart = 0, $limit = 0)
  	{
  		$result = $this->db->setQuery($query, $limitstart, $limit)->loadObjectList();
  
  		return $result;
  	}
  	
  	
  	public function getDistance($lat1, $lon1, $lat2, $lon2, $unit) {
  	
  		$theta = $lon1 - $lon2;
  		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  		$dist = acos($dist);
  		$dist = rad2deg($dist);
  		$miles = $dist * 60 * 1.1515;
  		$unit = strtoupper($unit);
  	
  		if ($unit == "K") {
  			return ($miles * 1.609344);
  		} else if ($unit == "N") {
  			return ($miles * 0.8684);
  		} else {
  			return $miles;
  		}
  	}
  	
  	public function getProductPrice($id=null)
  	{
  		$model = new ProductsModel($this->input, $this->db);
  		$item = $model->getItemById((int)$id);
  	
  		$unitPrice = $item->product_price;
  		$weight = $item->product_weight;
  		$priceWeightBased = $model->getpartjsonfield($item->product_params,'price_weight_based');
  		$weightUOM = $item->product_weight_uom;
  		if($priceWeightBased == 1)
  		{
  			if($weightUOM=='grams')
  			{
  				$factor = $weight/1000;
  				$unitPrice = $item->product_price*$factor;
  			}
  			if($weightUOM=='kg')
  			{
  				$factor = $weight/1;
  				$unitPrice = $item->product_price*$factor;
  			}
  			if($weightUOM=='ounce')
  			{
  				$factor = $weight/35.27396;
  				$unitPrice = $item->product_price*$factor;
  			}
  		}
  		return $unitPrice;
  	}
  	public function getpartjsonfield($string,$part)
  	{
  		$prod_params = json_decode($string, true);
  		$item = (string)$prod_params[$part];
  		return $item;
  	}
  	public function randStrGen($len){
  		$result = "";
  		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  		$charArray = str_split($chars);
  		for($i = 0; $i < $len; $i++){
  			$randItem = array_rand($charArray);
  			$result .= "".$charArray[$randItem];
  		}
  		return $result;
  	}
}