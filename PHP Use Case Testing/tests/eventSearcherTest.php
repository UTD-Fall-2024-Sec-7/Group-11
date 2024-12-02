<?php
class EventSearcherTest extends \PHPUnit\Framework\TestCase 
{
    private $mockConn;
    private $mockStmt;
    private $mockResult;
    private $eventSearcher;

    private $sampleData;

    public function setUp(): void 
    {
        // Mock database connection
        $this -> mockConn = $this -> createMock(mysqli :: class);
        
        // Mocking SQL statement object
        $this -> mockStmt = $this -> createMock(mysqli_stmt :: class);
        
        // Mock result object 
        $this -> mockResult = $this -> createMock(mysqli_result :: class);
    
        // Sample data for events, stored in database
        $this -> sampleData = 
        [
            ['name' => 'Social Night', 'date' => '2024-12-02'], // [0]
            ['name' => 'Social Night 2', 'date' => '2024-12-03'], // [1]
            ['name' => 'Tech Talk', 'date' => '2024-12-04'], // [2]
            ['name' => 'Conference', 'date' => '2024-12-05'], // [3]
            ['name' => 'Study Group', 'date' => '2024-12-06'], // [4]
            ['name' => 'Social Night 3', 'date' => '2024-12-10'] // [5]
        ];
    
        // Sets up database operations
        $this -> mockConn -> method('prepare') -> willReturn($this -> mockStmt);
        $this -> mockStmt -> method('get_result') -> willReturn($this -> mockResult);
        $this -> mockStmt -> method('bind_param') -> willReturn(true);
        $this -> mockStmt -> method('execute') -> willReturn(true);
    
        // Used to provide database values to the EventSearcher object
        $this -> mockResult -> method('fetch_assoc') -> willReturnOnConsecutiveCalls($this -> sampleData[0], $this -> sampleData[1], $this -> sampleData[2], $this -> sampleData[3], $this -> sampleData[4], $this -> sampleData[5], null);
    
        // Instantiates EventSearcher object with connection to the mySQL database
        $this -> eventSearcher = new EventSearcher($this -> mockConn);
    }

    // -----------     Search Events Use Cases     ----------
    // Use case test where date range and title query are valid
    public function testSearchEvents1()
    {
        // Searches with "Social Night" as query within range 2024-12-02 and 2024-12-06
        $events = $this -> eventSearcher -> searchEvents('Social Night', '2024-12-02', '2024-12-06');
    
        // Assert that only 2 events match the criteria 
        $this -> assertCount(2, $events);
        // The two matching criteria are "Social Night" and "Social Night 2"
        $this -> assertEquals('Social Night', $events[0]['name']);
        $this -> assertEquals('Social Night 2', $events[1]['name']);
    }

    // Use case test where date range is exceptional (Start date is later than end date)
    public function testSearchEvents2()
    {
        // 2024-12-06 start date is later than 2024-12-02 end date
        $result = $this -> eventSearcher -> searchEvents('Social Night', '2024-12-06', '2024-12-02');
        $this -> assertEquals(['Start date must be earlier or equal to end date.'], $result);
    }

    // Use case test where search query is invalid (empty)
    public function testSearchEvents3()
    {
        $result = $this -> eventSearcher -> searchEvents('', '2024-12-02', '2024-12-06');
        $this -> assertEquals(['Search string cannot be empty.'], $result);
    }

    // Use case test where date range is invalid (End date format is invalid)
    public function testSearchEvents4()
    {
        // End month cannot be 64
        $result = $this -> eventSearcher -> searchEvents('Social Night', '2024-12-02', '2024-64-06');
        $this -> assertEquals(['Invalid end date format.'], $result);
    }

    // Use case test where date range is invalid (Start date format is invalid)
    public function testSearchEvents5()
    {
        // Start day cannot be 32
        $result = $this -> eventSearcher -> searchEvents('Social Night', '2024-12-32', '2024-12-06');
        $this -> assertEquals(['Invalid start date format.'], $result);
    }

    // Use case test where search query is exceptional (escape character)
    public function testSearchEvents6()
    {
        // Escape chracter "\n" is used in the query
        $result = $this -> eventSearcher -> searchEvents("\n", '2024-12-02', '2024-12-06');
        $this -> assertEquals(['Search string cannot contain escape characters.'], $result);
    }
}
// To run: ./vendor/bin/phpunit
?>
 