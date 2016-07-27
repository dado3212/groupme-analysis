<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	// if (!defined('API')) {
	// 	die('Direct access not permitted.');
	// }
	 define('API', true);
	require_once('secret.php');

	// echo "<pre>" . print_r(analyze("16897222"), true) . "</pre>";
	leaveGroup("16897222");

	/**
	 * Attempts to find the group in which it has a given name
	 * @param $name The name that it's looking for
	 * @return the group id if successful, otherwise false
	 */
	function findGroupByName($name) {
		global $TOKEN;
		global $ME;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.groupme.com/v3/groups?token=${TOKEN}&per_page=100");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$contents = curl_exec($ch);
		curl_close($ch);

		$groups = json_decode($contents, true)['response'];
		foreach ($groups as $group) {
			foreach ($group['members'] as $member) {
				if ($member['user_id'] == $ME && $member['nickname'] == $name) {
					return $group['group_id'];
				}
			}
		}
		return false;
	}

	/**
	 * Analyzes the group for useful data
	 * @param $group The Group ID to check against
	 * @return a complex array of formatted information
	 */
	function analyze($group) {
		// Initialize
		global $TOKEN;
		date_default_timezone_set("America/New_York");

		$members = getMembers($group);

		$messages = getMessages($group);

		usort($messages, function ($a, $b) { return count($a["likes"]) < count($b["likes"]); });

		// Total data
		$totalComments = 0;
		$totalLikes = 0;
		$words = 0;
		$names = [];
		$topics = [];
		$mostPopular = array_slice($messages, 0, 10);

		foreach ($messages as $message) {
			$totalComments += 1;
			$totalLikes += count($message["likes"]);
			$words += count(explode(" ", $message["text"]));

			if ($message["sender_type"] == "user") { // User message
				if (array_key_exists($message["sender_id"], $members)) {
					$poster = $message["sender_id"];

					// Increment total number of received likes, comments, and words
					$members[$poster]["total_number"] += 1;
					$members[$poster]["total_likes_received"] += count($message["likes"]);
					$members[$poster]["total_words"] += count(explode(" ", $message["text"]));

					// Add to array of all posts and their times
					$members[$poster]["times"][date('G',$message["time"])] += 1;

					// Check top comment
					if (count($message["likes"]) > $members[$poster]["max_likes"]) {
						$members[$poster]["max_likes"] = count($message["likes"]);
						$members[$poster]["best_comment"] = $message;
					}

					// Increment likes given
					for ($i = 0; $i < count($message["likes"]); $i++) {
						$liker = $message["likes"][$i];
						$others = $message["likes"];

						unset($others[$i]);

						foreach ($others as $other) {
							if (array_key_exists($other, $members[$poster]["shared"])) {
								$members[$poster]["shared"][$other] += 1;
							} else {
								$members[$poster]["shared"][$other] = 1;
							}
						}

						if (array_key_exists($liker, $members)) {
							$members[$liker]["total_likes_given"] += 1;

							// Check self likes
							if ($liker == $poster) {
								$members[$poster]["self_likes"] += 1;
							}
						} else { // Liked by a non-existent member

						}
					}
				} else { // Not a current member...

				}
			} else if ($message["sender_type"] == "system") { // System messages
				if (
					strpos($message["text"], "changed the topic to: ") !== false &&
					strpos($message["text"], "changed the topic to: ") > 0 &&
					(strpos($message["text"], "changed the group's name to ") === false || strpos($message["text"], "changed the group's name to ") > strpos($message["text"], "changed the topic to: "))
				) { // Topic change
					$topics[] = [
						"name" => substr($message["text"], strpos($message["text"], "changed the topic to: ") + 22),
						"time" => $message["time"],
						"likes" => count($message["likes"]),
					];
				} else if (
					strpos($message["text"], "changed the group's name to ") !== false &&
					strpos($message["text"], "changed the group's name to ") > 0 &&
					(strpos($message["text"], "changed the topic to: ") === false || strpos($message["text"], "changed the group's name to ") < strpos($message["text"], "changed the topic to: "))
				) { // Group name change
					$names[] = [
						"name" => substr($message["text"], strpos($message["text"], "changed the group's name to ") + 28),
						"time" => $message["time"],
						"likes" => count($message["likes"]),
					];
				}
			} else { // Not currently handled (bot messages?)
				echo $message["sender_type"];
			}
		}

		// Clean up useful information
		foreach ($members as $key => $member) {
			$shared = $member["shared"];
			arsort($shared);

			$members[$key]["shared"] = array_slice($shared, 0, 3, true);
		}

		return [
			"individuals" => $members,
			"total" => [
				"comments" => $totalComments,
				"likes" => $totalLikes,
				"names" => $names,
				"topics" => $topics,
				"words" => $words,
				"popular" => $mostPopular,
			],
		];
	}

	/**
	 * Finds and formats all members of a group
	 * @param $group The group ID
	 * @return An array of formatted members
	 */
	function getMembers($group) {
		global $TOKEN;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.groupme.com/v3/groups/${group}?token=${TOKEN}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$contents = curl_exec($ch);
		curl_close($ch);

		$rawMembers = json_decode($contents, true)['response']['members'];
		$members = [];

		foreach ($rawMembers as $member) {
			$members[$member['user_id']] = [
				'name' => $member['nickname'],
				'image' => $member['image_url'],
				'total_likes_received' => 0,
				'total_likes_given' => 0,
				'total_number' => 0,
				'self_likes' => 0,
				'total_words' => 0,
				'max_likes' => 0,
				'best_comment' => '',
				'shared' => [],
				'times' => [
					0 => 0,
					1 => 0,
					2 => 0,
					3 => 0,
					4 => 0,
					5 => 0,
					6 => 0,
					7 => 0,
					8 => 0,
					9 => 0,
					10 => 0,
					11 => 0,
					12 => 0,
					13 => 0,
					14 => 0,
					15 => 0,
					16 => 0,
					17 => 0,
					18 => 0,
					19 => 0,
					20 => 0,
					21 => 0,
					22 => 0,
					23 => 0,
				],
			];
		}

		return $members;
	}

	/**
	 * Finds and formats all messages of a group
	 * @param $group The group ID
	 * @return An array of formatted messages
	 */
	function getMessages($group, $messages = [], $before = null) {
		global $TOKEN;

		if ($before) {
			$url = "https://api.groupme.com/v3/groups/${group}/messages?token=${TOKEN}&limit=100&before_id=${before}";
		} else {
			$url = "https://api.groupme.com/v3/groups/${group}/messages?token=${TOKEN}&limit=100";
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$contents = json_decode(curl_exec($ch), true);
		curl_close($ch);

		if ($contents) {
			foreach ($contents['response']['messages'] as $msg) {
				$messages[] = [
					"attachments" => $msg["attachments"],
					"likes" => $msg["favorited_by"],
					"id" => $msg["id"],
					"sender_id" => $msg["sender_id"],
					"sender_type" => $msg["sender_type"],
					"text" => $msg["text"],
					"time" => $msg["created_at"],
				];
			}

			return getMessages($group, $messages, $messages[count($messages)-1]["id"]);
		} else {
			return $messages;
		}
	}

	/**
	 * Sends a message to a group
	 * @param $group The group ID
	 * @param $message The message to send
	 * @return void
	 */
	function sendMessage($group, $message) {
		global $TOKEN;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.groupme.com/v3/groups/{$group}/messages?token=${TOKEN}");
		curl_setopt($ch, CURLOPT_HTTPHEADER, 'Content-Type: application/json');        
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, '{"message": {"text":"' . $message . '"}}');
		$contents = json_decode(curl_exec($ch), true);
		curl_close($ch);
	}

	/**
	 * Leaves a group
	 * @param $group The group ID
	 * @return void
	 */
	function leaveGroup($group) {
		global $TOKEN;
		global $ME;

		// Finds membership ID
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.groupme.com/v3/groups/${group}?token=${TOKEN}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$contents = curl_exec($ch);
		curl_close($ch);

		$rawMembers = json_decode($contents, true)['response']['members'];

		foreach ($rawMembers as $member) {
			if ($member["user_id"] === $ME) {
				// Leaves group
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://api.groupme.com/v3/groups/{$group}/members/{$member['id']}/remove?token={$TOKEN}");
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_exec($ch);
				curl_close($ch);

				return;
			}
		}
	}
?>