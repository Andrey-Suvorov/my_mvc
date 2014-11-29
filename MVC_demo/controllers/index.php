<?php
// контролер
Class Controller_Index Extends Controller_Base {
	
	// шаблон
	public $layouts = "first_layouts";
	// экшен
	public function index() {
		$dbObject = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
		$sql = "SELECT file.id AS file_id,file.name AS file_name, extension.id AS ext_id, extension.name AS ext_name
			FROM `file`
			LEFT JOIN `extension` ON file.extension_id = extension.id";
		$stmt = $dbObject->prepare($sql);
		$stmt->execute();
		$rows = array();
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$rows[] = $row;
		}
			
		$this->template->vars('rows', $rows);
		$this->template->view('index');
		
	}
	
		
	public function handler() {		
		$destinationPath = getcwd() . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
		
		if (!is_dir($destinationPath)) {
			exit('нет директории');
		}
		
		$description = $_POST['description'];
		$description = trim($description);
		$description = htmlspecialchars($description);
		$description = mysql_escape_string($description);
		
		$filePath = $_FILES['filename']['tmp_name'];
		$outName = $_FILES['filename']['name'];
		$fileName = reset(explode(".", $outName));
		$fileType = end(explode(".", $outName));
				
		$targetPath = $destinationPath . basename($outName);
		@move_uploaded_file($filePath, $targetPath);
		
		//коннект к базе данных
		$dbObject = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
		
		
		
		$sql = "SELECT * FROM `extension` WHERE `name` = '$fileType' ";
		$stmt = $dbObject->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if (!$row) {
			$sql = "INSERT INTO `extension` (`name`) VALUES (:fileType)";
			$q = $dbObject->prepare($sql);
			$q->execute(array(':fileType' => $fileType));
			$extensionId = $dbObject->lastInsertId();
		} else {
			$extensionId = $row['id'];			
		}
		
		$sql = "INSERT INTO `file`(`name` , `description` , `extension_id`) VALUES (:fileName, :description, :extensionId)";
		$q = $dbObject->prepare($sql);
		$q->execute(array(
			':fileName' => $fileName,
			':description' => $description,
			':extensionId' => $extensionId
		));
		
		$sql = "SELECT file.id AS file_id,file.name AS file_name, extension.id AS ext_id, extension.name AS ext_name
			FROM `file`
			LEFT JOIN `extension` ON file.extension_id = extension.id";
		$stmt = $dbObject->prepare($sql);
		$stmt->execute();
		$rows = array();
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$rows[] = $row;
		}
			
		$this->template->vars('rows', $rows);
		$this->template->view('index');			
	}
	
	public function edit() {
		$dbObject = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
		if (isset($_POST['delete'])) {
			$id = $_POST['file_id'];
			$sql = "UPDATE `file` SET `extension_id` = NULL WHERE `id` = :id";
			$stmt = $dbObject->prepare($sql);
			$stmt->bindparam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
						
			header("Location:index.php");
			
		} elseif (isset($_POST['edit'])) {
			if (!empty($_POST['new_extension'])) {
				
				
				$sql = "SELECT * FROM `extension` WHERE `name` = :new_extension";
				$stmt = $dbObject->prepare($sql);
				$stmt->bindparam(':new_extension', $_POST['new_extension'], PDO::PARAM_STR);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				
				if (!$row) {
					$sql = "INSERT INTO `extension` (`name`) VALUES (:new_extension)";
					$stmt = $dbObject->prepare($sql);
					$stmt->bindparam(':new_extension', $_POST['new_extension'], PDO::PARAM_STR);
					$stmt->execute();
					$extensionId = $dbObject->lastInsertId();
				} else {
					$extensionId = $row['id'];			
				}
								
				$id = $_POST['file_id'];
				$sql = "UPDATE `file` SET `extension_id` = :new_extension_id WHERE `id` = :id";				
				$stmt = $dbObject->prepare($sql);
				$stmt->bindparam(':new_extension_id', $extensionId, PDO::PARAM_INT);
				$stmt->bindparam(':id', $id, PDO::PARAM_INT);
				$stmt->execute();
				
				header("Location:index.php");	
			}
		} else {
			echo '<pre>'; print_r('NO!!!'); echo '</pre>'; exit;
		}
		
		
		
		//return false
	}

}