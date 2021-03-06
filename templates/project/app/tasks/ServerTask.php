<?
use Websocket\Server,
Phalcon\Tools\Cli;
/**
 * Manage Websocket server
 */
class ServerTask extends \Phalcon\CLI\Task
{
    public function mainAction() {
        Cli::success('- start', true);
    }

    /**
     * Start server
     */
    public function startAction() {
        $server = new Server('0.0.0.0','9000');
        try {
            $server->run();
        }
        catch (Exception $e) {
            $server->stdout($e->getMessage());
        }
    }

}