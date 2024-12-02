<?php
class MemberSearcherTest extends \PHPUnit\Framework\TestCase 
{
    private $mockConn;
    private $mockStmt;
    private $mockResult;
    private $memberSearcher;

    private $sampleData;

    public function setUp(): void 
    {
        // Mock database connection
        $this -> mockConn = $this -> createMock(mysqli :: class);
        
        // Mocking SQL statement object
        $this -> mockStmt = $this -> createMock(mysqli_stmt :: class);
        
        // Mock result object 
        $this -> mockResult = $this -> createMock(mysqli_result :: class);
    
        // Sample data for members, stored in database
        $this -> sampleData = 
        [
            ['name' => 'Jane Doe', 'classification' => 'Undergrad'], // [0]
            ['name' => 'John Doe', 'classification' => 'Graduate'], // [1]
            ['name' => 'Jane Smith', 'classification' => 'Undergrad'], // [2]
            ['name' => 'Joe Smith', 'classification' => 'Faculty'], // [3]
            ['name' => 'Jane Kane', 'classification' => 'Undergrad'], // [4]
            ['name' => 'Mark Joe', 'classification' => 'Other'] // [5]
        ];
    
        // Sets up database operations
        $this -> mockConn -> method('prepare') -> willReturn($this -> mockStmt);
        $this -> mockStmt -> method('get_result') -> willReturn($this -> mockResult);
        $this -> mockStmt -> method('bind_param') -> willReturn(true);
        $this -> mockStmt -> method('execute') -> willReturn(true);
    
        // Used to provide database values to the MemberSearcher object
        $this -> mockResult -> method('fetch_assoc') -> willReturnOnConsecutiveCalls($this -> sampleData[0], $this -> sampleData[1], $this -> sampleData[2], $this -> sampleData[3], $this -> sampleData[4], null);
    
        // Instantiates MemberSearcher object with connection to the mySQL database
        $this -> memberSearcher = new MemberSearcher($this -> mockConn);
    }

    // -----------     Search Members Use Cases     ----------
    // Use case test where member name and classification is valid 
    public function testSearchMembers1()
    {
        // Searches with "Jane" as name query and "Undergrad" as classification query
        $members = $this -> memberSearcher -> searchMembers('Jane', 'Undergrad');
    
        // Assert that only 3 events match the criteria 
        $this -> assertCount(3, $members);
        // The 3 matching members are "Jane Doe", "Jane Smith", and "Jane Kane" & they are all also undergrads
        $this -> assertEquals('Jane Doe', $members[0]['name']);
        $this -> assertEquals('Undergrad', $members[0]['classification']);
        $this -> assertEquals('Jane Smith', $members[1]['name']);
        $this -> assertEquals('Undergrad', $members[1]['classification']);
        $this -> assertEquals('Jane Kane', $members[2]['name']);
        $this -> assertEquals('Undergrad', $members[2]['classification']);
    }

    // Use case test where name search query is invalid (too many characters)
    public function testSearchMembers2()
    {
        // 26 characters more than 25 char limit
        $result = $this -> memberSearcher -> searchMembers('11111111111111111111111111', 'Undergrad');
        $this -> assertEquals(['Name string is too long.'], $result);
    }

    // Use case test where name search query is exceptional (escape character)
    public function testSearchMembers3()
    {
        // Escape chracter "\n" is used in the query
        $result = $this -> memberSearcher -> searchMembers("\n", 'Undergrad');
        $this -> assertEquals(['Name string cannot contain escape characters.'], $result);
    }

    // Use case test where classification search query is invalid (Classification doesn't matcb)
    public function testSearchMembers4()
    {
        // Customer is not one of the 4 classifications
        $result = $this -> memberSearcher -> searchMembers("Jane", 'Customer');
        $this -> assertEquals(['Classification string does not match.'], $result);
    }

    // Use case test where classification search query is exceptional (escape character)
    public function testSearchMembers5()
    {
        // Escape chracter "\t" is used in the query
        $result = $this -> memberSearcher -> searchMembers("Jane", "\t");
        $this -> assertEquals(['Classification string cannot contain escape characters.'], $result);
    }
}
// To run: ./vendor/bin/phpunit
?>
 