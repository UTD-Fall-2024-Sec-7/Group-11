<?php
class EventSearcher
{
    private $mysqli;

    // Creates a mysql database when EventSearcher is constructed
    public function __construct(mysqli $mysqli)
    {
        $this -> mysqli = $mysqli;
    }

    // Testcase 3 - Search Events (Title Query, Date Range Criteria)
    public function searchEvents(string $query, string $startDate, string $endDate)
    {
        // Start date cannot be in invalid format
        if (!$this -> validateDate($startDate)) 
        {
            return ['Invalid start date format.'];
        }
        // End date cannot be in invalid format
        if (!$this -> validateDate($endDate)) 
        {
            return ['Invalid end date format.'];
        }
        // Start date cannot be later than end date
        if (strtotime($startDate) > strtotime($endDate)) 
        {
            return ['Start date must be earlier or equal to end date.'];
        }
        // Title query cannot be empty
        if (strlen($query) == 0) 
        {
            return ['Search string cannot be empty.'];
        }

        // Title query cannot be more than 50 characters
        if(strlen($query) > 50)
        {
            return ['Search string is too long.'];
        }
        // Title query cannot contain escape characters
        if (preg_match('/[\\x00-\\x1F\\x7F]/', $query)) 
        {
            return ['Search string cannot contain escape characters.'];
        }
    
        // Prepares SQL instruction to select all the rows of the events table
        $stmt = $this -> mysqli -> prepare("SELECT * FROM events");
    
        // Error check to see if query succeeds
        if (!$stmt -> execute()) 
        {
            return ['Database query failed.'];
        }
    
        // Resulting dictionary from query is stored in result
        $result = $stmt -> get_result();
        $events = [];
    
        // Fetches data from each row in the events table
        while ($row = $result -> fetch_assoc()) 
        {
            // Checks if substring of title is in the row and that the date is within limits
            if (strpos($row['name'], $query) !== false && $row['date'] >= $startDate && $row['date'] <= $endDate) 
            {
                // Adds the row to the return array
                $events[] = $row;
            }
        }
    
        // If the return array is empty, there are no events that match the criteria
        if (empty($events)) 
        {
            return ['No matching events found.'];
        }
    
        // Returns array with matched events
        return $events;
    }
    

    // Helper function to validate the date string
    private function validateDate(string $date)
    {
        // Transforms date string to DateTime object
        $d = DateTime :: createFromFormat('Y-m-d', $date);
        // Returns comparison of original string and DateTime object
        return $d && $d -> format('Y-m-d') === $date;
    }
}
?>
