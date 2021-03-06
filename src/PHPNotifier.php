<?php
/**
 * Created by IntelliJ IDEA.
 * User: KryDos
 * Date: 22/03/16
 * Time: 21:31
 */

namespace PHPNotifier;


use PHPNotifier\interfaces\RWInterface;

class PHPNotifier
{
    /**
     * This constant has partial name of
     * Reader/Writer class, that will be
     * used in the library
     */
    const FILE_METHOD = 'File';

    /** @var  RWInterface $rw*/
    protected $rw;

    public function __construct($method, $store)
    {
        $this->rw = new RWFactory($method, $store);
    }

    /**
     * puts task inside a store
     *
     * @param Task $task
     */
    protected function schedule(Task $task)
    {
        $this->rw->getWriter()->createDb();
        $this->rw->getWriter()->write($task);
    }

    /**
     * set exact time when command have to be executed
     *
     * @param mixed $when
     * @param string $command
     * @param array $params
     */
    public function scheduleTaskAtTime($when, $command, array $params = [])
    {
        if ($when instanceof \DateTime) {
            $task = new Task($when->getTimestamp(), $command, $params);
        } elseif (is_numeric($when)) {
            $task = new Task($when, $command, $params);
        } elseif(is_string($when) && ($timestamp = strtotime($when))) {
            $task = new Task($timestamp, $command, $params);
        } else {
            throw new \InvalidArgumentException('time argument is not supported. Only integer, DateTime or valid date string are allowed');
        }

        $this->schedule($task);
    }

    /**
     * execute task after $run_after seconds
     *
     * @param integer $run_after
     * @param string $command
     * @param array $params
     */
    public function scheduleTaskIn($run_after, $command, array $params = [])
    {
        $task = new Task((time() + $run_after), $command, $params);
        $this->schedule($task);
    }

    /**
     * @return interfaces\ReaderInterface
     */
    public function getReader()
    {
        return $this->rw->getReader();
    }

    /**
     * @return interfaces\WriterInterface
     */
    public function getWriter()
    {
        return $this->rw->getWriter();
    }
}