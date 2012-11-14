<?php

// bootstrap
chdir(dirname(__FILE__));
require "../src/Kafka.php";

// arguments
$topic = isset($_SERVER['argv'][1])
    ? $_SERVER['argv'][1]
    : exit("\nUsage: php producer.php <topic_name>\n\n");

// connection
$kafka = new Kafka('localhost', 9092, 6, 0.71);

// request channel
$producer = $kafka->createProducer();

// add a few messages
$producer->add(
    new Kafka_Message(
        $topic, 0,
        'MESSAGE 1 - passed as uncompressed message object',
        Kafka::COMPRESSION_NONE
    )
);

for ($i=2; $i<100; $i++)
{
	$producer->add(
		new Kafka_Message(
			$topic, 0,
			"MESSAGE $i - passed as consecutive compressed message object",
			Kafka::COMPRESSION_GZIP
		)
	);
}

$producer->add(
    new Kafka_Message(
        'test', 0,
        'MESSAGE 100 - passed as compressed message object to a different topic `test`',
        Kafka::COMPRESSION_GZIP
    )
);

if ($producer->produce())
{
    echo "\nPublished.\n\n";
}

// go home
$producer->close();
