<?php  


/**
* 
*/
class BaseModel
{
	// Thực hiện thêm mới/cập nhật thông tin của đối tượng
	public function save(){
		$connect = BaseModel::getConnect();

		if(!isset($this->id) || $this->id == null || $this->id <= 0){
			// Xây dựng câu query insert into tablename (
			$this->queryBuilder = "insert into " . $this::$table;
			$this->queryBuilder .= " (";

			// Câu query trở thành insert into tablename ( cột1, cột2 , ...)
			for ($i=0; $i < count($this::$attributes); $i++) { 
				$this->queryBuilder .= " " . $this::$attributes[$i] . ",";
			}
			$this->queryBuilder = rtrim($this->queryBuilder, ",");
			$this->queryBuilder .= " ) values ( ";

			// Câu query trở thành insert into tablename ( cột1, cột2 , ...) values (:cột1, :cột2, :cộtn)
			for ($i=0; $i < count($this::$attributes); $i++) { 
				$this->queryBuilder .= " :" . $this::$attributes[$i] ." ,";
			}
			$this->queryBuilder = rtrim($this->queryBuilder, ",");
			$this->queryBuilder .= ")";
			
			$stmt = $connect->prepare($this->queryBuilder);

			// Bind dữ liệu vào trong câu query đã được hình thành ở trên
			for ($i=0; $i < count($this::$attributes); $i++) { 
				$data = isset($this->{$this::$attributes[$i]}) == false ? "": $this->{$this::$attributes[$i]};
				$stmt->bindValue(":" . $this::$attributes[$i] , $data);
			}
			// dd($this->queryBuilder);
			// Thực hiện thực thi câu lệnh với cơ sở dữ liệu
			$result = $stmt->execute();

			return $result;
			
		}else{
			$this->queryBuilder = "update " . $this::$table . " set ";
			for ($i=0; $i < count($this::$attributes); $i++) { 
				$data = isset($this->{$this::$attributes[$i]}) == false ? null : $this->{$this::$attributes[$i]};
				if($data == null){
					continue;
				}
				$this->queryBuilder .= $this::$attributes[$i]  . " = :"
				 . $this::$attributes[$i] . ",";
			}
			$this->queryBuilder = rtrim($this->queryBuilder, ",");
			$this->queryBuilder .= " where id = :id";
			$stmt = $connect->prepare($this->queryBuilder);
			// Bind dữ liệu vào trong câu query đã được hình thành ở trên
			for ($i=0; $i < count($this::$attributes); $i++) { 
				$data = isset($this->{$this::$attributes[$i]}) == false ? null : $this->{$this::$attributes[$i]};
				if($data == null){
					continue;
				}
				$stmt->bindValue(":" . $this::$attributes[$i] , $data);
			}
			$stmt->bindValue(":id", $this->id);
			$rs = $stmt->execute();
			return $rs;
		}
	}

	// Hàm mục đích tạo ra model thuộc dạng static có chứa câu query builder: select * from tên bảng where tên cột =(hoặc phép so sánh ở vị trí thứ 2) 'giá trị cần tìm kiếm'
	// Trả về object
	public static function where(){
		if ( func_num_args() >= 2 ){
			$compare = func_num_args() > 2 ? func_get_args()[1] : "=";
			$column = func_get_args()[0];
			$value = func_num_args() > 2 ? func_get_args()[2] : func_get_args()[1];

			$model = new static();
			$model->paramArr = [];
			$model->queryBuilder = "select * from " 
									. static::$table 
									. " where $column $compare :value".count($model->paramArr);
			$model->paramArr[] = $value;
			return $model;
	    }
	    return false;
	}
	// Hàm nối tiếp sau hàm where để tạo ra câu lệnh select * from table where điều kiện 1 and điều kiện 2
	// Trả về object/false;
	public function andWhere(){
		if ( func_num_args() >= 2 ){

			$this->queryBuilder .= " and ";
			$compare = func_num_args() > 2 ? func_get_args()[1] : "=";
			$column = func_get_args()[0];
			$value = func_num_args() > 2 ? func_get_args()[2] : func_get_args()[1];

			$this->queryBuilder .= " $column $compare :value".count($this->paramArr);
			$this->paramArr[] = $value;
			
			return $this;
	    }
	    return false;
	}

