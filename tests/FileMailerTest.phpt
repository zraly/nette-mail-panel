<?php declare(strict_types = 1);

use Nextras\MailPanel\IPersistentMailer;
use Nextras\MailPanel\FileMailer;
use Nette\Mail\Message;
use Tester\Assert;
use Tester\Helpers;

require __DIR__ . '/bootstrap.php';


class FileMailerTest extends MailerTestCase
{
	protected function setUp(): void
	{
		Helpers::purge(TEMP_DIR);
	}


	public function createMailerInstance(): IPersistentMailer
	{
		return new FileMailer(TEMP_DIR);
	}


	public function testLoadsMessagesWithDuplicateHashSuffixes(): void
	{
		$mailer = $this->createMailerInstance();

		@mkdir(TEMP_DIR); // @ - directory may already exist
		file_put_contents(TEMP_DIR . '/20240201100000-abcdef.mail', serialize((new Message)->setSubject('first')));
		file_put_contents(TEMP_DIR . '/20240201100100-abcdef.mail', serialize((new Message)->setSubject('second')));

		Assert::same(2, $mailer->getMessageCount());

		$messages = $mailer->getMessages(10);
		Assert::same(
			['20240201100100-abcdef', '20240201100000-abcdef'],
			array_keys($messages),
		);
	}
}

(new FileMailerTest)->run();
