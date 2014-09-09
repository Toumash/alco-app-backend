<?php

	require_once R . '/model/model.php';
	require_once R . '/model/contracts/Alcohol.php';

	class MainalcModel extends Model
	{
		/**
		 * @param $alcohols Alcohol[]
		 * @param $user     User
		 *
*@return string R_OK|R_ERROR
		 */
		public function insertSerial($alcohols, User $user)
		{
			$this->loadModel('log');
			##TRANSACTION
			try {
				$this->pdo->beginTransaction();

				foreach ($alcohols as $row) {
					$sql = $this->pdo->prepare(
						"INSERT INTO main_alcohols(NAME,PRICE,TYPE,SUBTYPE,VOLUME,PERCENT,DEPOSIT,userID) VALUES (:name,:price,:type,:subtype,:volume,:percent,:deposit,:userID)"
					);
					$sql->bindValue(':name', $row->name, PDO::PARAM_STR);
					$sql->bindValue(':price', strval($row->price), PDO::PARAM_STR);
					$sql->bindValue(':type', $row->type, PDO::PARAM_INT);
					$sql->bindValue(':subtype', $row->subtype, PDO::PARAM_INT);
					$sql->bindValue(':volume', $row->volume, PDO::PARAM_INT);
					$sql->bindValue(':percent', strval($row->percent), PDO::PARAM_STR);
					$sql->bindValue(':deposit', $row->deposit, PDO::PARAM_INT);
					$sql->bindValue(':userID', $user->id, PDO::PARAM_INT);
					$sql->execute();
				}
				$this->pdo->commit();

				return R_OK;
			} catch (Exception $e) {
				if (isset($this->pdo)) {
					$this->pdo->rollback();
					LogModel::d($e->getMessage(), LogModel::$API_LOG);
				}

				return R_ERROR;
			}

		}

		/**
		 * @return Alcohol[] with ID
		 */
		public function fetchAll()
		{
			$query = $this->pdo->query("SELECT * FROM main_alcohols LIMIT 10000");
			$array = array();
			while ($row = $query->fetch()) {
				$alc = new Alcohol($row['NAME'], $row['PRICE'], $row['TYPE'], $row['SUBTYPE'], $row['VOLUME'], $row['PERCENT'], $row['DEPOSIT']);
				$alc->setId($row['ID']);
				$array[] = $alc->toAPIArray();
			}

			return $array;
		}

		public function flag($id, $content, User $user)
		{

			$sql = $this->pdo->query(
				"SELECT EXISTS(SELECT 1 FROM main_alcohols where ID=$id LIMIT 1) as exist LIMIT 1"
			);
			print_r(mysqli_error($this->db));
			$result = $query_exist->fetch_assoc();
			if ($result['exist'] == 1) {

				// if (mysqli_num_rows($query) == 1) {
				//$json_string = $query->fetch_assoc();
				//print_r($json_string);
				/*                        $db_json = json_decode($json_string['FLAGS'], true);
										$data = $db_json;
										$data[$login] = $input['info'];*/
				$time                = date("Y.m.d H:i:s");
				$query_string_insert = "INSERT INTO alcohol_flags(alcoholID,userID,content,time) VALUES ($id,{$this->profileID},'{$content}','{$time}')";
				$insert_result       = $this->db->query($query_string_insert);

			}


			/**
		 * @return Alcohol[] with ID
		 */
		public function fetchAllWithTypes()
		{
			$query = $this->pdo->query(
				"SELECT u.ID,u.NAME,u.PRICE,u.VOLUME,u.PERCENT,u.DEPOSIT,t.name as type,s.name as subtype FROM main_alcohols as u,alcohol_types as t,alcohol_subtypes as s WHERE u.TYPE= t.id  AND u.SUBTYPE = s.id  AND u.TYPE = s.typeID  ORDER BY u.NAME ASC LIMIT 10000"
			);
			$array = array();
			while ($row = $query->fetch()) {
				$alc = new Alcohol($row['NAME'], $row['PRICE'], $row['type'], $row['subtype'], $row['VOLUME'], $row['PERCENT'], $row['DEPOSIT']);
				$alc->setId($row['ID']);
				$alc->setTypeString($row['type']);
				$alc->setSubtypeString($row['subtype']);
				$array[] = $alc;
			}

			return $array;
//return $this->select('user_alcohols', '*', null, null, 2000);
		}

		public function getCount()
		{
			$query  = $this->pdo->query("SELECT COUNT(*) as count FROM main_alcohols");
			$result = $query->fetch(PDO::FETCH_ASSOC);
			$count  = $result['count'];

			return $count;
		}

		/**
		 * @param $array array
		 *
		 * @return Alcohol[]
		 */
		public function JSONToAlcohols($array)
		{
			$alcohols = array();
			foreach ($array as $row) {
				$alcohols[] = new Alcohol($row['NAME'], $row['PRICE'], $row['TYPE'], $row['SUBTYPE'], $row['VOLUME'], $row['PERCENT'], $row['DEPOSIT']);
			}

			return $alcohols;
		}

		/**
		 * @param $id int id from db to delete
		 *
		 * @return bool
		 */
		public function delete($id)
		{
			try {
				$this->pdo->beginTransaction();
				$query = $this->pdo->prepare("DELETE FROM main_alcohols WHERE ID=:id LIMIT 1");
				$query->bindValue(':id', $id, PDO::PARAM_INT);
				$query->execute();
				$this->pdo->commit();

				return true;
			} catch (Exception $e) {

				if (isset($this->pdo)) {
					$this->pdo->rollback();
					LogModel::d($e->getMessage(), LogModel::$API_LOG);
				}

				return false;
			}
		}
	}