	// Hàm nối tiếp sau hàm where để tạo ra câu lệnh select * from table where điều kiện 1 or điều kiện 2
	// Trả về object/false;
	public function orWhere(){
		if ( func_num_args() >= 2 ){

			$this->queryBuilder .= " or ";
			$compare = func_num_args() > 2 ? func_get_args()[1] : "=";
			$column = func_get_args()[0];
			$value = func_num_args() > 2 ? func_get_args()[2] : func_get_args()[1];

			$this->queryBuilder .= " $column $compare :value".count($this->paramArr);
			$this->paramArr[] = $value;
			
			return $this;
	    }
	    return false;
	}

	// Hàm dựa vào câu query builder của các hàm where để lấy ra toàn bộ thông tin của cần tìm trong các câu lệnh where
	// Trả về list object/null
	public function get(){
		
		$conn = BaseModel::getConnect();
		$stmt = $conn->prepare($this->queryBuilder);
		for ($i=0; $i < count($this->paramArr); $i++) { 
			$stmt->bindValue(':value'.$i, $this->paramArr[$i]);
		}
		$stmt->execute();
		$rs = $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
		return $rs;
	}
	// Thêm order by cho câu lệnh query builder
	public function orderBy($column, $sortOrder = 'asc'){

		if($this->orderByArr == null){
			$this->orderByArr = [];
		}
		$this->orderByArr[$column] = $sortOrder;

		return $this;
	}

	// Hàm dựa vào câu query builder của các hàm where để lấy ra phần tử đầu tiên cần tìm trong các câu lệnh where
	// Trả về object/null
	public function first(){
		$conn = BaseModel::getConnect();
		if(isset($this->orderByArr) == true && $this->orderByArr != null && count($this->orderByArr) > 0){
			$this->queryBuilder .= " order by ";
			foreach ($this->orderByArr as $key => $value) {
				$this->queryBuilder .= " $key $value, ";
			}
			$this->queryBuilder = rtrim($this->queryBuilder,", ");
		}
		// var_dump($this);die;
		$stmt = $conn->prepare($this->queryBuilder);
		for ($i=0; $i < count($this->paramArr); $i++) { 
			$stmt->bindValue(':value'.$i, $this->paramArr[$i]);
		}
		$stmt->execute();
		$rs = $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
		
		if(count($rs) > 0){
			return $rs[0];
		}
		return null;
	}


	public function delete(){
		$query = "delete from ". $this::$table . " where id = :id";
		$conn = BaseModel::getConnect();
		
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':id', $this->id);
		try{
			$stmt->execute();
			return true;

		}catch(Exception $ex){
			return false;
		}
	}
	// Hàm tìm ra đối tượng có id = tham số truyền vào (trả về object)
	public static function find($id){
		$query = "select * from " . static::$table . " where id = :id";
		$conn = BaseModel::getConnect();
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		$rs = $stmt->fetchAll(PDO::FETCH_CLASS, static::class);

		if(count($rs) > 0){
			return $rs[0];
		}
		return null;
	}

	// Hàm tìm ra tất cả các bản ghi trong cơ sở dữ liệu của 1 bảng có model đại diện
	// Trả về list object
	public static function all(){
		
		$query = "select * from " . static::$table;
		$conn = BaseModel::getConnect();
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$rs = $stmt->fetchAll(PDO::FETCH_CLASS, static::class);

		return $rs;
	}
	// Hàm tạo kết nối cơ sở dữ liệu
	// Trả về PDO object
	public static function getConnect(){
		$servername = 'localhost';
		$dbname = 'shopping_cart';
		$dbUsername = 'root';
		$dbPassword = '';
		$conn = new PDO("mysql:servername=$servername;dbname=$dbname;charset=utf8;", $dbUsername, $dbPassword);
		return $conn;
	}
	
	
}