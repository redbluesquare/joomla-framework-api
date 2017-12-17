<?php
namespace App\Models;
use App\Models\DefaultModel;
use Joomla\Application;


class ProfilesModel extends DefaultModel
{
	protected $_published 	= 1;
	protected $_location	= null;
	protected $_token = 'ksdbvskob0vwfb8BKBKS8VSFLFFPANVVOFd1nspvpwru8r8rB72r8r928t'; 
	
	
	protected function _buildQuery()
  	{
  		$query = $this->db->getQuery(true);
  		$query->select('u.id, u.username, u.first_name, u.last_name, u.email')
  			->from($this->db->quoteName('#__users', 'u'))
  			->group('u.id');
		return $query;
	}	
	protected function _buildWhere(&$query, $val)
	{
		if(is_int($val) > 0)
		{
			$query->where('u.id = '.(int)$val);
		}
		if($this->input->get('myId', null)!=null)
		{
			$query->where('u.id = '.(int)$this->input->get('myId', null));
		}
		
		return $query;
	}

	public function register()
	{
		if(($this->input->getMethod()=='POST') && ($this->_token == $this->input->get("apptoken", null,'string')))
		{
			$fname = $this->input->get("fname", null,'string');
			$lname = $this->input->get("lname", null,'string');
			$email = $this->input->get("email", null,'string');
			$username = $this->input->get("username", null,'string');
			$password = $this->input->get("password", null, "string");
			$password1 = $this->input->get("password1", 1, "string");
			if($password != $password1)
			{
				$obj = array("success" => false, "msg" => "passwords do not match");
				return $obj;
			}
			$date = date("Y-m-d H:i:s");
			$options = [
					'cost' => 8,
			];

			$data = array(
					$fname,
					$lname,
					$username,
					$email,
					password_hash($password, PASSWORD_BCRYPT,$options),
					$date,
					json_encode(array('authentication'=>base64_encode(password_hash($password, PASSWORD_BCRYPT,$options))))
			);
			if($this->getUser(null,$email,null) != false)
			{
				$obj = array("success" => false, "msg" => "user already exists");
			}
			else {
				//Save user
				$columns = array("first_name","last_name", "username", "email", "password", "registerDate", "params" );
				$result = $this->insert("#__ddc_users", $columns, $data);
				$obj = $result;
			}
			
		}else 
		{
			$obj = array("success" => false, "msg" => "request did not authenticate");
		}
	
		return $obj;
	}

	public function getUser($id = null, $email = null, $username = null){
		
		$user = false;
		if($id!=null)
		{
			$user = $this->getItemById($id);
		}
		if(($email!=null) && ($user == false))
		{
			$user = $this->validate_user_email($email);
		}
		if(($username!=null) && ($email == false))
		{
			$user = $this->getItemByAlias($username);
		}
		
		return $user;
	}
	
}