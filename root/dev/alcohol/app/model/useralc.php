<?php
	require_once R . '/model/model.php';
	require_once R.'/model/contracts/Alcohol.php';

	class UseralcModel extends Model
	{
		/**
		 * @param $alcohols Alcohol[]
		 * @see Alcohol
		 * @param $userID
		 *
		 * @return string R_OK|R_ERROR
		 */
		public function insertSerial($alcohols, $userID)
		{
			try {
				$this->pdo->beginTransaction();

				foreach ($alcohols as $row) {
					$sql = $this->pdo->prepare(
						"INSERT INTO user_alcohols(NAME,PRICE,TYPE,SUBTYPE,VOLUME,PERCENT,DEPOSIT,userID) VALUES (:name,:price,:type,:subtype,:volume,:percent,:deposit,:userID)"
					);
					$sql->bindValue(':name', $row->name, PDO::PARAM_STR);
					$sql->bindValue(':price', strval($row->price), PDO::PARAM_STR);
					$sql->bindValue(':type', $row->type, PDO::PARAM_INT);
					$sql->bindValue(':subtype', $row->subtype, PDO::PARAM_INT);
					$sql->bindValue(':volume', $row->volume, PDO::PARAM_INT);
					$sql->bindValue(':percent', strval($row->percent), PDO::PARAM_STR);
					$sql->bindValue(':deposit', $row->deposit, PDO::PARAM_INT);
					$sql->bindValue(':userID', $userID, PDO::PARAM_INT);
					$sql->execute();
				}
				$this->pdo->commit();

				return R_OK;
			} catch (Exception $e) {
				if (isset($this->pdo)) {
					$this->pdo->rollback();

					$this->loadModel('log');
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
			$query    = $this->pdo->query("SELECT * FROM user_alcohols LIMIT 2000");
			$array = array();
			while ($row = $query->fetch()) {
				$alc = new Alcohol($row['NAME'], $row['PRICE'], $row['TYPE'], $row['SUBTYPE'], $row['VOLUME'], $row['PERCENT'], $row['DEPOSIT']);
				$alc->setId($row['ID']);
				$array[] = $alc;
			}

			return $array;
//return $this->select('user_alcohols', '*', null, null, 2000);
		}

		/**
		 * @return Alcohol[] with ID
		 */
		public function fetchAllWithTypes()
		{
			$query    = $this->pdo->query("SELECT u.ID,u.NAME,u.PRICE,u.VOLUME,u.PERCENT,u.DEPOSIT,t.name as type,s.name as subtype FROM user_alcohols as u,alcohol_types as t,alcohol_subtypes as s WHERE u.TYPE= t.id  AND u.SUBTYPE = s.id  AND u.TYPE = s.typeID  ORDER BY u.NAME ASC LIMIT 2000");
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
			$query  = $this->pdo->query("SELECT COUNT(*) as count FROM user_alcohols");
			$result = $query->fetch(PDO::FETCH_ASSOC);
			$count  = $result['count'];

			return $count;
		}
		/**
		 * @param $array array
		 * @return Alcohol[]
		 */
		public function JSONToAlcohols($array)
		{
			$alcohols = array();
			foreach ($array as $row) {
				$alcohols[] = new Alcohol($row['NAME'],$row['PRICE'],$row['TYPE'],$row['SUBTYPE'],$row['VOLUME'],$row['PERCENT'],$row['DEPOSIT']);
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
				$query = $this->pdo->prepare("DELETE FROM user_alcohols WHERE ID=:id LIMIT 1");
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