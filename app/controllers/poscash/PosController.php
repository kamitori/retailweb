<?php
namespace RW\Controllers\Poscash;
use RW\Models\JTProduct;
use RW\Models\JTCategory;
use RW\Models\JTContact;

class PosController extends ControllerBase
{
	public function indexAction()    {
	   
	}
	public function reportsAction(){

	}
	public function settingAction(){
		$param = $this->dispatcher->getParam('paramsList');
		$param = explode('/',$param);
		$this->view->module = isset($param[1])?$param[1]:'';
		if($param[1] != ''){
			switch ($param[1]) {
				case 'product':
					$this->view->product_list = JTProduct::find(array(array()));
					$this->view->category_list = JTCategory::find(array(array()));
					break;
				case 'category':
					$this->view->category_list = JTCategory::find(array(array()));
					break;
				case 'user':
					$this->view->user_list = JTContact::find(array(array()));
					break;
				default:
					# code...
					break;
			}
		}
	}

	public function updateCategoryAction(){
		$this->view->disable();
		if($this->request->isAjax()){
			$id = $this->request->hasPost("id")?$this->request->getPost("id"):'';
			$name = $this->request->hasPost("name")?$this->request->getPost("name"):'';
			if($name!=''){
				$short_name = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
			}else{
				$short_name='';
			}
			$parent_category = $this->request->hasPost("parent_category")?$this->request->getPost("parent_category"):'';
			$order = $this->request->hasPost("order")?$this->request->getPost("order"):'';
			$description = $this->request->hasPost("description")?$this->request->getPost("description"):'';
			if ($this->request->hasFiles() == true) {
				foreach ($this->request->getUploadedFiles() as $file){
					if($file->getKey()=='image'){
						$image = 'images/product-categories/'.md5(time()).'_'.$file->getName();
						if(!$file->moveTo($image)){
							$image='';
						}
					}
				}
			}else{
				$image='';
			}
			
			if($id!=''){
				// pr($id);die;
				$category = JTCategory::findFirst(
											array(array(
													'_id' => new \MongoId($id)
												)
											)
										);
			}else{
				$category = new JTCategory();
			}
			if($category){
				$category->name = $name;
				$category->short_name = $short_name;
				$category->parent_id = $parent_category!=''?new \MongoId($parent_category):'';
				$category->order_no = $order;
				$category->description = $description;
				if($image!='')
					$category->image = $image;
				
				$arr_return = array('status'=>'error','message'=>'');
				if($category->save()){
					$arr_return['status'] = 'success';
				}else{
					foreach ($category->getMessages() as $message) {
						$arr_return['message'] .= $message."<br/>";
					}
				}
				echo json_encode($arr_return);
			}
		}
	}

	public function deleteCategoryAction(){
		$this->view->disable();
		if($this->request->isAjax()){
			$id = $this->request->hasPost("id")?$this->request->getPost("id"):'';
			$arr_return = array('status'=>'error','message'=>'');
			if($id!=''){
				$category = JTCategory::findFirst(
											array(array(
													'_id' => new \MongoId($id)
												)
											)
										);
				if($category){
					if($category->delete()){
						$arr_return['status'] = 'success';
					}else{
						foreach ($category->getMessages() as $message) {
							$arr_return['message'] .= $message."<br/>";
						}
					}
				}
				echo json_encode($arr_return);
			}
		}
	}

	public function searchCategoryAction(){
		$this->view->disable();
		if($this->request->isAjax()){
			$key = $this->request->hasPost("key")?$this->request->getPost("key"):'';
			if($key!=''){
				$category_list = JTCategory::find(array(
													array(
														'$or'=>array(
															array('name'=>array('$regex'=>$key)),
															array('description'=>array('$regex'=>$key))
														)
													)
												)
											);
			}else{
				$category_list = JTCategory::find(array(
												
											));
			}
			
			$arr_return = array();
			foreach ($category_list as $key => $value) {
				$arr_return[] = $value->toArray();
			}
			echo json_encode($arr_return);
		}
	}

	public function updateProductAction(){
		$this->view->disable();
		if($this->request->isAjax()){
			$id = $this->request->hasPost("id")?$this->request->getPost("id"):'';
			$name = $this->request->hasPost("name")?$this->request->getPost("name"):'';
			$category = $this->request->hasPost("category")?$this->request->getPost("category"):'';
			$sku = $this->request->hasPost("sku")?$this->request->getPost("sku"):'';
			$price = $this->request->hasPost("price")?$this->request->getPost("price"):'';
			if ($this->request->hasFiles() == true) {
				foreach ($this->request->getUploadedFiles() as $file){
					if($file->getKey()=='image'){
						$image = 'images/product-categories/'.md5(time()).'_'.$file->getName();
						if(!$file->moveTo($image)){
							$image='';
						}
					}
				}
			}else{
				$image='';
			}
			
			if($id!=''){
				// pr($id);die;
				$product = JTProduct::findFirst(
											array(array(
													'_id' => new \MongoId($id)
												)
											)
										);
			}else{
				$product = new JTProduct();
			}
			if($product){
				$product->name = $name;
				$product->category = $category!=''?new \MongoId($category):'';
				$product->sku = $sku;
				$product->price = $price;
				if($image!='')
					$product->image = $image;
				
				$arr_return = array('status'=>'error','message'=>'');
				if($product->save()){
					$arr_return['status'] = 'success';
				}else{
					foreach ($product->getMessages() as $message) {
						$arr_return['message'] .= $message."<br/>";
					}
				}
				echo json_encode($arr_return);
			}
		}
	}

	public function deleteProductAction(){
		$this->view->disable();
		if($this->request->isAjax()){
			$id = $this->request->hasPost("id")?$this->request->getPost("id"):'';
			$arr_return = array('status'=>'error','message'=>'');
			if($id!=''){
				$product = JTProduct::findFirst(
											array(array(
													'_id' => new \MongoId($id)
												)
											)
										);
				if($product){
					if($product->delete()){
						$arr_return['status'] = 'success';
					}else{
						foreach ($product->getMessages() as $message) {
							$arr_return['message'] .= $message."<br/>";
						}
					}
				}
				echo json_encode($arr_return);
			}
		}
	}

	public function searchProductAction(){
		$this->view->disable();
		if($this->request->isAjax()){
			$key = $this->request->hasPost("key")?$this->request->getPost("key"):'';
			$category = $this->request->hasPost("category")?$this->request->getPost("category"):'';
			if($key!=''){
				if($category !=''){
					$category_list = JTProduct::find(array(
												array(
														'$or'=>array(
															array('name'=>array('$regex'=>$key)),
															array('description'=>array('$regex'=>$key))
														),
														'$and'=>array(
															array('category'=> new \MongoId($category))
														)
													)
											));
				}else{
					$category_list = JTProduct::find(array(
												array(
														'$or'=>array(
															array('name'=>array('$regex'=>$key)),
															array('description'=>array('$regex'=>$key))
														)
													)
											));
				}
			}else{
				if($category !=''){
					$category_list = JTProduct::find(array(
														array('category'=> new \MongoId($category))
												));
				}else{
					$category_list = JTProduct::find(array(
												array()
											));
				}
			}
			
			$arr_return = array();
			foreach ($category_list as $key => $value) {
				$arr_return[] = $value->toArray();
			}
			echo json_encode($arr_return);
		}
	}

}
