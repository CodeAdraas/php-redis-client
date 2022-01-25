<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Dodo\Redis\RedisClient;
use Dodo\Redis\Exception\RedisClientException;
use Dodo\Redis\Command\Core\Resolver;
use Dodo\Redis\Command\Get;
use Dodo\Redis\Command\Set;
use Dodo\Redis\Command\Del;
use Dodo\Redis\Command\Unlink;


final class RedisClientTest extends TestCase
{
    public function test_redis_connection_failure_with_debug()
    {
        $wrongHost = "128.0.0.1";

        $client = new RedisClient([ 
            "host" => $wrongHost,
            "debug" => true,
        ]);

        $this->expectException(RedisClientException::class);
        $client->getConnection()->open();
    }

    public function test_redis_connection_failure_no_debug()
    {
        $wrongHost = "128.0.0.1";

        $redis = new RedisClient([
            "host" => $wrongHost,
            "debug" => false
        ]);

        $query = $redis->ping();
        $this->assertFalse($query->status);
        $this->assertFalse($query->get());
    }

    /**
     * Test reddis client ping 
     */
    public function test_redis_client_ping()
    {
        $redis = new RedisClient([ "debug" => true ]);

        $query = $redis->ping(); // The ping method is not chainable and thus
                                 // directly invoking execution

        $this->assertTrue($query->status);
        $this->assertEquals( "pong", $query->get() );
    }

    /**
     * Test reddis client internal core argument sorting
     */
    public function test_redis_client_command_resolver_sort_arguments()
    {
        $commandResolver = new Resolver();

        $commandResolver->addCommandArgument([ "position" => 10, "value" => "ten" ]);
        $commandResolver->addCommandArgument([ "position" => 1, "value" => "one" ]);
        $commandResolver->addCommandArgument([ "position" => 3, "value" => "three" ]);

        $unsortedArguments = $commandResolver->getCommandArguments();
        $this->assertEquals( "ten", $unsortedArguments[0] );
        $this->assertEquals( "one", $unsortedArguments[1] );
        $this->assertEquals( "three", $unsortedArguments[2 ]);

        $result = $commandResolver->getSortedCommandArguments();
        $this->assertEquals( "one", $result[0] );
        $this->assertEquals( "three", $result[1] );
        $this->assertEquals( "ten", $result[2] );
    }

    /**
     * Test reddis client internal core command ID resolving
     */
    public function test_redis_client_command_resolver_resolve_command()
    {
        $commandResolver = new Resolver();
        $arguments = [];

        $commands = [
            "get" => new Get($commandResolver, $arguments),
            "set" => new Set($commandResolver, $arguments),
            "del" => new Del($commandResolver, $arguments),
            "unlink" => new Unlink($commandResolver, $arguments),
        ];

        foreach ($commands as $commandId => $commandObj) {
            $commandResolver->addCommandToBuffer( $commandObj );                   // Add command object to buffer
            $this->assertEquals( $commandId, $commandResolver->getCommand() ); // Test if command resolver resolves correct command string
            $commandResolver->flush();
        }
    }

    /**
     * Test reddis client get command with json format
     */
    public function test_redis_client_get_json()
    {
        $redis = new RedisClient([ "debug" => true ]);

        $query = $redis->get("user")
            ->json()
            ->exec();

        $this->assertTrue($query->status);
        $this->assertIsObject($query->get());
    }

    /**
     * Test reddis client set command with json format
     */
    public function test_redis_client_set_json()
    {
        $redis = new RedisClient([ "debug" => true ]);

        $query = $redis->set("user", array(
                "id" => "baf23630-8d59-4936-9850-1f2b188775fe",
                "first_name" => "Zeus",
                "sur_name" => "Arthemis",
                "occupation" => "Bringing order"
            ))
            ->json()
            ->exec();

        $this->assertTrue($query->status);
        $this->assertIsObject($query->get());
    }

    /**
     * Test reddis client del command
     */
    public function test_redis_client_del_and_unlink()
    {
        $redis = new RedisClient([ "debug" => true ]);
        $key = "test_del_and_unlink";
        $value = "test_value";

        // Set a value to delete
        $redis->set($key, $value)->exec();
        // Delete
        $query = $redis->del("test_delete");
        $this->assertTrue($query->status);

        $redis->set($key, $value)->exec();
        // Unlink (alias of del command)
        $query = $redis->unlink("test_delete");
        $this->assertTrue($query->status);


        $query = $redis->get("test_delete")->exec();
        $this->assertFalse($query->get());
    }

    /**
     * Test reddis client set command with TTL (time to live)
     */
    public function test_redis_client_set_with_TTL()
    {
        $redis = new RedisClient([ "debug" => true ]);
        $key = "test_TTL";
        $value = "test_value";

        // Set a test value that will
        // expire in 2 (s)
        $redis->set($key, $value)
            ->ex(2)
            ->exec();

        sleep(1);

        // Key should be retrievable
        $query = $redis->get($key)->exec();
        $this->assertEquals( $value, $query->get() );
        
        sleep(1);

        // Key should NOT be retrievable
        $query = $redis->get($key)->exec();
        $this->assertFalse($query->get());
    }

    /**
     * Test reddis client set command with TTL (time to live)
     * - 'ix' stands for a conditional, namely 'if exists'
     * - 'nx' stands for a conditional, namely 'if not exists'
     */
    public function test_redis_client_set_nx_and_ix()
    {
        $redis = new RedisClient([ "debug" => true ]);
        $key = "test_ix_and_nx";
        $oldValue = "test_value";
        $newValue = "new_test_value";

        // Set test value if it doesn't
        // exist yet in database
        $redis->set($key, $oldValue)
            ->nx()
            ->exec();

        // Control
        $query = $redis->get($key)->exec();
        $this->assertEquals( $oldValue, $query->get() );

        // 'Try' replacing old value
        $redis->set($key, $newValue)
            ->nx()
            ->exec();

        // Get command should still return
        // old value
        $query = $redis->get($key)->exec();
        $this->assertEquals( $oldValue, $query->get() );

        // 'Try' replacing old value 
        // with new value IF does exist
        $redis->set($key, $newValue)
            ->ix()
            ->exec();

        // Get command should now return
        // new value
        $query = $redis->get($key)->exec();
        $this->assertEquals( $newValue, $query->get() );

        // Delete test key from database
        $redis->del($key);
    }

    /**
     * Test reddis client append command
     */
    public function test_redis_client_append()
    {
        $redis = new RedisClient([ "debug" => true ]);
        $key = "test_append";
        $value = "test_value";
        $append = "_append";

        // Set test value if it doesn't
        // exist yet in database
        $redis->set($key, $value)
            ->ex(5)
            ->exec();

        // Append value to key
        $query = $redis->append($key, $append)->exec();
        // Should return new size of string
        $this->assertIsInt( $query->get() );
        // Control
        $query = $redis->get($key)->exec();
        // String should be old value + appended value
        $this->assertEquals( ($value . $append), $query->get() );
    }
}