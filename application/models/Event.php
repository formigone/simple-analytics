<?php 

class Application_Model_Event {
	
	private $db;
	
	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function __construct() {
		$this->db = $this->db = Zend_Db_Table::getDefaultAdapter();;
	}

	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function getCategoriesList($asObj = false) {
		$categories = $this->db->fetchAll(
				$this->db->select()
				->from(array("EventCategory" => "event_categories"))
				->order(array("EventCategory.name ASC"))
			);

		$list = array();
		$label = "Select event category";

		if ($asObj) {
			array_push($list, array("value" => 0, "name" => $label));

			foreach ($categories as $category)
				array_push($list, array("value" => $category["id"], "name" => $category["name"]));
		} else {
			$list[0] = $label;

			foreach ($categories as $category)
				$list[$category["id"]] = $category["name"];
		}

		return $list;
	}

	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function getSchoolsList($asObj = false) {
		$schools = $this->db->fetchAll(
				$this->db->select()
				->from(array("School" => "schools"))
				->order(array("School.name ASC"))
			);

		$list = array();
		$label = "Select school";

		if ($asObj) {
			array_push($list, array("value" => 0, "name" => $label));

			foreach ($schools as $school)
				array_push($list, array("value" => $school["id"], "name" => $school["name"]));
		} else {
			$list[0] = $label;

			foreach ($schools as $school)
				$list[$school["id"]] = $school["name"];
		}

		return $list;
	}
	
	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function addCategory($category) {
		$this->db->insert("event_categories", array(
				"name" => $category
			)
		);

		return $this->getCategoriesList(true);
	}

	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function getStreamsList($school) {
		/* $streams = $this->db->fetchAll(
				$this->db->select()
				->from(array("Event" => "events"), array(
						"Event.id", "Event.name"))
				->joinLeft(array("Embed" => "embed_codes"),
						"Embed.school_id = Event.school_id", array(
							"embed_id" => "Embed.id", "embed_name" => "Embed.name"))
				->where("Event.school_id = ?", $school)
				->order(array("Event.name ASC", "Embed.name ASC"))
		); */

		// TODO: Make this a single statement
		$streams = $this->db->fetchAll(
				$this->db->select()
				->from(array("Event" => "events"), array(
						"Event.id", "Event.name"))
				->where("Event.school_id = ?", $school)
				->order(array("Event.name ASC"))
		);

		$embeds = $this->db->fetchAll(
				$this->db->select()
				->from(array("Embed" => "embed_codes"), array(
						"Embed.id", "Embed.name"))
				->where("Embed.school_id = ?", $school)
				->order(array("Embed.name ASC"))
		);

		$list = array(array("value" => 0, "name" => "Select stream"));

		foreach ($streams as $stream) {
			// If stream is from Ooyala
				array_push($list, array("value" => $stream["id"], "name" => $stream["name"]));
		}

		foreach ($embeds as $embed) {
			// If stream is from some custom embed code
				array_push($list, array("value" => "embed:". $embed["id"], "name" => $embed["name"]));
		}

		return $list;
	}

	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function addEvent($data) {
		$streamId = str_replace("embed:", "", $data["stream"]);
		$isEmbed = (bool) preg_match("/^embed:/", $data["stream"]);

		return $this->db->insert("schedules", array(
				"stream_id"         => $isEmbed ? null : $streamId,
				"embed_id"          => $isEmbed ? $streamId : null,
				"event_category_id" => $data["category"],
				"title"             => $data["title"],
				"air_date"          => "{$data["airDate"]} {$data["airTime"]}:00",
				"air_duration"      => new Zend_Db_Expr("SEC_TO_TIME({$data["airDuration"]} * 60)"),
				"is_embed"          => $isEmbed
			)
		);
	}

