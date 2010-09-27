# QueueEmail

QueueEmail is a CakePHP plugin that allows you to queue emails in your database
instead of sending them directly out. This is helpful for sending large batches
of emails via a CRON job.

## Usage

Create the table

   cake schema create QueueEmail.queue

Use the QueueEmail component

    var $components = array('QueueEmail.QueueEmail');

Then send your emails as you normally would

    $this->QueueEmail->to = 'test@test.com';
    $this->QueueEmail->from = 'test@test.com';
    $this->QueueEmail->subject = 'A queued Email';
    $this->QueueEmail->send();

Instead of sending, the email will be stored in the database. You can do anything
you would do on the built-in Email component with QueueEmail, such as sending
via smtp, attaching files, etc.

When you're ready to send, use the shell provided. I recommend setting up a CRON
job to execute the shell every 5 minutes.

    /path/to/site/cake/console/cake -app "/path/to/site/app" queue_sender send

By default, the shell sends out 50 at a time. You can change this by passing the
`-batchSize` parameter

    /path/to/site/cake/console/cake -app "/path/to/site/app" queue_sender send -batchSize 100

This would send 100 emails. You can also send individual emails by passing the
id directly after the send method, as such

    /path/to/site/cake/console/cake -app "/path/to/site/app" queue_sender send 12

Would send email number 12.

If you have emails you don't want to queue, simply change the `$queue` var to
false.

    $this->QueueEmail->queue = false; // this email won't be queued

This will send the email as the regular EmailComponent normally would.

## Managing

The QueueEmail plugin also comes with some limited views that allow you to see
what emails are currently queued. Visit `/queue_email/queues/index`

## Annoyances

Much of the information is stored as serialized array strings and I'm not fond
of that. If you have suggestions to avoid this let me know.

## Future

* Add ability to send one or multiple emails from the management console.