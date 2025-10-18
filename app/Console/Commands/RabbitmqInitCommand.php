<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Throwable;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;

class RabbitmqInitCommand extends Command
{
    protected $signature = 'rabbitmq:init
                           {--recreate= : Delete queue with data and then create again}';

    protected $description = 'Initialize RabbitMQ queue';

    /**
     * Used Command line
     *
     * @var string|null
     */
    private string $commandLine;

    /**
     * @param RabbitMQConnector $connector
     * @throws Throwable
     */
    public function handle(RabbitMQConnector $connector): int
    {
        global $argv;
        $this->commandLine = implode(' ', $argv);

        try {
            return $this->handleInner($connector);
        } catch (Throwable $e) {
            $this->error("{$this->commandLine} error : " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @param RabbitMQConnector $connector
     * @throws Throwable
     */
    public final function handleInner(RabbitMQConnector $connector): int
    {
        $queueName = config('queue.connections.rabbitmq.queue');;
        if (trim($queueName) == '') {
            $this->error("Queue name is empty. Please check queue.connections.rabbitmq.queue configuration value");
            return 1;
        }

        $config = config('queue.connections.rabbitmq');

        $queue = $connector->connect($config);

        $this->info("Declaring queue {$queueName}.");
        if ($queue->isQueueExists($queueName)) {
            $recreate = $this->option('recreate');
            if ($recreate) {
                $this->info("Queue {$queueName} already exists, deleting.");
                $queue->deleteQueue($queueName);
            } else {
                $this->info("Queue {$queueName} already exists, skip.");
                return 0;
            }
        }

        $queueExtraArguments = config('queue.connections.rabbitmq.rabbitmq_queue_extra_arguments');
        $queueExtraArguments = $queueExtraArguments ? json_decode($queueExtraArguments, true) : [];
        $queue->declareQueue(
            $queueName,
            true /* durable */,
            false /* auto-delete */,
            $queueExtraArguments
        );

        $this->info("Queue {$queueName} declared successfully.");
        return 0;
    }
}