	// TODO: Fix this sucka. Seems that it's not recording the duration properly
	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function updateEvent($data) {
		$streamId = str_replace("embed:", "", $data["stream"]);
		$isEmbed = (bool) preg_match("/^embed:/", $data["stream"]);

		return $this->db->update("schedules", array(
				"stream_id"         => $isEmbed ? null : $streamId,
				"embed_id"          => $isEmbed ? $streamId : null,
				"event_category_id" => $data["category"],
				"title"             => $data["title"],
				"air_date"          => "{$data["airDate"]} {$data["airTime"]}:00",
				"is_embed"          => $isEmbed,
				"air_duration"      => new Zend_Db_Expr("SEC_TO_TIME({$data["airDuration"]} * 60)")), 
				array(
					"id = ?"          => $data["id"]
				)
		);
	}

	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	private function formatEventList($events) {
		$list = array();

		$category = "";
		$date = "";
		$index = -1;

		foreach ($events as $event) {

			if ($category != $event["category"]) {
				$index++;
				$category = $event["category"];
				$list[$index] = array(
						"category" => $category,
						"events"   => array()
				);

				if ($index == 0) {
					$list[0]["dates"] = array();
					array_push($list[0]["dates"], array(
						"dayOfWeek" => date("l", strtotime($event["air_date"])),
						"month"     => date("M", strtotime($event["air_date"])),
						"day"       => date("j", strtotime($event["air_date"])),
						"year"      => date("Y", strtotime($event["air_date"]))
						)
					);
				}
			}

			array_push($list[$index]["events"], array(
				"schedule_id" => $event["schedule_id"],
				"time"  => isset($event["air_date"]) ? date("g:i a", strtotime($event["air_date"])) : $event["time"], //date("g:i a", strtotime($event["air_date"])),
				"title" => $event["title"],
				"is_on" => $event["is_on"] == 1 ? true : false
				)
			);
		}

		return $list;
	}
	
	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	private function formatEventListByDate($events) {
		$list = array();

		$category = "";
		$date = "";
		$index = -1;

		// Group every event in its proper date group
		foreach ($events as $event) {
			$day = date("Y-m-d", strtotime($event["air_date"]));
			if ($date != $day) {
				$index++;
				$date = $day;

				$list[$index] = array(
						"events_tmp"   => array(),
						"date" => $date,
						"dates" => array(
								"dayOfWeek" => date("l", strtotime($event["air_date"])),
								"month"     => date("M", strtotime($event["air_date"])),
								"day"       => date("j", strtotime($event["air_date"])),
								"year"      => date("Y", strtotime($event["air_date"]))
						)
				);
			}

			array_push($list[$index]["events_tmp"], array(
				"category" => $event["category"],
				"schedule_id" => $event["schedule_id"],
				"time" => date("g:i a", strtotime($event["air_date"])),
				"title" => $event["title"],
				"is_on" => $event["is_on"] == 1 ? true : false
				)
			);
		}

		// Now sort each event category within each date
		foreach ($list as &$item) {
//			$events = $this->formatEventList($item["events"]);
			$item["categories"] = $this->formatEventList($item["events_tmp"]); //$events;
			unset ($item["events_tmp"]);
		}

		return $list;
	}
	
	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function getLiveEventsList() {
		$date = date("y-m-d", time());

		$events = $this->db->fetchAll(
				$this->db->select()
				->from(array("Schedule" => "schedules"), array(
						"schedule_id" => "Schedule.id",
						"title"       => "Schedule.title",
						"air_date"    => "Schedule.air_date",
						"is_on"       => "IF(1, 1, 0)"
					)
				)
				->join(array("Category" => "event_categories"),
						"Category.id = Schedule.event_category_id", array(
								"category"    => "Category.name"
						)
				)
				->where("Schedule.air_date >= ?", "{$date} 00:00:00")
				->where("Schedule.air_date <= ?", "{$date} 23:59:59")
				->where("IF(NOW() >= Schedule.air_date AND NOW() <= ADDTIME(Schedule.air_date, Schedule.air_duration), true, false)")
				->order(array("Category.name ASC", "Schedule.air_date ASC"))
		);

		return $this->formatEventList($events);
	}
	
	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function getEventsList($date, $daysToAdd = 0) {
		$endDate = $date;
		if ($daysToAdd > 0)
			$endDate = date("Y-m-d", strtotime("{$endDate} + {$daysToAdd} days"));

		$events = $this->db->fetchAll(
				$this->db->select()
				->from(array("Schedule" => "schedules"), array(
								"schedule_id"   => "Schedule.id",
								"title"         => "Schedule.title",
								"air_date"      => "Schedule.air_date",
								"is_on"         => "IF(NOW() >= Schedule.air_date AND NOW() <= ADDTIME(Schedule.air_date, Schedule.air_duration), 1, 0)"
								)
						)
				->join(array("Category" => "event_categories"),
						"Category.id = Schedule.event_category_id", array(
								"category"    => "Category.name"
								)
						)
				->where("Schedule.air_date >= ?", "{$date} 00:00:00")
				->where("Schedule.air_date <= ?", "{$endDate} 23:59:59")
				->order(array("Schedule.air_date ASC", "Category.name ASC"))
		);

		return $this->formatEventListByDate($events);
	}

	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function getSchedule($scheduleId) {
		return $this->db->fetchRow(
				$this->db->select()
				->from(array("Schedule" => "schedules"))
				->joinLeft(array("Event" => "events"),
						"Event.stream_id = Schedule.stream_id", array(
								"school_id" => "Event.school_id"
							)
					)
				->joinLeft(array("Embed" => "embed_codes"),
						"Embed.id = Schedule.embed_id", array(
								"school_id" => "Embed.school_id"
						)
				)
				->where("Schedule.id = ?", $scheduleId)
			);
	}

	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function getScheduledStreamData($scheduleId) {

		$resp = $this->db->fetchRow(
				$this->db->select()
				->from(array("Schedule" => "schedules"), null)
				->joinLeft(array("Stream" => "streams"),
						"Stream.id = Schedule.stream_id", array(
								"title"    => "Schedule.title",
								"event_id" => "Schedule.id",
								"asset_id" => "Stream.asset_id"
							)
					)
				->joinLeft(array("Embed" => "embed_codes"),
						"Embed.id = Schedule.embed_id", array(
								"title"    => "Schedule.title",
								"event_id" => "Schedule.id",
								"code"     => "Embed.code"
						)
				)
				->where("Schedule.id = ?", $scheduleId)
			);
		
		return $resp;
	}
	
	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function getScheduledNowStreamData() {
		return $this->db->fetchRow(
				$this->db->select()
				->from(array("Schedule" => "schedules"), null)
				->joinLeft(array("Stream" => "streams"),
						"Stream.id = Schedule.stream_id", array(
								"title"    => "Schedule.title",
								"event_id" => "Schedule.id",
								"asset_id" => "Stream.asset_id"
							)
					)
				->joinLeft(array("Embed" => "embed_codes"),
						"Embed.id = Schedule.embed_id", array(
								"title"    => "Schedule.title",
								"event_id" => "Schedule.id",
								"code"     => "Embed.code"
						)
				)
				->where("Schedule.air_date <= NOW() AND addtime(Schedule.air_date, Schedule.air_duration) >= NOW()")
		);
	}
	
