<?php
class MemberSearcher
{
    private $mysqli;

    // Constant array with the 4 pre-defined classifications
    private const CLASSIFICATION_ARRAY = ["Undergrad", "Graduate", "Faculty", "Other"];

    // Creates a mysql database when MemberSearcher is constructed
    public function __construct(mysqli $mysqli)
    {
        $this -> mysqli = $mysqli;
    }

    // Testcase 4 - Search Members (Name Query)
    public function searchMembers(string $query, string $classification)
    {      
        
        // Name query cannot be empty
        if (strlen($query) == 0) 
        {
            return ['Name string cannot be empty.'];
        }

        // Name query cannot be more than 25 characters
        if(strlen($query) > 25)
        {
            return ['Name string is too long.'];
        }
        // Name query cannot contain escape characters
        if (preg_match('/[\\x00-\\x1F\\x7F]/', $query)) 
        {
            return ['Name string cannot contain escape characters.'];
        }

        // Classification query cannot contain escape characters
        if (preg_match('/[\\x00-\\x1F\\x7F]/', $classification)) 
        {
            return ['Classification string cannot contain escape characters.'];
        }

        // Loops through array of constant CLASSIFICATIONS to see if there is a match
        $matches = false;
        foreach (self :: CLASSIFICATION_ARRAY as $class)
        {
            if(strtolower($class) === strtolower($classification))
                $matches = true;
                break;
        }

        // Classification query must match one of the 4 constant values
        if(!$matches)
            return['Classification string does not match.'];

        // Prepares SQL instruction to select all the rows of the members table
        $stmt = $this -> mysqli -> prepare("SELECT * FROM members");
    
        // Error check to see if query succeeds
        if (!$stmt -> execute()) 
        {
            return ['Database query failed.'];
        }
    
        // Resulting dictionary from query is stored in result
        $result = $stmt -> get_result();
        $members = [];
    
        // Fetches data from each row in the members table
        while ($row = $result -> fetch_assoc()) 
        {
            // Checks if substring of member's name is in the row and that the row's classification matches input
            if (strpos($row['name'], $query) !== false && $row['classification'] === $classification) 
            {
                // Adds the row to the return array
                $members[] = $row;
            }
        }
    
        // If the return array is empty, there are no members that match the criteria
        if (empty($members)) 
        {
            return ['No matching members found.'];
        }
    
        // Returns array with matched members
        return $members;
    }
}
?>
