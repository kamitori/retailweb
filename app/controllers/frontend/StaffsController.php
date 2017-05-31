<?php
namespace RW\Controllers;

use RW\Models\JTContact;

class StaffsController extends ControllerBase
{
	public function startShiftAction()
	{
		if (!$this->request->isAjax()) {
			return $this->error404();
		}
		$arrReturn = ['error'=>0, 'message'=>'Done.'];
		if(!$this->updateShift('start'))
		{
			$arrReturn = ['error'=>1, 'message'=>'Can not perform this action.'];
		}
		return $this->response($arrReturn);
	}

	public function endShiftAction()
	{
		if (!$this->request->isAjax()) {
			return $this->error404();
		}
		$arrReturn = ['error'=>0, 'message'=>'Done.'];
		if(!$this->updateShift('end'))
		{
			$arrReturn = ['error'=>1, 'message'=>'Can not perform this action.'];
		}
		if($arrReturn['error']==0){
			$arr_session_id = json_decode(file_get_contents("session_id.json"),true);
			foreach ($arr_session_id as $key => $value) {
				if($value==session_id()){
				  unset($arr_session_id[$key]);
				}
			}
			file_put_contents("session_id.json", json_encode($arr_session_id));
		}
		return $this->response($arrReturn);
	}

	public function checkPerformedShiftAction($s='start')
	{
		if (!$this->request->isAjax()) {
			return $this->error404();
		}
		$result = true;
		if($this->session->has('user'))
		{
			$user = $this->session->get('user');
			
			if($user['is_employee'] == '1')
			{
				$user_obj = JTContact::findFirst(array(
												'conditions'=>array('_id'=>$user['_id'])
											));
				if($user_obj)
				{
					$shifts = json_decode($user_obj->shifts, true);
					if($shifts == null) $shifts = array();
					$date = date('Y-m-d');
					$time = date('h:i:s');
					$f = false;
					foreach ($shifts as $key => $value) {
						if($value['date'] == $date)
						{
							if($value[$s.'_time'] == null)
							{
								$result = false;
							}        				
							$f = true;
							break;            			
						}
					}
					if(!$f) $result = false;
				}
			}			
		}
		return $this->response(['status'=>$result]);
	}

	function updateShift($s='start')
	{
		$user = $this->session->get('user');
		//pr($user);exit;		
		if($user['is_employee'] == '1')
		{
			$user_obj = JTContact::findFirst(array(
											'conditions'=>array('_id'=>$user['_id'])
										));
			if($user_obj)
			{
				$shifts = json_decode($user_obj->shifts, true);
				if($shifts == null) $shifts = array();
				$date = date('Y-m-d');
				$time = date('h:i:s');
				$f = false;
				foreach ($shifts as $key => $value) {
					if($value['date'] == $date)
					{
						if($value[$s.'_time'] == null)
						{
							$value[$s.'_time'] = $time;	
							$shifts[$key] = $value;
						}        				

						$f = true;
						break;            			
					}
				}
				if(!$f)
				{
					if($s == 'start')
					{
						$shifts[] = ["date"=>$date, "start_time"=>$time, "end_time"=>null];	
					}
					else
					{
						$shifts[] = ["date"=>$date, "start_time"=>null, "end_time"=>$time];
					}            		
				}

				$user_obj->shifts = json_encode($shifts);
				$user_obj->save();
				return true;
			}
		}
		return false;
	}
}