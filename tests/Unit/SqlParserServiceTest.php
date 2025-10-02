<?php

namespace Tests\Unit;

use App\Services\SqlParserService;
use Tests\TestCase;

class SqlParserServiceTest extends TestCase
{
    private SqlParserService $sqlParser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sqlParser = new SqlParserService();
    }

    public function test_validates_safe_insert_statement(): void
    {
        $sql = "INSERT INTO users (name, email) VALUES ('John Doe', 'john@example.com');";
        
        $result = $this->sqlParser->validateSql($sql);
        
        $this->assertTrue($result['is_valid']);
        $this->assertEmpty($result['errors']);
    }

    public function test_validates_safe_update_statement(): void
    {
        $sql = "UPDATE users SET name = 'Jane Doe' WHERE id = 1;";
        
        $result = $this->sqlParser->validateSql($sql);
        
        $this->assertTrue($result['is_valid']);
        $this->assertEmpty($result['errors']);
    }

    public function test_validates_safe_select_statement(): void
    {
        $sql = "SELECT * FROM users WHERE active = 1;";
        
        $result = $this->sqlParser->validateSql($sql);
        
        $this->assertTrue($result['is_valid']);
        $this->assertEmpty($result['errors']);
    }

    public function test_rejects_dangerous_drop_statement(): void
    {
        $sql = "DROP TABLE users;";
        
        $result = $this->sqlParser->validateSql($sql);
        
        $this->assertFalse($result['is_valid']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('DROP', $result['errors'][0]);
    }

    public function test_rejects_dangerous_delete_statement(): void
    {
        $sql = "DELETE FROM users;";
        
        $result = $this->sqlParser->validateSql($sql);
        
        $this->assertFalse($result['is_valid']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('DELETE', $result['errors'][0]);
    }

    public function test_rejects_dangerous_truncate_statement(): void
    {
        $sql = "TRUNCATE TABLE users;";
        
        $result = $this->sqlParser->validateSql($sql);
        
        $this->assertFalse($result['is_valid']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('TRUNCATE', $result['errors'][0]);
    }

    public function test_parses_multiple_statements(): void
    {
        $sql = "INSERT INTO users (name) VALUES ('John'); INSERT INTO users (name) VALUES ('Jane');";
        
        $statements = $this->sqlParser->parseStatements($sql);
        
        $this->assertCount(2, $statements);
        $this->assertStringContainsString('John', $statements[0]);
        $this->assertStringContainsString('Jane', $statements[1]);
    }

    public function test_handles_empty_sql(): void
    {
        $result = $this->sqlParser->validateSql('');
        
        $this->assertFalse($result['is_valid']);
        $this->assertNotEmpty($result['errors']);
    }

    public function test_handles_sql_with_comments(): void
    {
        $sql = "-- This is a comment\nINSERT INTO users (name) VALUES ('John');";
        
        $result = $this->sqlParser->validateSql($sql);
        
        $this->assertTrue($result['is_valid']);
    }

    public function test_generates_warnings_for_risky_operations(): void
    {
        $sql = "UPDATE users SET password = 'newpass';";
        
        $result = $this->sqlParser->validateSql($sql);
        
        $this->assertTrue($result['is_valid']);
        $this->assertNotEmpty($result['warnings']);
    }
}
