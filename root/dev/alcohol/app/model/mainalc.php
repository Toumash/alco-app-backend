<?php
	if (!defined('R')) {
		die('This script cannot be run directly');
	}
	require_once R . '/model/model.php';

	class MainalcModel extends Model
	{
		public function __construct()
		{
			parent::__construct();
			$this->log = Logger::getLogger(__CLASS__);
		}

		/**
		 * @param $alc_id int
		 * @param $limit  int  how much to fetch
		 *
		 * @return array|null
		 */
		public function fetchRatings($alc_id, $limit)
		{

			$query_string = "SELECT r.rate,r.time,r.content,u.LOGIN FROM alcohol_ratings AS r,users  AS u WHERE r.userID= u.ID AND r.alcoholID={$alc_id} ORDER BY r.time DESC LIMIT {$limit}";
			$query        = $this->pdo->query($query_string);
			$result       = array();
			while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				$alc      = array();
				$alc['d'] = $row['time'];
				$alc['c'] = $row['content'];
				$alc['a'] = $row['LOGIN'];
				$alc['r'] = $row['rate'];

				$result[] = $alc;
			}
			if (!empty($result) > 0) {
				return $result;
			} else {
				return null;
			}
		}

		/**
		 * @param $alcohols Alcohol[]
		 * @param $user     User
		 *
		 * @return bool
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

				return true;
			} catch (Exception $e) {
				if (isset($this->pdo)) {
					$this->pdo->rollback();
					$this->log->error('insertSerial', $e);
				}

				return false;
			}

		}

		/**
		 * @return Alcohol[] with ID
		 */
		public function fetchAll()
		{
			try {
				$query = $this->pdo->query("SELECT * FROM main_alcohols LIMIT 10000");
				$array = array();
				while ($row = $query->fetch()) {
					$alc = new Alcohol($row['NAME'], $row['PRICE'], $row['TYPE'], $row['SUBTYPE'], $row['VOLUME'], $row['PERCENT'], $row['DEPOSIT']);
					$alc->setId($row['ID']);
					$array[] = $alc->toAPIArray();
				}

				return $array;
			} catch (PDOException $e) {
				$this->log->error('fetchAll', $e);

				return array();
			}
		}

		/**
		 * @param      $id
		 * @param      $content
		 * @param User $user
		 *
		 * @return bool|null false - MySQL error<br>
		 *                   null - no alcohol with given id
		 *                   true - ok
		 */
		public function flag($id, $content, User $user)
		{
			try {
				$sql    = $this->pdo->query(
					"SELECT EXISTS(SELECT 1 FROM main_alcohols where ID=$id LIMIT 1) as exist LIMIT 1"
				);
				$result = $sql->fetch(PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				$this->log->error('flagger', $e);

				return false;
			}

			if ($result['exist'] == 1) {
				$time                = date("Y.m.d H:i:s");
				$query_string_insert = "INSERT INTO alcohol_flags(alcoholID,userID,content,time) VALUES ($id,{$user->id},'{$content}','{$time}')";

				try {
					return $this->pdo->query($query_string_insert) ? true : false;
				} catch (PDOException $e) {
					$this->log->error('flag', $e);

					return false;
				}
			} else {
				return null;
			}
		}

		/**
		 * @return Alcohol[] with ID
		 */
		public function fetchAllWithTypes()
		{
			try {
				$query = $this->pdo->query(
					"SELECT u.ID,u.NAME,u.PRICE,u.VOLUME,u.PERCENT,u.DEPOSIT,t.name as type,s.name AS subtype FROM main_alcohols as u,alcohol_types as t,alcohol_subtypes as s WHERE u.TYPE= t.id  AND u.SUBTYPE = s.id  AND u.TYPE = s.typeID  ORDER BY u.NAME ASC LIMIT 10000"
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

			} catch (Exception $e) {
				$this->log->error('fetching main alcohols', $e);

				return array();
			}
		}

		/**
		 * @return int if error returns 0
		 */
		public function getCount()
		{
			try {
				$query  = $this->pdo->query("SELECT COUNT(*) as count FROM main_alcohols");
				$result = $query->fetch(PDO::FETCH_ASSOC);
				$count  = $result['count'];

				return $count;
			} catch (Exception $e) {
				$this->log->error('getCount', $e);

				return -1;
			}
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
					$this->log->error('delete not working', $e);
				}

				return false;
			}
		}

		/**
		 * @param int $id id of the alcohol in the MainAlcDB
		 *
		 * @return bool
		 */
		public function exists($id)
		{

			$sql = $this->pdo->prepare(
				"SELECT EXISTS(SELECT 1 FROM main_alcohols where ID=:id) as exist LIMIT 1"
			);
			$sql->bindValue(':id', $id, PDO::PARAM_INT);
			$sql->execute();
			$result = $sql->fetch(PDO::FETCH_ASSOC);

			return $result['exist'] == 1;
		}

		/**
		 * @param int    $id
		 * @param string $content
		 * @param float  $rate
		 * @param User   $user
		 *
		 * @return bool
		 */
		public function rate($id, $content, $rate, User $user)
		{
			$time = date("Y.m.d H:i:s");
			try {
				$sql = $this->pdo->prepare(
					"SELECT EXISTS(SELECT 1 FROM alcohol_ratings where alcoholID=:id and userID=:profile_id LIMIT 1) as exist"
				);
				$sql->bindValue(':id', $id, PDO::PARAM_INT);
				$sql->bindValue(':profile_id', $id, PDO::PARAM_INT);
				$sql->execute();
				$res = $sql->fetch(PDO::FETCH_ASSOC);


				if ($res['exist'] == 1) {
					$sql = $this->pdo->prepare(
						"UPDATE alcohol_ratings SET content=:content,time=:time,rate=:rate where alcoholID =:id and userID=:user_id LIMIT 1"
					);
					$sql->bindValue(':content', $content, PDO::PARAM_STR);
					$sql->bindValue(':rate', $rate, PDO::PARAM_STR);
					$sql->bindValue(':id', $id, PDO::PARAM_INT);
					$sql->bindValue(':user_id', $user->id, PDO::PARAM_INT);
					$sql->execute();
				} else {
					$sql = $this->pdo->prepare(
						"INSERT INTO alcohol_ratings(alcoholID,userID,content,time,rate) VALUES (:id,:user_id,:content,:time,:rate)"
					);
					$sql->bindValue(':id', $id, PDO::PARAM_INT);
					$sql->bindValue(':user_id', $user->id, PDO::PARAM_INT);
					$sql->bindValue(':content', $content, PDO::PARAM_STR);
					$sql->bindValue(':time', $time, PDO::PARAM_STR);
					$sql->bindValue(':rate', $rate, PDO::PARAM_STR);
					$sql->execute();
				}

				return true;
			} catch (PDOException $e) {
				$this->log->error('rating mysql error', $e);

				return false;

			}
		}
	}