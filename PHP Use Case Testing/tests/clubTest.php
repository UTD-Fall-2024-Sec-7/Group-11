<?php
include_once  '../club.php';
class ClubTest extends \PHPUnit\Framework\TestCase
{

    private $club;
    private $user1;
    private $user2;

    protected function setUp() : void
    {
        $this -> club =  new Club("Programming Club");
        $this -> user1 = new User("John Doe");
        $this -> user2 = new User("Jane Doe");
    }

    public function testAddMember()
    {
        $this -> assertEquals($this -> club  -> addMember(0, ["President", "Officer"]), "Member successfully created.");
        $this -> assertEquals($this -> club  -> addMember(0, ["President", "Officer", "Programming", "Treasurer", "Vice President", "Instructor", "Engineering"]), "Roles must be limited to a maximum of 5.");
        $this -> assertEquals($this -> club  -> addMember(1, "\n"), "Roles must be an array format list.");
        $this -> assertEquals($this -> club  -> addMember(-1, ["President", "Officer"]), "ID cannot be negative.");
        $this -> assertEquals($this -> club  -> addMember("\n", ["President", "Officer"]), "ID must be of integer type.");
    }



}

// ./vendor/bin/phpunit