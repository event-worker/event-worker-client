<?php

/**
 * Class for formatting the date.
 *
 * Format the date for sorting.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerFormatDate
{
    /**
    * The date variable.
    * @var string
    */
    public $worker_event_date;

    /** 
     * The constructor.
     *
     * @param string $worker_event_date the start or the end date of the event
     */
    function __construct($worker_event_date)
    {
        $first_array = explode(" ", $worker_event_date);
        $date_array = explode(".", $first_array[0]);
      
        $time = str_replace(":", "", $first_array[1]);

        $final_array = $date_array[2].$date_array[1].$date_array[0].$time;
        $this->$worker_event_date = $final_array;
    }
}

?>