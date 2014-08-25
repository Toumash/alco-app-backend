<?php

	class AlcoholDisplay
	{

		static public $ALC = array(
			"id"      => "id",
			"name"    => "Nazwa",
			"price"   => "Cena",
			"volume"  => "Objętość",
			"type"    => "Typ",
			"subtype" => "Podtyp",
			"deposit" => "Kaucja",
			"percent" => "Procent",
			"accept"  => "Zatwierdź",
			"delete"  => "Usuń",
		);
		/**
		 * @var mysqli
		 */
		var $db;
		/**
		 * @var string
		 */
		var $permission;

		public function __construct($db, $permission)
		{
			$this->db         = $db;
			$this->permission = $permission;
		}

		public function index($action, $id)
		{
			$view = 'db';
			if ($_SESSION['PERMISSION'] < 2) {
				showPermissionAlert();

				return false;
			}

			$this->handleRequest($action, $id);
			if (isset($_GET['db'])) {
				switch ($_GET['db']) {
					case 'main':
					{
						$this->showMainAlcohols();
						break;
					}
					case 'user':
					{
						$this->showUserAlcohols();
						break;
					}
				}
			} else {
				$q_user     = $this->db->query("SELECT COUNT(*) FROM user_alcohols");
				$q_main     = $this->db->query("SELECT COUNT(*) FROM main_alcohols");
				$count_main = $q_main->fetch_row();
				$count_user = $q_user->fetch_row();
				echo '<div style="text-align: center;"><h3>Wybięrz Bazę danych:</h3></div><br/>';
				echo '<center><table><tr><td style="padding-right:2em;"><a href="?v=' . $view . '&db=main"><img src="images/database-main.png"></a></td><td><a href="?v=' . $view . '&db=user"><img src="images/database-users.png"></a></td></tr>';
				echo '<tr><td style="padding-right:2em; text-align: center;">Główna</td><td style="text-align: center;">Użyszkodników</td></tr>';
				echo '<tr><td style="padding-right:2em;"><center>' . $count_main[0] . '</center></td><td><center>' . $count_user[0] . '</center></td></tr></table></center>';
			}
		}

		public function handleRequest($action, $id)
		{

			if (isset($action)) {
				if ($id) {
					$id = $this->db->real_escape_string($id);
					switch ($action) {
						case 'a':
						{
							$SQL_move = "INSERT INTO main_alcohols(NAME,PRICE,TYPE,SUBTYPE,VOLUME,PERCENT,DEPOSIT) SELECT NAME,PRICE,TYPE,SUBTYPE,VOLUME,PERCENT,DEPOSIT FROM user_alcohols WHERE ID=" . $id;
							$successMove = $this->db->query($SQL_move) or die(mysqli_error($this->db));
							if ($successMove) {
								$this->db->query("DELETE FROM user_alcohols WHERE ID=" . $id . ";");
								echo 'Przeniesiono #' . $id . ' pomyślnie do głównej bazy danych';
								echo '<br/>';
								Log::d('DB:user_alcohols::' . $_SESSION['user'] . ': MOVED to main_alcohols id:' . $id);
							}
							break;
						}
						case 'd':
						{
							if (isset($_GET['db'])) {
								switch ($_GET['db']) {

									case 'main':
									{
										$SQL_delete = "DELETE FROM main_alcohols WHERE ID=" . $id . " LIMIT 1;";
										$successDelete = $this->db->query($SQL_delete) or die('error' . mysqli_error(
												$this->db
											));

										if ($successDelete) {
											echo 'Usunięto #' . $id;
											echo '<br/>';
											Log::d('DB:main_alcohols::' . $_SESSION['user'] . ': DELETED id:' . $id);
										}
										break;
									}

									case 'user':
									{
										$SQL_delete    = "DELETE FROM user_alcohols WHERE ID=" . $id . " LIMIT 1;";
										$successDelete = $this->db->query($SQL_delete);

										if ($successDelete) {
											echo 'Usunięto #' . $_GET['id'];
											echo '<br/>';
											Log::d('DB:user_alcohols::' . $_SESSION['user'] . ': DELETED id:' . $id);
										}
										break;
									}
								}
								//switch db
							}
						}
						//case d
					}
					//switch action
				}
				//isset id
			}
			//isset action
		}

		public function showMainAlcohols()
		{
			$this->handleAdding('main_alcohols');

			$query_string = "SELECT alc.ID,alc.NAME,alc.PRICE,alc.PERCENT,alc.VOLUME,t.name AS TYPE,s.name AS SUBTYPE,alc.DEPOSIT FROM main_alcohols AS alc,alcohol_types AS t,alcohol_subtypes AS s WHERE alc.TYPE= t.id  AND alc.SUBTYPE = s.id  AND alc.TYPE = s.typeID  ORDER BY alc.NAME ASC";
			$query        = $this->db->query($query_string);
			$RESULT       = array();

			while ($row = $query->fetch_assoc()) {

				$alc            = array();
				$alc['ID']      = $row['ID'];
				$alc['NAME']    = $row['NAME'];
				$alc['PRICE']   = $row['PRICE'];
				$alc['PERCENT'] = $row['PERCENT'];
				$alc['VOLUME']  = $row['VOLUME'];
				$alc['TYPE']    = $row['TYPE'];
				$alc['SUBTYPE'] = $row['SUBTYPE'];
				$alc['DEPOSIT'] = $row['DEPOSIT'];
				$RESULT[]       = $alc;
			}


			$Arow = self::$ALC;
			echo '<h3 class="alcohols">Główna Baza Danych</h3>';
			echo '<table class="alcohols-table">';
			echo '<thead>';
			echo '<tr><th>id</th><th>' . $Arow['name'] . '</th><th>' . $Arow['price'] . '</th><th>' . $Arow['percent'] . '</th><th>' . $Arow['volume'] . '</th><th>' . $Arow['type'] . '</th><th>' . $Arow['subtype'] . '</th><th>' . $Arow['deposit'] . '</th><th>' . $Arow['delete'] . '</th>';
			echo '</thead>';
			if ($RESULT != null) {
				foreach ($RESULT as $ROW) {
					echo '<tr><td data-th="' . $Arow['id'] . '">#' . $ROW['ID'] . '</td><td data-th="' . $Arow['name'] . '">' . $ROW['NAME'] . '</td><td data-th="' . $Arow['price'] . '">' . $ROW['PRICE'] . '</td><td data-th="'
						. $Arow['percent'] . '">' . $ROW['PERCENT'] . '</td><td data-th="' . $Arow['volume'] . '">' . $ROW['VOLUME'] . '</td><td data-th="' . $Arow['type'] . '">' . $ROW['TYPE'] . '</td><td data-th="'
						. $Arow['subtype'] . '">' . $ROW['SUBTYPE'] . '</td><td data-th="' . $Arow['deposit'] . '">' . $ROW['DEPOSIT'] . '</td>';
					echo '<td data-th="' . $Arow['delete'] . '">';
					if ($this->permission >= 5) {
						echo '<a class="delete" href="?v=' . $_GET['v'] . '&db=' . $_GET['db'] . '&action=d&id=' . $ROW['ID'] . '">[ X ]</a>';
					}
					echo '</td>';
					echo '</tr>';
				}
				if ($this->permission >= 4) {
					$this->showAddingRow();
				}
			} else {
				if ($this->permission >= 4) {
					$this->showAddingRow();
				} else {
					echo '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
				}
			}
			echo '</table>';
		}

		public function handleAdding($database_name = 'user_alcohols')
		{
			if (isset($_POST['action']) && $_POST['action'] == 'i' && isset($_POST['NAME'])) {
				$tname    = $this->db->real_escape_string(trim($_POST['NAME']));
				$ttype    = $this->db->real_escape_string(trim($_POST['TYPE']));
				$tsubtype = $this->db->real_escape_string(trim($_POST['SUBTYPE']));
				$tprice   = $this->db->real_escape_string(trim($_POST['PRICE']));
				$tvolume  = $this->db->real_escape_string(trim($_POST['VOLUME']));
				$tpercent = $this->db->real_escape_string(trim($_POST['PERCENT']));
//$tdeposit = $db->real_escape_string($_POST['DEPOSIT']);
				$tdeposit = $this->db->real_escape_string(isset($_POST['DEPOSIT']) ? 1 : 0);
				if ($this->validateUserInput(
					$tname,
					$ttype,
					$tsubtype,
					$tprice,
					$tvolume,
					$tpercent,
					$tdeposit
				)
				) {
					$addSQLcmd  = "INSERT INTO `$database_name`(NAME, TYPE, SUBTYPE, PRICE, VOLUME, PERCENT,DEPOSIT) VALUES ('$tname',$ttype,$tsubtype,$tprice,$tvolume,$tpercent,$tdeposit);";
					$successAdd = $this->db->query($addSQLcmd);
					if ($successAdd) {
						echo '<text style="color:green;">Sukces</text>';
						echo '<br/>';
						Log::d(
							'DB:' . $database_name . '::' . $_SESSION['user'] . ': ADDED id:' . $this->db->insert_id
						);
					} else {
						echo '<text style="color:red;">Wystąpił błąd przy dodawaniu. Proszę, sprawdź poprawność danych</text>';
						//echo mysqli_error($this->db);
						echo '<br/>';
					}
				} else {
					echo 'Nieprawidłowe dane';
				}
			}
		}

		public function validateUserInput(
			$_name,
			$_type,
			$_subtype,
			$_price,
			$_volume,
			$_percent,
			$_deposit
		) {
			//TODO: validate iT!!!
			return true;
		}

		public function showAddingRow()
		{
			$ROW = self::$ALC;
			echo '<tr><form method="POST" action="?v=' . $_GET['v'] . '&db=' . $_GET['db'] . '"><td data-th="' . $ROW['id'] . '">#auto</td><td data-th="' . $ROW['name'] . '"><input type="text" name ="NAME" class="add-name"/></td>'
				. '<td data-th="' . $ROW['price'] . '"><input type="text" name="PRICE" class="add-int"/></td>'
				. '<td data-th="' . $ROW['percent'] . '"><input type="text" name="PERCENT" class="add-int"/></td>'
				. '<td data-th="' . $ROW['volume'] . '"><input type="text" name="VOLUME" class="add-int"/></td>'
				. '<td data-th="' . $ROW['type'] . '"><input type="text" name="TYPE" class="add-type"/></td>'
				. '<td data-th="' . $ROW['subtype'] . '"><input type="text" name="SUBTYPE" class="add-int"/></td>'
				. '<td data-th="' . $ROW['deposit'] . '"><input type="checkbox" name="DEPOSIT" class="add-int"/></td>'
				. '<td data-th="' . $ROW['accept'] . '"><input type="submit" value="OK"/><input type="hidden" name="action" value="i"/></td></form></tr>';
		}

		public function showUserAlcohols()
		{
			$this->handleAdding('user_alcohols');
			$COL = self::$ALC;
# FETCHING ROWS
			$query        = "SELECT a.ID,a.NAME,a.PRICE,a.PERCENT,a.VOLUME,t.name AS TYPE,s.name AS SUBTYPE,a.DEPOSIT FROM user_alcohols AS a,alcohol_types AS t,alcohol_subtypes AS s WHERE a.TYPE= t.id  AND a.SUBTYPE = s.id  AND a.TYPE = s.typeID  ORDER BY a.NAME ASC
";
			$query_result = $this->db->query($query);

			$RESULT = array();
			//mysql_fetch_row - 1,2,3
			//mysql_fetch_assoc - 'lol','xd'

			while ($row = $query_result->fetch_assoc()) {
				$alc            = array();
				$alc['ID']      = $row['ID'];
				$alc['NAME']    = $row['NAME'];
				$alc['PRICE']   = $row['PRICE'];
				$alc['PERCENT'] = $row['PERCENT'];
				$alc['VOLUME']  = $row['VOLUME'];
				$alc['TYPE']    = $row['TYPE'];
				$alc['SUBTYPE'] = $row['SUBTYPE'];
				$alc['DEPOSIT'] = $row['DEPOSIT'];
				$RESULT[]       = $alc;
			}

			echo '<h3 class="alcohols">Wpisy użytkowników</h3>';
			echo '<table class="alcohols-table">';
			echo '<thead>';
			echo '<tr><th>id</th><th>' . $COL['name'] . '</th><th>' . $COL['price'] . '</th><th>' . $COL['percent'] . '</th><th>' . $COL['volume'] . '</th><th>' . $COL['type'] . '</th><th>' . $COL['subtype'] . '</th><th>'
				. $COL['deposit'] . '</th><th>' . $COL['accept'] . '</th><th>' . $COL['delete'] . '</th>';
			echo '</thead>';
			if ($RESULT != null) {
				foreach ($RESULT as $ROW) {
					echo '<tr><td data-th="' . $COL['id'] . '">#' . $ROW['ID'] . '</td><td data-th="' . $COL['name'] . '">' . $ROW['NAME'] . '</td><td data-th="' . $COL['price'] . '">' . $ROW['PRICE'] . '</td><td data-th="'
						. $COL['percent'] . '">' . $ROW['PERCENT'] . '</td><td data-th="' . $COL['volume'] . '">' . $ROW['VOLUME'] . '</td><td data-th="' . $COL['type'] . '">' . $ROW['TYPE'] . '</td><td data-th="'
						. $COL['subtype'] . '">' . $ROW['SUBTYPE'] . '</td><td data-th="' . $COL['deposit'] . '">' . $ROW['DEPOSIT'] . '</td>';

					if ($this->permission >= 2) {
						echo '<td data-th="' . $COL['accept'] . '">';
						echo '<a class="approve" href="?v=' . $_GET['v'] . '&db=' . $_GET['db'] . '&action=a&id=' . $ROW['ID'] . '">$$$</a>';
						echo '</td>';
					}


					if ($this->permission >= 5) {
						echo '<td data-th="' . $COL['delete'] . '">';
						echo '<a class="delete" href="?v=' . $_GET['v'] . '&db=' . $_GET['db'] . '&action=d&id=' . $ROW['ID'] . '">[ X ]</a>';
						echo '</td>';
					}

					echo '</tr>';
				}
				if ($this->permission > 2) {
					$this->showAddingRow();
				}
			} else {
				if ($this->permission > 2) {
					$this->showAddingRow();
				} else {
					echo '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
				}
			}
			echo '</table>';
		}

//function handleRequest
	}