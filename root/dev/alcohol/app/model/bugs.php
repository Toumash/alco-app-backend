<?php
	if (!defined('R')) {
		die('This script cannot be run directly');
	}
	require_once R . '/model/model.php';
	require_once(R . '/vendors/bitbucket/bitbucket.lib.php');

	class BugsModel extends Model
	{
		public function __construct()
		{
			parent::__construct();
			$this->log = Logger::getLogger(__CLASS__);
		}

		/**
		 * @param        $title
		 * @param string $description
		 * @param string $user
		 * @param string $kind bug,enhancement,proposal,task
		 * @param string $priority
		 *
		 * @return bool state of the Bitbucket bug request
		 */
		public function reportBug(
			$title,
			$description = 'No description',
			$user = 'Anonymous',
			$kind = 'bug',
			$priority = 'major'
		) {
			$description = strlen($description) > 0 ? $description : 'No description';
			$user        = strlen($user) > 0 ? $user : 'Anonymous';
			$kind        = strlen($kind) > 0 ? $kind : 'bug';
			$priority    = strlen($priority) > 0 ? $priority : 'major';

			$basicAuth        = 'Q1NCdWdSZXBvcnRlcjphbGNvcmVwb3J0MzIx'; // Base64 encode of: username:password of your Read only user.
			$bitBucketAccount = 'code-sharks'; // Team account which contains your repo.
			$bitBucketRepo    = 'alcohol'; // Name of your repo.
			$companyName      = 'Code Sharks'; // The name of your company or department (used for the confirmation email).


			$status = submitBug(
				$title,
				$description,
				$user,
				$bitBucketAccount,
				$bitBucketRepo,
				$basicAuth,
				null,
				'new',
				$priority,
				$kind
			);
			if ($status === false) {
				return false;
			} else {
				sendBugEmail(
					$user,
					$status['issueid'],
					$companyName,
					$status['issueurl']
				);

				return true;
			}
		}
	}