	private function escapseSql($keyword) {
		return str_replace(array("%", "_"), array("\%", "\_"), $keyword);
	}
	
	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function getEventsListByKeyword($date, $keyword, $daysToAdd = 0) {
		$keyword = $this->escapseSql($keyword);
		
		$endDate = $date;
		if ($daysToAdd > 0)
			$endDate = date("Y-m-d", strtotime("{$endDate} + {$daysToAdd} days"));

		$events = $this->db->fetchAll(
				$this->db->select()
				->from(array("Schedule" => "schedules"), array(
						"schedule_id" => "Schedule.id",
						"title"       => "Schedule.title",
						"air_date"    => "Schedule.air_date",
						"is_on"       => "IF(NOW() >= Schedule.air_date AND NOW() <= ADDTIME(Schedule.air_date, Schedule.air_duration), 1, 0)"
				)
				)
				->join(array("Category" => "event_categories"),
						"Category.id = Schedule.event_category_id", array(
								"category"    => "Category.name"
						)
				)
				->where("Schedule.air_date >= ?", "{$date} 00:00:00")
				->where("Schedule.air_date <= ?", "{$endDate} 23:59:59")
				->where("Schedule.title LIKE ?", "%{$keyword}%")
				->order(array("Schedule.air_date ASC", "Category.name ASC"))
		);

		return $this->formatEventListByDate($events);

		/* $list = array();

		$category = "";
		$date = "";
		$index = -1;

		foreach ($events as $event) {
			if ($category != $event["category"]) {
				$index++;
				$category = $event["category"];
				$list[$index] = array(
						"category" => $category,
						"events"   => array()
				);

				if ($index == 0) {
					$list[0]["dates"] = array();
					array_push($list[0]["dates"], array(
					"dayOfWeek" => date("l", strtotime($event["air_date"])),
					"month"     => date("M", strtotime($event["air_date"])),
					"day"       => date("j", strtotime($event["air_date"])),
					"year"      => date("Y", strtotime($event["air_date"]))
					)
					);
				}
			}

			array_push($list[$index]["events"], array(
			"schedule_id" => $event["schedule_id"],
			"time" => date("g:i a", strtotime($event["air_date"])),
			"title" => $event["title"],
			"is_on" => $event["is_on"] == 1 ? true : false
			)
			);
		}
		//echo "<pre>";var_dump($list);exit;
		return $list; */
	}
}